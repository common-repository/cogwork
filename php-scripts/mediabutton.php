<?php
// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) { exit('Plugins should not be called directly'); }

/* The button for creating shortcode doesn't work in Elementor Pagebuilder */
if(is_admin() && did_action( 'elementor/loaded' )) {
    return;
}

function cwAddMediaButton() {
	require_once(CW_PHP_CLASSES_DIR.'cwMediaButton.php');
	if (cwMediaButton::showMediaButton()) {
		$button = new cwMediaButton();
		$button->addButton();
	}
}
add_action('media_buttons', 'cwAddMediaButton');


function cwDisplayContentTypeOptions() {

	require_once(CW_PHP_CLASSES_DIR.'cwConnector.php');
	$connector = new cwConnector();
	$connector->setContentType('contentTypeOptions');
	$connector->addParam('selectedContentType', $_REQUEST['cwSelectedContentType']);


	echo $connector->getHtmlContent();

	wp_die();
}
add_action( 'wp_ajax_cwAjaxActionContentTypeOptions', 'cwDisplayContentTypeOptions' ); // ajax hook. For logged in users only

?>