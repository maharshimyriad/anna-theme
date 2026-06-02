<?php
/**
 * Template part: Move page section loader.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sections = array( 'hero', 'evolution', 'what-was', 'pillars', 'cta' );

foreach ( $sections as $section ) {
	get_template_part( 'template-parts/pages/move/' . $section );
}
