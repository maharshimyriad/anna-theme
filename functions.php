<?php
/**
 * Anna Baylis Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Anna_Baylis
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Require core theme setup classes and files.
 */
require get_template_directory() . '/inc/setup.php';
require get_template_directory() . '/inc/classes/class-theme-enqueue.php';

/**
 * Require helpers.
 */
require get_template_directory() . '/inc/helpers/components.php';

/**
 * Require custom post types.
 */
// require get_template_directory() . '/inc/cpt/testimonials.php';

/**
 * Require custom admin settings.
 */
require get_template_directory() . '/inc/admin/class-theme-settings.php';
require get_template_directory() . '/inc/admin/class-theme-settings-renderer.php';
