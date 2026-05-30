<?php
/**
 * Template part: Coaching page section loader.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$coaching = anna_get_coaching_page_content();
set_query_var( 'anna_coaching_page_content', $coaching );

$sections = array( 'hero', 'work', 'expect', 'faq' );

foreach ( $sections as $section ) {
	get_template_part( 'template-parts/pages/coaching/' . $section );
}
