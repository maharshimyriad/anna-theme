<?php
/**
 * Mental Health Support page theme settings fields.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render Mental Health Support settings tab.
 */
function anna_render_mhs_page_settings_fields() {
	anna_field_heading( __( 'Mental Health Support — Hero', 'anna-baylis' ) );
	anna_field_text( 'mhs_pg_hero_eyebrow', __( 'Eyebrow', 'anna-baylis' ) );
	anna_field_text( 'mhs_pg_hero_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_media( 'mhs_pg_hero_image_id', __( 'Background Image', 'anna-baylis' ) );

	anna_field_heading( __( 'Opening — Your Story as an Athlete', 'anna-baylis' ) );
	anna_field_text( 'mhs_pg_opening_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_textarea( 'mhs_pg_opening_body', __( 'Body', 'anna-baylis' ), __( 'One paragraph per blank line.', 'anna-baylis' ), 10 );
	anna_field_media( 'mhs_pg_opening_image_id', __( 'Portrait Image', 'anna-baylis' ) );

	anna_field_heading( __( 'Mental Programs', 'anna-baylis' ) );
	anna_field_text( 'mhs_pg_programs_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_textarea( 'mhs_pg_programs_body', __( 'Body', 'anna-baylis' ), __( 'Use *word* for emphasis.', 'anna-baylis' ), 10 );

	anna_field_heading( __( 'Inner Health', 'anna-baylis' ) );
	anna_field_text( 'mhs_pg_inner_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_textarea( 'mhs_pg_inner_body', __( 'Body', 'anna-baylis' ), '', 12 );
	anna_field_media( 'mhs_pg_inner_image_id', __( 'Image', 'anna-baylis' ) );

	anna_field_heading( __( 'How I Work', 'anna-baylis' ) );
	anna_field_text( 'mhs_pg_work_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_textarea( 'mhs_pg_work_body', __( 'Body', 'anna-baylis' ), '', 10 );

	anna_field_heading( __( 'My Daily Practice', 'anna-baylis' ) );
	anna_field_text( 'mhs_pg_practice_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_textarea( 'mhs_pg_practice_body', __( 'Body', 'anna-baylis' ), '', 6 );
	anna_field_text( 'mhs_pg_practice_link_text', __( 'Link Text', 'anna-baylis' ) );
	anna_field_text( 'mhs_pg_practice_link_url', __( 'Link URL', 'anna-baylis' ), '', 'url' );

	anna_field_heading( __( 'Ready to Go Deeper (CTA)', 'anna-baylis' ) );
	anna_field_text( 'mhs_pg_ready_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_text( 'mhs_pg_ready_subheading', __( 'Subheading', 'anna-baylis' ) );
	anna_field_textarea( 'mhs_pg_ready_body', __( 'Body', 'anna-baylis' ), '', 3 );
	anna_field_text( 'mhs_pg_ready_button_primary_text', __( 'Primary Button Text', 'anna-baylis' ) );
	anna_field_text( 'mhs_pg_ready_button_primary_url', __( 'Primary Button URL', 'anna-baylis' ), '', 'url' );
	anna_field_text( 'mhs_pg_ready_button_secondary_text', __( 'Secondary Button Text', 'anna-baylis' ) );
	anna_field_text( 'mhs_pg_ready_button_secondary_url', __( 'Secondary Button URL', 'anna-baylis' ), '', 'url' );
	anna_field_text( 'mhs_pg_ready_button_tertiary_text', __( 'Tertiary Button Text', 'anna-baylis' ) );
	anna_field_text( 'mhs_pg_ready_button_tertiary_url', __( 'Tertiary Button URL', 'anna-baylis' ), '', 'url' );
}
