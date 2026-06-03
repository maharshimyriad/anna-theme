<?php
/**
 * Plugin Name: Anna Page Scaffolder
 * Description: Scaffold full Anna Baylis theme pages from a slug — templates, sections, CSS, admin fields, and page editor content.
 * Version: 1.0.0
 * Author: Anna Baylis
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ANNA_PAGE_SCAFFOLDER_VERSION', '1.0.0' );
define( 'ANNA_PAGE_SCAFFOLDER_FILE', __FILE__ );
define( 'ANNA_PAGE_SCAFFOLDER_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Bootstrap scaffolder when theme is active.
 */
function anna_page_scaffolder_bootstrap() {
	if ( ! function_exists( 'anna_get_scaffolded_pages' ) ) {
		add_action( 'admin_notices', static function () {
			echo '<div class="notice notice-warning"><p>';
			esc_html_e( 'Anna Page Scaffolder requires the Anna Baylis theme (with page registry).', 'anna-baylis' );
			echo '</p></div>';
		} );
		return;
	}

	if ( ! class_exists( 'Anna_Content_Manager' ) ) {
		add_action( 'admin_notices', static function () {
			echo '<div class="notice notice-warning"><p>';
			esc_html_e( 'Anna Page Scaffolder requires Anna Content Manager for page editor fields.', 'anna-baylis' );
			echo '</p></div>';
		} );
	}

	require_once ANNA_PAGE_SCAFFOLDER_DIR . 'includes/scaffold-sections.php';
	require_once ANNA_PAGE_SCAFFOLDER_DIR . 'includes/class-anna-page-scaffold-generator.php';
	require_once ANNA_PAGE_SCAFFOLDER_DIR . 'includes/class-anna-page-scaffolder-admin.php';

	if ( is_admin() ) {
		Anna_Page_Scaffolder_Admin::instance();
	}
}
add_action( 'after_setup_theme', 'anna_page_scaffolder_bootstrap', 20 );
