<?php
/**
 * Functions and definitions.
 *
 * The master bootstrap file. Loads all modular includes in the correct order.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ─── Theme Constants ─────────────────────────────────────────────────────────
define( 'ANNA_VERSION', '3.1.16' );
define( 'ANNA_DIR', get_template_directory() );
define( 'ANNA_URI', get_template_directory_uri() );
define( 'ANNA_ASSETS', ANNA_URI . '/assets' );
define( 'ANNA_CSS', ANNA_ASSETS . '/css' );
define( 'ANNA_JS', ANNA_ASSETS . '/js' );
define( 'ANNA_IMAGES', ANNA_ASSETS . '/images' );

// ─── Core Setup ───────────────────────────────────────────────────────────────
require_once ANNA_DIR . '/inc/theme-setup.php';
require_once ANNA_DIR . '/inc/enqueue.php';
require_once ANNA_DIR . '/inc/image-sizes.php';
require_once ANNA_DIR . '/inc/helpers.php';
require_once ANNA_DIR . '/inc/template-functions.php';
require_once ANNA_DIR . '/inc/template-tags.php';
require_once ANNA_DIR . '/inc/nav-walkers.php';

// ─── Custom Post Types ────────────────────────────────────────────────────────

// ─── Taxonomies ───────────────────────────────────────────────────────────────

// ─── Meta Boxes ───────────────────────────────────────────────────────────────

// ─── Admin Settings Framework ────────────────────────────────────────────────
require_once ANNA_DIR . '/inc/admin/settings-framework.php';
require_once ANNA_DIR . '/inc/admin/settings-fields.php';
require_once ANNA_DIR . '/inc/admin/settings-sanitize.php';
require_once ANNA_DIR . '/inc/admin/settings-pages.php';
require_once ANNA_DIR . '/inc/admin/settings-css-output.php';
