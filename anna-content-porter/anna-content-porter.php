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
 */
function anna_content_porter_bootstrap() {
	require_once ANNA_PORTER_DIR . 'includes/class-anna-porter-registry.php';
	require_once ANNA_PORTER_DIR . 'includes/class-anna-porter-exporter.php';
	require_once ANNA_PORTER_DIR . 'includes/class-anna-porter-importer.php';
	require_once ANNA_PORTER_DIR . 'includes/class-anna-porter-admin.php';

	add_action( 'plugins_loaded', static function () {
		( new Anna_Porter_Admin() )->init();
	} );
}
anna_content_porter_bootstrap();
