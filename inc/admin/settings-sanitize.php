<?php
/**
 * Admin Settings — Sanitization callbacks
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitize all theme options.
 *
 * @param  array $input Raw input.
 * @return array Sanitized output.
 */
function anna_sanitize_options( $input ) {
	$defaults  = anna_get_default_options();
	$sanitized = array();

	// Color fields.
	$color_fields = array( 'color_primary', 'color_accent', 'color_bg_soft', 'color_text', 'color_heading' );
	foreach ( $color_fields as $key ) {
		$sanitized[ $key ] = isset( $input[ $key ] ) ? sanitize_hex_color( $input[ $key ] ) : $defaults[ $key ];
	}

	// URL fields.
	$url_fields = array( 'header_cta_url', 'cta_primary_url', 'cta_secondary_url', 'services_cta_url', 'about_cta_url', 'privacy_url', 'terms_url' );
	foreach ( $url_fields as $key ) {
		$sanitized[ $key ] = isset( $input[ $key ] ) ? esc_url_raw( $input[ $key ] ) : $defaults[ $key ];
	}

	// HTML-allowed fields (headings with <em> etc).
	$html_fields = array( 'hero_heading', 'about_heading', 'about_body', 'intro_body' );
	foreach ( $html_fields as $key ) {
		$sanitized[ $key ] = isset( $input[ $key ] ) ? wp_kses_post( $input[ $key ] ) : ( $defaults[ $key ] ?? '' );
	}

	// Plain text fields.
	$text_fields = array(
		'font_heading', 'font_body', 'font_size_base', 'font_weight_heading', 'font_weight_body',
		'container_max', 'container_wide', 'section_padding_md',
		'border_radius_btn', 'btn_padding_x', 'btn_padding_y',
		'header_style', 'header_cta_text',
		'hero_eyebrow', 'hero_description', 'hero_trust_text',
		'stat_1_value', 'stat_1_label', 'stat_2_value', 'stat_2_label', 'stat_3_value', 'stat_3_label',
		'intro_eyebrow', 'intro_quote', 'intro_quote_cite',
		'recognition_eyebrow', 'recognition_heading', 'recognition_description',
		'services_eyebrow', 'services_heading', 'services_description', 'services_cta_text',
		'about_eyebrow', 'about_quote', 'about_badge_number', 'about_badge_text', 'about_cta_text',
		'testimonials_eyebrow', 'testimonials_heading',
		'cta_eyebrow', 'cta_heading', 'cta_description', 'cta_trust',
		'cta_primary_text', 'cta_secondary_text',
		'footer_description', 'contact_email', 'contact_phone', 'contact_address',
		'newsletter_text', 'copyright_text',
		'animation_speed',
		'seo_default_title_suffix', 'seo_default_description',
	);
	foreach ( $text_fields as $key ) {
		$sanitized[ $key ] = isset( $input[ $key ] ) ? sanitize_text_field( $input[ $key ] ) : ( $defaults[ $key ] ?? '' );
	}

	// Integer (image IDs).
	$int_fields = array( 'hero_image_id', 'about_image_id', 'seo_og_image_id' );
	foreach ( $int_fields as $key ) {
		$sanitized[ $key ] = isset( $input[ $key ] ) ? absint( $input[ $key ] ) : '';
	}

	// Boolean toggles.
	$bool_fields = array(
		'animations_enabled',
		'section_hero_enabled', 'section_intro_enabled', 'section_recognition_enabled',
		'section_services_enabled', 'section_about_enabled', 'section_testimonials_enabled', 'section_cta_enabled',
	);
	foreach ( $bool_fields as $key ) {
		$sanitized[ $key ] = ! empty( $input[ $key ] ) ? true : false;
	}

	// Social links (sub-array of URLs).
	if ( isset( $input['social_links'] ) && is_array( $input['social_links'] ) ) {
		$sanitized['social_links'] = array();
		foreach ( $input['social_links'] as $platform => $url ) {
			$sanitized['social_links'][ sanitize_key( $platform ) ] = esc_url_raw( $url );
		}
	} else {
		$sanitized['social_links'] = $defaults['social_links'];
	}

	return $sanitized;
}
