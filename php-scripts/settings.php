<?php

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) { echo 'Plugins should not be called directly'; exit; }

// Draw the menu page itself
function cwOptionsPageContent() {
    require_once(CW_PHP_CLASSES_DIR.'cwOptions.php');
    $options = new cwOptions();
    $options->echoOptionsPageContent();
}

// Init plugin options to white list our options
function cogwork_settings_init(){
    register_setting('cogwork_settings_options', 'cogwork_option', 'cwOptionsValidateInput');
}
add_action('admin_init', 'cogwork_settings_init' );

// Add menu page
function cogwork_settings_add_page() {
    add_options_page('CogWork - Settings', 'CogWork', 'manage_options', 'cogwork_settings', 'cwOptionsPageContent');
}
add_action('admin_menu', 'cogwork_settings_add_page');

// Sanitize and validate input. Accepts an array, return a sanitized array.
function cwOptionsValidateInput($input) {
    require_once(CW_PHP_CLASSES_DIR.'cwOptions.php');
    return cwOptions::validateInput($input);
}

// Add a settings link on plugin page
function cwAddPluginActionLinks( $links ) {
    $linksToAdd = array(
        '<a href="' . admin_url( 'options-general.php?page=cogwork_settings' ) . '">'.__('Settings').'</a>',
    );
    return array_merge($linksToAdd, $links);
}
add_filter('plugin_action_links_cogwork/cogwork.php', 'cwAddPluginActionLinks');

?>