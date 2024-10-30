<?php
// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) { exit('Plugins should not be called directly'); }

// use the cwConnector.php script to get data for contenType and ContenTypeoptions
require_once (CW_PHP_CLASSES_DIR . 'cwConnector.php');

/* Needed to for creating gutenberg plugin */
defined('ABSPATH') || exit();

// Function to enque script block.js
function cw_gutenberg_block_enqueue()
{
    // Get the html selectbox with contentTypes from server side.
    $connector = new cwConnector();
    $connector->setContentType('contentTypes');
    $html = $connector->getHtmlContent();

    /*
     * Register the script block.build.js. The code for the Gutenberg block is created in block.js
     * and then you use Webpack to compile the data to the file block.build.js that is run by Wordpress.
     * How to install Wepback https://modularwp.com/how-to-build-gutenberg-blocks-jsx/
     */

    wp_enqueue_script('cw_gutenberg_script', // Unique handle.
    CW_JS_URL . 'block.build.js', // block.js: We register the block here.
    array(
        'wp-blocks',
        'wp-i18n',
        'wp-element'
    ) // Dependencies, defined above.
    );

    /*
     * creating the variabel cw_script_vars.htmlcontent that is a global variable that can
     * also be used in block.js with the html selectbox with contentTypes
     */
    wp_localize_script('cw_gutenberg_script', 'cw_script_vars', array(
        'htmlcontent' => __($html, 'cogwork')
    ));

    wp_localize_script('cw_gutenberg_script', 'cwgutenbergoption_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}

// Enque the block.js script with the variabel cw_script_vars.htmlcontent
add_action('enqueue_block_editor_assets', 'cw_gutenberg_block_enqueue');

// Function to create ajax call to get the contentypesoptions dependent on contentype
function cw_gutenberg_options()
{
    $connector2 = new cwConnector();
    $connector2->setContentType('contentTypeOptions');
    $connector2->addParam('selectedContentType', $_POST['inputData']);

    echo $connector2->getHtmlContent();

    wp_die();
}

// Create the action for the ajax call
add_action('wp_ajax_cw_gutenberg_options', 'cw_gutenberg_options');

?>