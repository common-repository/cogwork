<?php

class cwMediaButton {

	public function __construct() {
		if (!self::showMediaButton()) {
			return;
		}
		$this->addActions();
	}

	/**
	 * @return boolean
	 *
	 * Only show mediabutton tools/content in post/page creation and edit screens
	 */
	public static function showMediaButton() {
		global $pagenow;
		return in_array($pagenow, array( 'post.php', 'page.php', 'post-new.php', 'post-edit.php'));
	}

	public static function addButton() {

		$html = "\n";

		// We must let our javascript know where to look for admin-ajax.php
		$html.= "\n".'<script type="text/javascript">var wpAdminUrl = "'.admin_url('/').'";</script>';

		// The url must be #TB_inline?&inlineId.
		// Writing #TB_inline?inlineId without the extra & will not work.
		// Propably some strange WP-magic.
		$url = '#TB_inline?&inlineId=cwShortCodeSelector&width=750&height=750';
		$html.= "\n".'<a href="'.htmlspecialchars($url).'" id="cwInsertShortcodeMediaButton" class="thickbox button">[cw]</a>';

		// Register and enquing scripts here does not seem to work even if you want it after the rest of the body content.
		// Maybe it works if done earlier but we do not want to load it if not needed so we do it the old fashioned way instead.
		// wp_register_script('cwMediaButton', CW_JS_URL.'cwMediaButton.js', array ('jquery', 'jquery-ui'), false, true);
		// wp_enqueue_script('cwMediaButton');

		$scriptUrl = CW_JS_URL.'cwMediaButton.js';
		$html.= "\n".'<script type="text/javascript" src="'.$scriptUrl.'"></script>';

		$html.= "\n";

		echo $html;
	}

	/**
	 * Hook CogWork Media buttons into WordPress
	 */
	private function addActions() {
		add_action('admin_footer', array( $this, 'admin_footer' ), 11);
	}

	/**
	 * Append the 'Choose CogWork Shortcode' thickbox content to the bottom of selected admin pages
	 */
	public function admin_footer() {

		if (!cwMediaButton::showMediaButton()) {
			return '';
		}

		$html = "\n";

		$html.= "\n".'<div id="cwShortCodeSelector" style="display: none;">';
		$html.= "\n".'        <h2>Select and add shortcode</h2>';

		require_once(CW_PHP_CLASSES_DIR.'cwConnector.php');
		$connector = new cwConnector();
		$connector->setContentType('contentTypes');
		$html.= $connector->getHtmlContent();

		$html.= "\n".'        <br /><br />';
		$html.= "\n"."        <button class='button primary' id='cwSubmitSelectShortcode'>Add shortcode</button>";
		$html.= "\n".'        <br /><br />';

		$html.= "\n".'        <div id="cwShortCodeParametersContainer"></div>';

		$html.= "\n".'</div>'."\n";

		echo $html;
	}
}

?>