<?php
/**
 * Template part: Health Support page section loader.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sections = array( 'hero', 'opening', 'programs', 'inner-health', 'approach', 'daily-practice', 'cta' );

foreach ( $sections as $section ) {
	get_template_part( 'template-parts/pages/health-support/' . $section );
}
