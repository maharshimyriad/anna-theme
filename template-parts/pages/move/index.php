<?php
/**
 * Template part: MOVE page section loader.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$move = anna_get_move_page_content();
set_query_var( 'anna_move_page_content', $move );

$sections = array( 'hero', 'evolution', 'was', 'said', 'reviews', 'pillars', 'evolved' );

foreach ( $sections as $section ) {
	get_template_part( 'template-parts/pages/move/' . $section );
}
