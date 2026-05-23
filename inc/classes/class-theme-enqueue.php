<?php
/**
 * Enqueue scripts and styles.
 */

class Anna_Theme_Enqueue {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	public function enqueue_assets() {
		$theme_version = wp_get_theme()->get( 'Version' );
		$is_dev = defined('WP_ENVIRONMENT_TYPE') && WP_ENVIRONMENT_TYPE === 'development';

		// We assume Vite is running on port 3000 in dev
		if ( $is_dev && $this->is_vite_running() ) {
			wp_enqueue_script( 'vite-client', 'http://localhost:3000/@vite/client', array(), null, false );
			wp_enqueue_script( 'anna-main-js', 'http://localhost:3000/src/js/main.js', array(), null, array('in_footer' => true, 'strategy' => 'defer') );
		} else {
			// Production
			$css_path = get_template_directory_uri() . '/assets/css/style.css';
			$js_path  = get_template_directory_uri() . '/assets/js/main.js';

			if ( file_exists( get_template_directory() . '/assets/css/style.css' ) ) {
				wp_enqueue_style( 'anna-style', $css_path, array(), filemtime( get_template_directory() . '/assets/css/style.css' ) );
			}
			if ( file_exists( get_template_directory() . '/assets/js/main.js' ) ) {
				wp_enqueue_script( 'anna-main-js', $js_path, array(), filemtime( get_template_directory() . '/assets/js/main.js' ), array('in_footer' => true, 'strategy' => 'defer') );
			}
		}
	}

	private function is_vite_running() {
		$connection = @fsockopen( 'localhost', 3000, $errno, $errstr, 1 );
		if ( is_resource( $connection ) ) {
			fclose( $connection );
			return true;
		}
		return false;
	}
}

new Anna_Theme_Enqueue();

// Allow modules in script tags
add_filter('script_loader_tag', function($tag, $handle, $src) {
	if ( 'vite-client' === $handle || 'anna-main-js' === $handle ) {
		return '<script type="module" src="' . esc_url($src) . '" defer></script>';
	}
	return $tag;
}, 10, 3);
