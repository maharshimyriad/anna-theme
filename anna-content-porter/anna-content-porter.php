<?php
/**
 * Plugin Name: Anna Content Porter
 * Description: Export and import Anna Baylis theme page content (anna_theme_options) between installations. Bundles images as base64 payloads for fully portable JSON packages.
 * Version: 1.0.0
 * Author: Anna Baylis
 * Text Domain: anna-content-porter
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ANNA_PORTER_DIR', plugin_dir_path( __FILE__ ) );
define( 'ANNA_PORTER_URL', plugin_dir_url( __FILE__ ) );

/**
 * Bootstrap the Content Porter.
 *
 * Checks that the Anna Baylis theme is active before loading any class files
 * or hooking into WordPress. If the wrong theme is active an admin notice is
 * displayed and the plugin exits gracefully.
 */
function anna_content_porter_bootstrap() {
	$theme    = wp_get_theme();
	$template = $theme->get_template(); // parent-theme slug for child themes

	// The theme folder is 'annabaylis'; get_template() returns the folder name.
	if ( 'annabaylis' !== $template ) {
		add_action( 'admin_notices', static function () {
			echo '<div class="notice notice-error is-dismissible"><p>';
			esc_html_e( 'Anna Content Porter requires the Anna Baylis theme to be active. The plugin has not been initialised.', 'anna-content-porter' );
			echo '</p></div>';
		} );
		return;
	}

	require_once ANNA_PORTER_DIR . 'includes/class-anna-porter-registry.php';
	require_once ANNA_PORTER_DIR . 'includes/class-anna-porter-exporter.php';
	require_once ANNA_PORTER_DIR . 'includes/class-anna-porter-importer.php';
	require_once ANNA_PORTER_DIR . 'includes/class-anna-porter-admin.php';

	add_action( 'plugins_loaded', static function () {
		( new Anna_Porter_Admin() )->init();
	} );
}
anna_content_porter_bootstrap();
