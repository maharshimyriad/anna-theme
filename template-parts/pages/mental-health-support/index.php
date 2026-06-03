<?php
/**
 * Template part: Mental Health Support page section loader.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mhs = anna_get_mhs_page_content();
set_query_var( 'anna_mhs_page_content', $mhs );

$sections = array( 'hero', 'opening', 'mental-programs', 'inner-health', 'how-i-work', 'practice', 'ready' );

foreach ( $sections as $section ) {
	get_template_part( 'template-parts/pages/mental-health-support/' . $section );
}
