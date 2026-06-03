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

$oasis = anna_get_oasis_page_content();
set_query_var( 'anna_mhs_page_content', $oasis );

$sections = array( 'hero', 'opening', 'mental-programs', 'inner-health', 'how-i-work','practice' );

foreach ( $sections as $section ) {
	get_template_part( 'template-parts/pages/mental-health-support/' . $section );
}
