<?php
/**
 * Admin Settings — Dynamic CSS Output
 *
 * Generates <style> block in wp_head from admin settings,
 * overriding CSS custom properties from variables.css.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Note: Dynamic CSS output is handled in template-functions.php
// via the anna_dynamic_css_vars() function hooked on wp_head.
// This file is kept as the architectural placeholder.
// For advanced CSS generation (e.g. media queries), extend here.
