<?php
/**
 * Reviews page: section loader.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reviews_content = anna_get_reviews_page_content();
set_query_var( 'anna_reviews_page_content', $reviews_content );

$sections = array( 'hero', 'grid', 'cta' );

foreach ( $sections as $section ) {
	get_template_part( 'template-parts/pages/reviews/' . $section );
}
