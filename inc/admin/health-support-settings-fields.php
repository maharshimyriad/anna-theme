<?php
/**
 * Health Support page theme settings fields.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render Health Support settings tab.
 */
function anna_render_health_support_page_settings_fields() {
	anna_field_heading( __( 'Health Support Hero', 'anna-baylis' ) );
	anna_field_text( 'hs_pg_hero_eyebrow', __( 'Eyebrow', 'anna-baylis' ) );
	anna_field_textarea( 'hs_pg_hero_heading', __( 'Heading', 'anna-baylis' ), __( 'One line per row.', 'anna-baylis' ), 3 );
	anna_field_media( 'hs_pg_hero_image_id', __( 'Background Image', 'anna-baylis' ) );

	anna_field_heading( __( 'Opening', 'anna-baylis' ) );
	anna_field_text( 'hs_pg_opening_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_textarea( 'hs_pg_opening_body', __( 'Body', 'anna-baylis' ), '', 8 );
	anna_field_media( 'hs_pg_opening_image_id', __( 'Image', 'anna-baylis' ) );
	
	anna_field_heading( __( 'Mental Programs', 'anna-baylis' ) );
	anna_field_text( 'hs_pg_programs_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_textarea( 'hs_pg_programs_body', __( 'Body Text', 'anna-baylis' ), '', 8 );
	
	anna_field_heading( __( 'Inner Health', 'anna-baylis' ) );
	anna_field_text( 'hs_pg_inner_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_textarea( 'hs_pg_inner_body', __( 'Body Text', 'anna-baylis' ), '', 8 );
	anna_field_media( 'hs_pg_inner_image_id', __( 'Image', 'anna-baylis' ) );
	
	anna_field_heading( __( 'How I work', 'anna-baylis' ) );
	anna_field_text( 'hs_pg_work_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_textarea( 'hs_pg_work_body', __( 'Body Text', 'anna-baylis' ), '', 8 );

	anna_field_heading( __( 'My daily practice', 'anna-baylis' ) );
	anna_field_text( 'hs_pg_practice_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_textarea( 'hs_pg_practice_body', __( 'Body Text', 'anna-baylis' ), '', 5 );
	anna_field_text( 'hs_pg_practice_link_text', __( 'Link Text', 'anna-baylis' ) );
	anna_field_text( 'hs_pg_practice_link_url', __( 'Link URL', 'anna-baylis' ) );

	anna_field_heading( __( 'Call to Action', 'anna-baylis' ) );
	anna_field_text( 'hs_pg_cta_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_textarea( 'hs_pg_cta_body', __( 'Body Text', 'anna-baylis' ), '', 4 );
	anna_field_text( 'hs_pg_cta_btn_1_text', __( 'Button 1 Text', 'anna-baylis' ) );
	anna_field_text( 'hs_pg_cta_btn_1_url', __( 'Button 1 URL', 'anna-baylis' ) );
	anna_field_text( 'hs_pg_cta_btn_2_text', __( 'Button 2 Text', 'anna-baylis' ) );
	anna_field_text( 'hs_pg_cta_btn_2_url', __( 'Button 2 URL', 'anna-baylis' ) );
	anna_field_text( 'hs_pg_cta_btn_3_text', __( 'Button 3 Text', 'anna-baylis' ) );
	anna_field_text( 'hs_pg_cta_btn_3_url', __( 'Button 3 URL', 'anna-baylis' ) );
}
