<?php
/**
 * Anna Theme functions and definitions
 *
 * @package anna-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define theme constants.
define( 'ANNA_THEME_VERSION', '1.0.0' );
define( 'ANNA_THEME_DIR', trailingslashit( get_template_directory() ) );
define( 'ANNA_THEME_URI', trailingslashit( get_template_directory_uri() ) );

// Require theme includes.
require_once ANNA_THEME_DIR . 'inc/setup.php';
require_once ANNA_THEME_DIR . 'inc/enqueue.php';
require_once ANNA_THEME_DIR . 'inc/helpers.php';
require_once ANNA_THEME_DIR . 'inc/template-tags.php';
