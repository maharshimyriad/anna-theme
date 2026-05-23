<?php
/**
 * Admin Settings — Sanitization callbacks
 *
 * IMPORTANT: Because the settings panel uses tabs, only one tab's
 * fields are submitted at a time. We must MERGE the incoming data
 * with the previously-saved options so other tabs are preserved.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Map each settings tab to the option keys it contains.
 *
 * @return array Tab slug => array of option keys.
 */
function anna_get_tab_fields_map() {
	return array(
		'brand' => array(
			'color_primary', 'color_accent', 'color_bg_soft', 'color_text', 'color_heading',
		),
		'typography' => array(
			'font_heading', 'font_body', 'font_size_base', 'font_weight_heading', 'font_weight_body',
		),
		'layout' => array(
			'container_max', 'container_wide', 'section_padding_md', 'border_radius_btn',
		),
		'header' => array(
			'header_style', 'header_cta_text', 'header_cta_url',
		),
		'hero' => array(
			'hero_eyebrow', 'hero_heading', 'hero_description', 'hero_trust_text', 'hero_image_id',
			'stat_1_value', 'stat_1_label', 'stat_2_value', 'stat_2_label', 'stat_3_value', 'stat_3_label',
		),
		'sections' => array(
			'section_hero_enabled', 'section_intro_enabled', 'section_recognition_enabled',
			'section_services_enabled', 'section_about_enabled', 'section_testimonials_enabled', 'section_cta_enabled',
		),
		'content' => array(
			'intro_eyebrow', 'intro_quote', 'intro_quote_cite', 'intro_image_id',
			'recognition_eyebrow', 'recognition_heading', 'recognition_description', 'recognition_image_id',
			'services_eyebrow', 'services_heading', 'services_description', 'services_cta_text', 'services_cta_url',
			'about_eyebrow', 'about_heading', 'about_body', 'about_image_id',
			'about_badge_number', 'about_badge_text', 'about_quote', 'about_cta_text', 'about_cta_url',
			'testimonials_eyebrow', 'testimonials_heading',
		),
		'cta' => array(
			'cta_eyebrow', 'cta_heading', 'cta_description', 'cta_trust', 'cta_image_id',
			'cta_primary_text', 'cta_primary_url', 'cta_secondary_text', 'cta_secondary_url',
		),
		'footer' => array(
			'footer_description', 'contact_email', 'contact_phone', 'contact_address',
			'newsletter_text', 'copyright_text', 'privacy_url', 'terms_url',
		),
		'social' => array(
			'social_links',
		),
		'animations' => array(
			'animations_enabled', 'animation_speed',
		),
		'seo' => array(
			'seo_default_title_suffix', 'seo_default_description', 'seo_og_image_id',
		),
	);
}

/**
 * Sanitize a single option value based on its key.
 *
 * @param  string $key   Option key.
 * @param  mixed  $value Raw value.
 * @return mixed Sanitized value.
 */
function anna_sanitize_single_option( $key, $value ) {
	// Color fields.
	$color_fields = array( 'color_primary', 'color_accent', 'color_bg_soft', 'color_text', 'color_heading' );
	if ( in_array( $key, $color_fields, true ) ) {
		return sanitize_hex_color( $value );
	}

	// URL fields.
	$url_fields = array( 'header_cta_url', 'cta_primary_url', 'cta_secondary_url', 'services_cta_url', 'about_cta_url', 'privacy_url', 'terms_url' );
	if ( in_array( $key, $url_fields, true ) ) {
		return esc_url_raw( $value );
	}

	// HTML-allowed fields.
	$html_fields = array( 'hero_heading', 'about_heading', 'about_body', 'intro_body' );
	if ( in_array( $key, $html_fields, true ) ) {
		return wp_kses_post( $value );
	}

	// Integer / image ID fields.
	$int_fields = array( 'hero_image_id', 'about_image_id', 'intro_image_id', 'recognition_image_id', 'cta_image_id', 'seo_og_image_id' );
	if ( in_array( $key, $int_fields, true ) ) {
		return absint( $value );
	}

	// Boolean toggle fields.
	$bool_fields = array(
		'animations_enabled',
		'section_hero_enabled', 'section_intro_enabled', 'section_recognition_enabled',
		'section_services_enabled', 'section_about_enabled', 'section_testimonials_enabled', 'section_cta_enabled',
	);
	if ( in_array( $key, $bool_fields, true ) ) {
		return ! empty( $value ) ? true : false;
	}

	// Social links sub-array.
	if ( 'social_links' === $key ) {
		if ( is_array( $value ) ) {
			$sanitized_links = array();
			foreach ( $value as $platform => $url ) {
				$sanitized_links[ sanitize_key( $platform ) ] = esc_url_raw( $url );
			}
			return $sanitized_links;
		}
		$defaults = anna_get_default_options();
		return $defaults['social_links'];
	}

	// Default: plain text.
	return sanitize_text_field( $value );
}

/**
 * Sanitize all theme options.
 *
 * Merges incoming tab data with existing saved options so that
 * saving one tab does NOT wipe fields from other tabs.
 *
 * @param  array $input Raw input from the submitted form.
 * @return array Complete sanitized options.
 */
function anna_sanitize_options( $input ) {
	$defaults       = anna_get_default_options();
	$existing       = get_option( 'anna_theme_options', array() );
	$existing       = is_array( $existing ) ? $existing : array();
	$tab_fields_map = anna_get_tab_fields_map();

	// Determine which tab was submitted.
	$active_tab = isset( $input['_anna_active_tab'] ) ? sanitize_key( $input['_anna_active_tab'] ) : '';

	// Start from existing saved options (preserves all other tabs).
	$sanitized = wp_parse_args( $existing, $defaults );

	// If we know which tab was submitted, only update that tab's fields.
	if ( $active_tab && isset( $tab_fields_map[ $active_tab ] ) ) {
		$tab_keys = $tab_fields_map[ $active_tab ];

		foreach ( $tab_keys as $key ) {
			if ( isset( $input[ $key ] ) ) {
				$sanitized[ $key ] = anna_sanitize_single_option( $key, $input[ $key ] );
			} else {
				// For checkboxes/toggles: unchecked = not in POST = false.
				$bool_fields = array(
					'animations_enabled',
					'section_hero_enabled', 'section_intro_enabled', 'section_recognition_enabled',
					'section_services_enabled', 'section_about_enabled', 'section_testimonials_enabled', 'section_cta_enabled',
				);
				if ( in_array( $key, $bool_fields, true ) ) {
					$sanitized[ $key ] = false;
				}
				// For image IDs: if not present, means cleared.
				$int_fields = array( 'hero_image_id', 'about_image_id', 'intro_image_id', 'recognition_image_id', 'cta_image_id', 'seo_og_image_id' );
				if ( in_array( $key, $int_fields, true ) ) {
					$sanitized[ $key ] = '';
				}
			}
		}
	} else {
		// Fallback: sanitize everything that was submitted.
		foreach ( $input as $key => $value ) {
			if ( $key === '_anna_active_tab' ) {
				continue;
			}
			$sanitized[ $key ] = anna_sanitize_single_option( $key, $value );
		}
	}

	// Remove internal tracking key.
	unset( $sanitized['_anna_active_tab'] );

	return $sanitized;
}
