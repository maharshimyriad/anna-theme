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

define( 'ANNA_PORTER_DIR', get_template_directory() . '/anna-content-porter/' );
define( 'ANNA_PORTER_URL', get_template_directory_uri() . '/anna-content-porter/' );

/**
 * Bootstrap the Content Porter.
 *
 * When loaded via functions.php (inside the theme), plugins_loaded has already
 * fired, so we initialise directly rather than hooking into it.
 */
function anna_content_porter_bootstrap() {
	require_once ANNA_PORTER_DIR . 'includes/class-anna-porter-registry.php';
	require_once ANNA_PORTER_DIR . 'includes/class-anna-porter-exporter.php';
	require_once ANNA_PORTER_DIR . 'includes/class-anna-porter-importer.php';
	require_once ANNA_PORTER_DIR . 'includes/class-anna-porter-admin.php';

	( new Anna_Porter_Admin() )->init();
}
anna_content_porter_bootstrap();
