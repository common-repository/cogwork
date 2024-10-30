<?php
/*
 Plugin Name: CogWork
 Plugin URI: https://minaaktiviteter.se/help/api/wordpress/
 Description: Enables shortcodes that loads and displays content from CogWork (MinaAktiviteter/Dans.se/Idrott.se)
 Author: CogWork AB
 Text Domain: cogwork
 Version: 1.8.2
 Author URI: https://minaaktiviteter.se/cogwork/
 */

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) { exit('Plugins should not be called directly'); }

if (!defined('CW_PHP_NAMESPACE'  )) { define('CW_PHP_NAMESPACE'  , 'CW'); }
if (!defined('CW_DIR'            )) { define('CW_DIR'            , plugin_dir_path(__FILE__)); }
if (!defined('CW_PHP_CLASSES_DIR')) { define('CW_PHP_CLASSES_DIR', CW_DIR.'php-classes/'); }
if (!defined('CW_PHP_SCRIPTS_DIR')) { define('CW_PHP_SCRIPTS_DIR', CW_DIR.'php-scripts/'); }
if (!defined('CW_JS_URL'         )) { define('CW_JS_URL'         , plugins_url('js/', __FILE__)); }

load_plugin_textdomain('cogwork', false, basename(dirname(__FILE__)).'/languages');


// core plugin functionality
require_once(CW_PHP_CLASSES_DIR.'cwCore.php');
cwCore::init();


//Settings for the plugin
require_once(CW_PHP_SCRIPTS_DIR.'settings.php');
//Button for selecting ShortCode in Classic editor
require_once(CW_PHP_SCRIPTS_DIR.'mediabutton.php');
//Create the shortcodes used by the plugin
require_once(CW_PHP_SCRIPTS_DIR.'shortcodes.php');
//Add a Gutenberg block to the new WordPress edtior
require_once(CW_PHP_SCRIPTS_DIR.'gutenbergblock.php');
//Add options to log in with Mina Aktiviteter
require_once(CW_PHP_SCRIPTS_DIR.'login.php');

?>