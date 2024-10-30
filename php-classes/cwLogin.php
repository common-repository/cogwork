<?php

/*
 * Plugin Name: CogWorkAdmin
 * Plugin URI: https:/cogwork.se
 * Description: Show PHP then logged in to site.
 * Version: 1.0
 * Author: Cogwork
 * Author URI: https:/cogwork.se
 */

class cwLogin
{
  public function __construct()
  {
  }

  // Define accepted user roles in WordPress
  private function cwCheckRole($role)
  {
    $result = false;
    $userRoles =  array('cwuser', 'subscriber', 'contributor', 'author', 'editor', 'administrator');

    if ($role && in_array($role, $userRoles)) {
      $result = true;
    }

    return $result;
  }

  private static function addUserRole($role) {
    if($role === "cwuser") {
       add_role("cwuser","cwUser", ["read" => "true"] );
    }
  }

  public function cwAuthorization($user, $username, $password)
  {
    $username = sanitize_text_field($username);
    $password = sanitize_text_field($password);

    // Check if username and password is recived from login attempt
    if ($username === '' || $password === '') return "";

    $user = '';
    $domain = cwCore::cwDomainName();
    $failedLogin = 'Inloggning via ' . $domain . ' misslyckades: ';

    // If exist get Wordpress user with the same login name as the inputed username
    $userRetrivied = get_user_by('login', $username);

    // If user wih that username exist 
    if ($userRetrivied) {
       
      // Get cwKey for user if it exist
      $userCwKey = get_the_author_meta('cwKey', $userRetrivied->ID);


      if ($userCwKey) {
        // Disable login with WordPress credential if user has a cwKey and thereby should login through Mina Aktiviteter.
        remove_action('authenticate', 'wp_authenticate_username_password', 20);     

        $cwConnector = new cwConnector();

        // Login to Mina Aktiviteter
        $ext_auth =  $cwConnector->cwLogin($username, $password);

        // Check if and retun error
      if ($ext_auth['error']) {     
          $user = new WP_Error('denied', $failedLogin . $ext_auth['error']);
      }     

        // Return error if no WordPress role is selected
        elseif (!$this->cwCheckRole($ext_auth['role'])) {
          $user = new WP_Error('denied', $domain. ' behörighet har tagits bort för användaren ' . $username . '.');
          $userRetrivied->set_role('');
        }

         // Return error message if cwKey doesn't match the key saved in the database
         elseif ($ext_auth['cwKey'] != $userCwKey) {
          $user = new WP_Error('denied', 'Felaktiga användardata hämtad för användaren från ' . $domain);
        }

        // Check if login method should be permantly changed to WordPress login
        // Function deactivated might be added later
        /*
          elseif(array_key_exists('wordpress', $ext_auth)) {
            delete_user_meta($userRetrivied->ID, 'cwKey');
            $wordpressuser =  $userRetrivied;
            $wordpressuser->set_role($ext_auth['role']);
            $user = new WP_Error( 'denied', __('Inloggningsmetod har bytts från ' . $domain . ' till WordPress. Klickas på "Glömt ditt lösenord?" nedan om du vill ha nytt lösenord till ') . $userRetrivied->user_email . ' .' );
          }
           */
        // Return user that should login in to WordPress
        else {
          $user =  $userRetrivied;
          // Update role
          self::addUserRole($ext_auth['role']);
          $user->set_role($ext_auth['role']);
          // Update email
          if ($ext_auth['email'] && $ext_auth['email']) {
            wp_update_user(['ID' => $user->ID, 'user_email' => $ext_auth['email']]);
          }
        }
      }
    }
    // Check if the usernamne isa an email and if any WorPress user have the same email as the username
    elseif (strpos($username, "@") && email_exists($username)) {


      $userRetriviedFromEmail = get_user_by('email', $username);
      // Display error that login should only be with username if Mina Aktiviteter user
      if (get_the_author_meta('cwKey', $userRetriviedFromEmail->ID)) {
        remove_action('authenticate', 'wp_authenticate_email_password', 20);
        remove_action('authenticate', 'wp_authenticate_username_password', 20);

        return new WP_Error('denied', __("Logga in med ditt användarnamn i " . $domain . " istället för din e-postadress"));
      }
    }
    // Else check if new user should be created
    else {

      $cwConnector = new cwConnector();
      $ext_auth =  $cwConnector->cwLogin($username, $password);

      // If login to Mina Aktiviteter succeded and role exist
      if (!$ext_auth['error'] && $ext_auth['cwKey'] && $this->cwCheckRole($ext_auth['role'])) {
        //Remove WordPress standard login
        remove_action('authenticate', 'wp_authenticate_username_password', 20);

        // Check if any user have the same cwKey
        if (get_users(['meta_key' => "cwKey", 'meta_value' => $ext_auth['cwKey'], 'number' => 1])) {

          return new WP_Error('denied', __($failedLogin . "Felaktiga användardata hämtad för användaren"));
        }




        // E-mail adresses needed to create new user
        if (!$ext_auth['email']) {
          return new WP_Error('denied', __($failedLogin . " E-postadressen saknas i " . $domain . "."));
        } else {


          // E-mail adress needs to be unique in WordPress so add Duplikat to email adress.
          if (email_exists($ext_auth['email'])) {
            $ext_auth['email'] = "duplikat.1." . $ext_auth['email'];
            /* Alternative solution display error */
            /*
                return new WP_Error( 'denied', __("CW Inlogg: Användare gick inte att skapa då e-postadressen redan används av annan användare") );
                */
          }

          $userdata = array(
            'user_email' => $ext_auth['email'],
            'user_login' => $username,

          );
          $new_user_id = wp_insert_user($userdata); // A new user has been created
          update_user_meta($new_user_id, 'cwKey', $ext_auth['cwKey']);
          // Load the new user info
          $user = new WP_User($new_user_id);
          self::addUserRole($ext_auth['role']);
          $user->set_role($ext_auth['role']);
        }
      }
    }

    return $user;
  }
}
