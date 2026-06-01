<?php
/**
 * Speaking page section loader.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$speaking = anna_get_speaking_page_content();
set_query_var( 'anna_speaking_page_content', $speaking );

$sections = array( 'hero', 'bring', 'topics', 'formats', 'takeaway', 'experience' );

foreach ( $sections as $section ) {
	get_template_part( 'template-parts/pages/speaking/' . $section );
}
