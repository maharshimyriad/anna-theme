<?php
/**
 * Move page theme settings fields.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render Move settings tab.
 */
function anna_render_move_page_settings_fields() {
	anna_field_heading( __( 'Move Page Hero', 'anna-baylis' ) );
	anna_field_text( 'move_pg_hero_eyebrow', __( 'Eyebrow', 'anna-baylis' ) );
	anna_field_textarea( 'move_pg_hero_heading', __( 'Heading', 'anna-baylis' ), __( 'One line per row.', 'anna-baylis' ), 3 );
	anna_field_media( 'move_pg_hero_image_id', __( 'Background Image', 'anna-baylis' ) );

	anna_field_heading( __( 'Opening - The Evolution', 'anna-baylis' ) );
	anna_field_text( 'move_pg_evo_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_textarea( 'move_pg_evo_body', __( 'Body', 'anna-baylis' ), '', 8 );
	
	anna_field_heading( __( 'Moments Gallery', 'anna-baylis' ) );
	anna_field_text( 'move_pg_gallery_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_media( 'move_pg_gallery_image_1_id', __( 'Image 1', 'anna-baylis' ) );
	anna_field_media( 'move_pg_gallery_image_2_id', __( 'Image 2', 'anna-baylis' ) );
	anna_field_media( 'move_pg_gallery_image_3_id', __( 'Image 3', 'anna-baylis' ) );
	anna_field_media( 'move_pg_gallery_image_4_id', __( 'Image 4', 'anna-baylis' ) );
	anna_field_media( 'move_pg_gallery_image_5_id', __( 'Image 5', 'anna-baylis' ) );
	
	anna_field_heading( __( 'What M.O.V.E was', 'anna-baylis' ) );
	anna_field_text( 'move_pg_what_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_textarea( 'move_pg_what_body', __( 'Body Text', 'anna-baylis' ), '', 8 );
	
	anna_field_heading( __( 'What Women Said', 'anna-baylis' ) );
	anna_field_textarea( 'move_pg_quote_1', __( 'Quote 1', 'anna-baylis' ) );
	anna_field_textarea( 'move_pg_quote_2', __( 'Quote 2', 'anna-baylis' ) );
	anna_field_textarea( 'move_pg_quote_3', __( 'Quote 3', 'anna-baylis' ) );
	anna_field_textarea( 'move_pg_quote_4', __( 'Quote 4', 'anna-baylis' ) );
	
	anna_field_heading( __( 'Four Pillars', 'anna-baylis' ) );
	anna_field_text( 'move_pg_pillars_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_text( 'move_pg_pillar_1_title', __( 'Pillar 1 Title', 'anna-baylis' ) );
	anna_field_textarea( 'move_pg_pillar_1_body', __( 'Pillar 1 Body', 'anna-baylis' ), '', 5 );
	anna_field_text( 'move_pg_pillar_2_title', __( 'Pillar 2 Title', 'anna-baylis' ) );
	anna_field_textarea( 'move_pg_pillar_2_body', __( 'Pillar 2 Body', 'anna-baylis' ), '', 5 );
	anna_field_text( 'move_pg_pillar_3_title', __( 'Pillar 3 Title', 'anna-baylis' ) );
	anna_field_textarea( 'move_pg_pillar_3_body', __( 'Pillar 3 Body', 'anna-baylis' ), '', 5 );
	anna_field_text( 'move_pg_pillar_4_title', __( 'Pillar 4 Title', 'anna-baylis' ) );
	anna_field_textarea( 'move_pg_pillar_4_body', __( 'Pillar 4 Body', 'anna-baylis' ), '', 5 );

	anna_field_heading( __( 'Call to Action', 'anna-baylis' ) );
	anna_field_text( 'move_pg_cta_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_textarea( 'move_pg_cta_body', __( 'Body Text', 'anna-baylis' ), '', 4 );
	anna_field_text( 'move_pg_cta_btn_1_text', __( 'Button 1 Text', 'anna-baylis' ) );
	anna_field_text( 'move_pg_cta_btn_1_url', __( 'Button 1 URL', 'anna-baylis' ) );
	anna_field_text( 'move_pg_cta_btn_2_text', __( 'Button 2 Text', 'anna-baylis' ) );
	anna_field_text( 'move_pg_cta_btn_2_url', __( 'Button 2 URL', 'anna-baylis' ) );
	anna_field_text( 'move_pg_cta_btn_3_text', __( 'Button 3 Text', 'anna-baylis' ) );
	anna_field_text( 'move_pg_cta_btn_3_url', __( 'Button 3 URL', 'anna-baylis' ) );
}
