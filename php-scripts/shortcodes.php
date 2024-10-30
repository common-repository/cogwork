<?php
// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) { exit('Plugins should not be called directly'); }
/* 
 * Preview of shortcode doesn't work in Elementor. Leads to page not able to save 
 */
if (is_admin() && did_action( 'elementor/loaded' )) {
    return;
}

/**
 * @param string[] $attributes
 * @param string $content
 * @param string $tag
 * @return string
 */
function cwProcessShortCode($attributes, $content, $tag)
{
    require_once (CW_PHP_CLASSES_DIR . 'cwShortCodeProcessor.php');
    return cwShortCodeProcessor::process($attributes, $content, $tag);
}

// Add all supported shortcodes
add_shortcode('cw', 'cwProcessShortCode');
add_shortcode('cwLink', 'cwProcessShortCode');
add_shortcode('cwChildPages', 'cwProcessShortCode');
add_shortcode('cwService', 'cwProcessShortCode');

// Only for backward compability with old installations
add_shortcode('cwShop', 'cwProcessShortCode');
add_shortcode('cwToc', 'cwProcessShortCode');

?>