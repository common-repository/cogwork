<?php

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
  exit('Plugins should not be called directly');
}

require_once(CW_PHP_CLASSES_DIR . 'cwLogin.php');

/* Add Mina Aktiviter login and also customize user information */

$option = get_option('cogwork_option');

/* Add action and filters if Mina Aktiviter / CogWork login activated */
if (is_array($option)  && array_key_exists('loginMethod', $option) && $option['loginMethod'] == 'cogwork') {
  add_action('show_user_profile', 'cwLoginMethod');
  add_action('edit_user_profile', 'cwLoginMethod');
  add_filter('authenticate', 'cwLogin', 10, 3);
  
}

/* Add action and filters if Mina Aktiviter users exist */
$args = array(
  'meta_key'     => 'cwKey',
);

$users = get_users($args);

if ($users) {
  add_filter('manage_users_columns', 'cwAddLoginColum');
  add_filter('manage_users_custom_column', 'cwAddLoginColumData', 10, 3);
}

function cwLogin($user, $username, $password)
{

  $cwLogin = new cwLogin;
  return $cwLogin->cwAuthorization($user, $username, $password);
}

function cwLoginMethod($user)
{
  $domainName = cwCore::cwDomainName();
?>

  <h2><?php _e('Login method','cogwork') . $domainName ?></h2>
  <p>
    <?php

    // Get correct domain

    // Information about login method.      
    if (get_the_author_meta('cwKey', $user->ID)) {
        echo '<p><strong>' . $domainName . '</strong></p>';
    }
    else {
      echo '<p><strong>WordPress</strong></p>';
    }
    ?>
  </p>
  <?php if (get_the_author_meta('cwKey', $user->ID))
  // Add JavaScript code to hide login field if the user login through Mina Aktiviteter
  { ?>
    <script>
      jQuery('#password, #email-description, .user-generate-reset-link-wrap, #application-passwords-section').hide();
      jQuery('#email, #role').prop('readonly', true);
      jQuery('#role option:not(:selected)').attr('disabled', true);
    </script>
<?php }
}




/* Create   */


function cwAddLoginColum($columns)
{

  // unset( $columns['posts'] ); // maybe you would like to remove default columns
  $columns['loginMethod'] = __('Login method','cogwork'); // add new

  return $columns;
}

/* Add data to user login method column */



function cwAddLoginColumData($row_output, $column_id_attr, $user)
{


  if ($column_id_attr == 'loginMethod') {

    if (get_the_author_meta('cwKey', $user)) {

      $row_output  = cwCore::cwDomainName();
    } else {
      $row_output = "WordPress";
    }
  }


  return $row_output;
}




?>