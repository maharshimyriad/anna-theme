<?php
/**
 * Contact page: section loader.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$contact = anna_get_contact_page_content();
set_query_var( 'anna_contact_page_content', $contact );

$sections = array( 'hero', 'body' );

foreach ( $sections as $section ) {
	get_template_part( 'template-parts/pages/contact/' . $section );
}
