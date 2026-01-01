<?php
/**
 * Demo Helper Functions
 * 
 * Zentrale Helper-Funktionen für Demo-Ansichten
 * 
 * @package ChurchTools_Suite
 * @since   0.9.4.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Embed public frontend CSS inline for demo preview
 * 
 * Ensures that frontend CSS is available in admin demo preview area
 * regardless of enqueue order or iframe isolation.
 * 
 * @since 0.9.4.2
 * @return void
 */
function cts_demo_embed_public_css(): void {
	$css_file = CHURCHTOOLS_SUITE_PATH . 'assets/css/churchtools-suite-public.css';
	
	if ( ! file_exists( $css_file ) ) {
		return;
	}
	
	$css_content = file_get_contents( $css_file );
	
	if ( $css_content === false ) {
		return;
	}
	
	echo '<style id="cts-demo-inline-public-css">' . $css_content . '</style>';
}
