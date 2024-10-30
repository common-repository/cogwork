<?php

class cwOptions
{

	public function __construct()
	{
	}
	public function echoOptionsPageContent()
	{
		$dboptions = get_option('cogwork_option');
		// Create kes and set value to null if the key not used so not needed to use array_key_exists every time key in options should be used.
		$optionKeyList = ["orgCode", "websiteCode", "protocol", "apiToken", "loginMethod", "apiKey"];

		$options = [];

		if (is_array($dboptions)) {
			$options = $dboptions;
			foreach ($optionKeyList as $optionkey) {
				if (!array_key_exists($optionkey, $options)) {
					$options[$optionkey] = "";
				}
			}
		} else {
			foreach ($optionKeyList as $optionkey) {
				$options[$optionkey] = "";
			}
		}

		$cwDomainName = cwCore::cwDomainName();
?>
		<div id="cwWrap" class="wrap" style="max-width: 1000px">
			<div style="width: 60%;  float: left; padding-right: 20px">
				<h2>CogWork Plugin Settings</h2>
				<?php 
					if(!$options['orgCode']) {
					echo "<h3>" . __('Enter organisation code to start using the plugin', 'cogwork') . "</h3>";	
					}	
				?>			
				<form method="post" action="options.php">

					<?php settings_fields('cogwork_settings_options'); ?>

					<table class="form-table">

						<tr valign="top">
							<th scope="row"><?php _e('Organisation code', 'cogwork'); ?></th> 
							<td><input type="text" name="cogwork_option[orgCode]" value="<?php echo $options['orgCode']; ?>" /></td>
						</tr>

						<tr valign="top">
							<th scope="row"><?php _e('Website or server', 'cogwork'); ?></th>
							<td>
								<select name="cogwork_option[websiteCode]">
									<option value="minaaktiviteter" <?php if ($options['websiteCode'] == 'minaaktiviteter') echo 'selected="selected"'; ?>>MinaAktiviteter.se</option>
									<option value="dans" <?php if ($options['websiteCode'] == 'dans') echo 'selected="selected"'; ?>>Dans.se</option>
									<option value="idrott" <?php if ($options['websiteCode'] == 'idrott') echo 'selected="selected"'; ?>>Idrott.se</option>
									<option value="test" <?php if ($options['websiteCode'] == 'test') echo 'selected="selected"'; ?>>Test</option>
									<option value="local" <?php if ($options['websiteCode'] == 'local') echo 'selected="selected"'; ?>>Localhost</option>
								</select>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><?php _e('Protocol', 'cogwork'); ?></th>
							<td>
								<select name="cogwork_option[protocol]">
									<option value="https" <?php if ($options['protocol'] == 'https') echo 'selected="selected"'; ?>>https (secure)</option>
									<option value="http" <?php if ($options['protocol'] == 'http') echo 'selected="selected"'; ?>>http</option>
								</select>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><?php _e('Server password', 'cogwork') ?> *</th>
							<td><input id="apiToken" type="text" name="cogwork_option[apiToken]"
							 <?php
								if ($options['apiToken'] || $options['apiKey']) {

									echo 'placeholder="' . __('Server password is saved', 'cogwork') . '"';
								}
							?> />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"> <?php echo $cwDomainName . " - " .  __('login', 'cogwork'); ?> </th>
							<td>
								<select id="loginMethod" name="cogwork_option[loginMethod]">
									<option value='wordpress' <?php if ($options['loginMethod'] == 'wordpress') echo 'selected="selected"'; ?>>Nej</option>
									<option value='cogwork' <?php if ($options['loginMethod'] == 'cogwork') echo 'selected="selected"'; ?>>Ja</option>
								</select>
							</td>
						</tr>
					</table>
					<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cogwork'); ?>" /></p>
				</form>
				<p>* <?php _e('Activity booking and other types of content can be retrieved from Mina Aktiviteter without adding the server password. Server password is needed for activating Mina Aktiviteter login and retrieve private data', 'cogwork'); ?> </p>

			</div>
			<div style="width: 35%; float: left;">
				<?php
				echo $this->getCwUsers($options['loginMethod'], $cwDomainName);
				?>

			</div>		
			<h2 style="clear: both"> <?php _e('Usage', 'cogwork'); ?></h2>
			<p><?php _e('Activity booking, membership button, calendar list and other content from Mina Aktiviteter can be added to this website with the CogWork block in the WordPress Gutenberg Editor.', 'cogwork'); ?>
			<p><?php _e('Shortcodes can also be directly added to a webpage if another editor is used on this website. For example [cw shop eventGroup="lindyhop"].', 'cogwork'); ?>
			<p>
			<h3><?php echo __('Login through ', 'cogwork') . $cwDomainName; ?></h3>

			<p><?php _e('User can now log in to this website with their Mina Aktiviteter login credentials.', 'cogwork'); ?></p> 
			<!-- Mer information finns i handledning -->
			<p><a href="https://cogwork.se/handledning/integrera/wordpress/" target="_blank"> <?php _e('More information is in the tutorial', 'cogwork'); ?> </a></p>
			<h3><?php echo __('Test getting content from ', 'cogwork') . " " . $cwDomainName; ?></h3>
			<!-- Check if connection to Mina Aktiviter can be established -->
			<p><?php $this->testLoadContent($options['websiteCode']);
			// Check if the organization code is correct and that their are visible events by also getting webbshop 
		
			  if($options['orgCode']) {
				echo do_shortcode('[cwShop]'); 
			  }
	
			?>
		</div>

	   <!--  Replace the text all occurance of Mina AKtiviteter in p element if other domain is used -->
		<script>
			window.addEventListener("load", (event) => {
			
				var element = document.getElementsByTagName("p");

				for (var i = 0; i < element.length; i++) {

					element[i].innerHTML = element[i].innerHTML.replaceAll('Mina Aktiviteter', <?php echo  "'" .  $cwDomainName . "'" ?>);
				}

			});
		</script>


<?php      
	   
	}

	/**
	 * @param array $input
	 * @return array
	 *
	 * Sanitize and validate input.
	 * Accepts an array, return a sanitized array
	 */
	public static function validateInput($input)
	{
		$options = get_option('cogwork_option');
		// Remove any HTML tags

		// $input['orgCode'] =  sanitize_text_field($input['orgCode']);

		$input['websiteCode'] = sanitize_text_field($input['websiteCode']);
		$input['apiToken'] = sanitize_text_field($input['apiToken']);

		$tokenNotSet = true;

		if (!$input['apiToken']) {

			// In that case check and set to existing apiToken value
			if ($options['apiToken']) {
				$input['apiToken'] = $options['apiToken'];
				$tokenNotSet = false;
			}
			// Else check compability with earlier version of the plugin and set apiToken to unencrypted apiKey value
			elseif ($options['apiKey']) {
				$cwResources = new cwResources();
				$input['apiToken'] = $cwResources->encrypt($options['apiKey'], 'e');		
				$tokenNotSet = false;
			}
		} else {
			// Set option apiToken if input value exists
			$cwResources = new cwResources();
			$input['apiToken'] = $cwResources->encrypt($input['apiToken'], 'e');
			$tokenNotSet = false;
		}
		/* Deactive CogWork / Mina Aktiviteter login if APiKey / ApiToken not set */

		if ($tokenNotSet || !$input['orgCode']) {
			$input['loginMethod'] = "wordpress";
		}
  
		return $input;
	}


	/* Show all CogWork / Mina Aktiviteter users */
	private function getCwUsers($loginMethod, $cwUserdDomain)

	{

		$html = "";
		$args = array(
			'role'    => 'administrator',
			'meta_key'     => 'cwKey',
			'fields'       => 'user_login'					
		);

		$users = get_users($args);
	

		if(!empty($users) && is_array($users)) {	
			$userString = (implode(", ", $users));			
			$html = '<h3 >';
			$html .= $cwUserdDomain;
			$html .= ' ';
			$html .=  __('users', 'cogwork');
			$html .= '</h3>';				
			$html .= '<p><strong>';
			$html .= __('Administrators', 'cogwork');
			$html .= '</strong><br>';
			$html .= $userString;
			$html .= '</p>';		
			$html .= '<p>';						
			$html .= '<a href="'. home_url() . '/wp-admin/users.php">';
			$html .=  __('All Mina Aktiviteter-users', 'cogwork');
			$html .= '</a>';
			$html .= '</p>';

			if ($loginMethod == 'cogwork') {
				$html .= "<p class='cwMa'>";
				$html .=  __('Mina Aktiviteter-login enabled', 'cogwork');
				$html .= "<p>";
			}			
			
			
		
			/* Inform about CogWork / Mina Aktiviter users then the CogWork login is not active. */
			else {
				$html .= "<p class='cwMa'>";
				$html .=  __('Mina Aktiviteter login not enabled. Mina Aktiviteter users can login as WordPresss users if they get new password. Their accounts can be deleted if they no longer should be able to login to WordPress', 'cogwork');
				$html .= "</p>";
			}

		

		}

		return $html;
	
	}

	private function testLoadContent($websiteCode)
	{
       	// Check if connection to Mina Aktiviteter can be establisged */

		$result = "<p>";
		$response = wp_remote_get("https://minaaktiviteter.se/api/json/?org=demoklubben&contentType=shop");
	
	
		if (is_wp_error($response)) {
			$result = __('Connection to Mina Aktiviteter failed','cogwork');
			$result .= "<br" . $response->get_error_message();
		} elseif (wp_remote_retrieve_response_code($response) === 200) {
						$result = __('Connection established with Mina Aktiviteter. The shop with activities is displaced below. Check if the organisation code is correct and that there are visible activities in Mina Aktiviteter, if the shop is not displayed below.', 'cogwork'); //Mårten 2023-10-06
		} else {
			$result = __('Connection to Mina Aktiviteter failed','cogwork');
		}
        $result .= "</p>";
		echo $result;
	}


}

?>