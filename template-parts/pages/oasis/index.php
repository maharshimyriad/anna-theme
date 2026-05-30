<?php
/**
 * Template part: Oasis page section loader.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$oasis = anna_get_oasis_page_content();
set_query_var( 'anna_oasis_page_content', $oasis );

$sections = array( 'hero', 'what', 'ready', 'inside', 'how', 'choose', 'begun', 'waitlist', 'faq' );

foreach ( $sections as $section ) {
	get_template_part( 'template-parts/pages/oasis/' . $section );
}
