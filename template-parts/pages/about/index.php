<?php
/**
 * Template part: About page section loader.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$about = anna_get_about_page_content();
set_query_var( 'anna_about_page_content', $about );

$sections = array( 'hero', 'story', 'coach', 'work', 'qualifications', 'life' );

foreach ( $sections as $section ) {
	get_template_part( 'template-parts/pages/about/' . $section );
}
