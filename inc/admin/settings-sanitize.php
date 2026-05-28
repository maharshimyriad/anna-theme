<?php
/**
 * Admin Settings - Sanitization callbacks
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
 * @return array
 */
function anna_get_tab_fields_map() {
	return array(
		'brand' => array(
			'site_logo_id',
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
			'intro_eyebrow', 'intro_heading', 'intro_body', 'intro_quote', 'intro_quote_cite', 'intro_image_id',
			'recognition_eyebrow', 'recognition_heading', 'recognition_description', 'recognition_items_text', 'recognition_image_id',
			'services_eyebrow', 'services_heading', 'services_description', 'services_cta_text', 'services_cta_url',
			'about_eyebrow', 'about_heading', 'about_body', 'about_image_id',
			'about_badge_number', 'about_badge_text', 'about_quote', 'about_expertise_text', 'about_cta_text', 'about_cta_url',
			'testimonials_eyebrow', 'testimonials_heading', 'testimonials_summary', 'testimonials_cta_text', 'testimonials_cta_url',
		),
		'about_page' => array(
			'about_pg_hero_eyebrow', 'about_pg_hero_heading', 'about_pg_hero_subheading', 'about_pg_hero_description', 'about_pg_hero_tags_text', 'about_pg_hero_image_id',
			'about_pg_story_eyebrow', 'about_pg_story_heading', 'about_pg_story_body', 'about_pg_story_image_id',
			'about_pg_rock_heading', 'about_pg_rock_left_body', 'about_pg_rock_right_body',
			'about_pg_coach_eyebrow', 'about_pg_coach_title', 'about_pg_coach_body', 'about_pg_coach_button_text', 'about_pg_coach_button_url', 'about_pg_coach_image_id',
			'about_pg_work_eyebrow', 'about_pg_work_heading', 'about_pg_work_body',
			'about_pg_work_card_1_title', 'about_pg_work_card_1_body',
			'about_pg_work_card_2_title', 'about_pg_work_card_2_body',
			'about_pg_work_card_3_title', 'about_pg_work_card_3_body',
			'about_pg_work_card_4_title', 'about_pg_work_card_4_body',
			'about_pg_qual_heading', 'about_pg_qual_intro', 'about_pg_qual_items_text',
			'about_pg_life_eyebrow', 'about_pg_life_heading', 'about_pg_life_body', 'about_pg_life_image_id',
		),
		'cta' => array(
			'cta_eyebrow', 'cta_heading', 'cta_description', 'cta_trust', 'cta_image_id',
			'cta_primary_text', 'cta_primary_url', 'cta_secondary_text', 'cta_secondary_url',
		),
		'footer' => array(
			'footer_description', 'contact_email', 'contact_phone', 'contact_address', 'contact_hours',
			'newsletter_heading', 'newsletter_text', 'newsletter_name_placeholder', 'newsletter_email_placeholder', 'newsletter_button_text',
			'copyright_text', 'privacy_url', 'terms_url',
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
 * @param string $key   Option key.
 * @param mixed  $value Raw value.
 * @return mixed
 */
function anna_sanitize_single_option( $key, $value ) {
	$color_fields = array( 'color_primary', 'color_accent', 'color_bg_soft', 'color_text', 'color_heading' );
	if ( in_array( $key, $color_fields, true ) ) {
		return sanitize_hex_color( $value );
	}

	$url_fields = array( 'header_cta_url', 'cta_primary_url', 'cta_secondary_url', 'services_cta_url', 'about_cta_url', 'privacy_url', 'terms_url', 'testimonials_cta_url', 'about_pg_coach_button_url' );
	if ( in_array( $key, $url_fields, true ) ) {
		return esc_url_raw( $value );
	}

	$html_fields = array( 'hero_heading', 'about_heading', 'about_body', 'intro_body', 'intro_heading', 'cta_heading' );
	if ( in_array( $key, $html_fields, true ) ) {
		return wp_kses_post( $value );
	}

	$textarea_fields = array(
		'hero_description', 'intro_quote', 'recognition_description', 'services_description', 'about_quote', 'cta_description',
		'footer_description', 'newsletter_text', 'contact_address', 'recognition_items_text', 'about_expertise_text',
		'about_pg_hero_description', 'about_pg_story_body', 'about_pg_rock_left_body', 'about_pg_rock_right_body',
		'about_pg_coach_body', 'about_pg_work_body',
		'about_pg_work_card_1_body', 'about_pg_work_card_2_body', 'about_pg_work_card_3_body', 'about_pg_work_card_4_body',
		'about_pg_qual_intro', 'about_pg_qual_items_text', 'about_pg_life_body',
		'about_pg_hero_tags_text',
	);
	if ( in_array( $key, $textarea_fields, true ) ) {
		return sanitize_textarea_field( $value );
	}

	$int_fields = array(
		'site_logo_id', 'hero_image_id', 'about_image_id', 'intro_image_id', 'recognition_image_id', 'cta_image_id', 'seo_og_image_id',
		'about_pg_hero_image_id', 'about_pg_story_image_id', 'about_pg_life_image_id', 'about_pg_coach_image_id',
	);
	if ( in_array( $key, $int_fields, true ) ) {
		return absint( $value );
	}

	$bool_fields = array(
		'animations_enabled',
		'section_hero_enabled', 'section_intro_enabled', 'section_recognition_enabled',
		'section_services_enabled', 'section_about_enabled', 'section_testimonials_enabled', 'section_cta_enabled',
	);
	if ( in_array( $key, $bool_fields, true ) ) {
		return ! empty( $value );
	}

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

	return sanitize_text_field( $value );
}

/**
 * Sanitize all theme options.
 *
 * @param array $input Raw input from the submitted form.
 * @return array
 */
function anna_sanitize_options( $input ) {
	$defaults       = anna_get_default_options();
	$existing       = get_option( 'anna_theme_options', array() );
	$existing       = is_array( $existing ) ? $existing : array();
	$tab_fields_map = anna_get_tab_fields_map();
	$active_tab     = isset( $input['_anna_active_tab'] ) ? sanitize_key( $input['_anna_active_tab'] ) : '';
	$sanitized      = wp_parse_args( $existing, $defaults );
	$int_fields     = array(
		'site_logo_id', 'hero_image_id', 'about_image_id', 'intro_image_id', 'recognition_image_id', 'cta_image_id', 'seo_og_image_id',
		'about_pg_hero_image_id', 'about_pg_story_image_id', 'about_pg_life_image_id',
	);
	$bool_fields    = array(
		'animations_enabled',
		'section_hero_enabled', 'section_intro_enabled', 'section_recognition_enabled',
		'section_services_enabled', 'section_about_enabled', 'section_testimonials_enabled', 'section_cta_enabled',
	);

	if ( $active_tab && isset( $tab_fields_map[ $active_tab ] ) ) {
		foreach ( $tab_fields_map[ $active_tab ] as $key ) {
			if ( isset( $input[ $key ] ) ) {
				$sanitized[ $key ] = anna_sanitize_single_option( $key, $input[ $key ] );
			} elseif ( in_array( $key, $bool_fields, true ) ) {
				$sanitized[ $key ] = false;
			} elseif ( in_array( $key, $int_fields, true ) ) {
				$sanitized[ $key ] = '';
			}
		}
	} else {
		foreach ( $input as $key => $value ) {
			if ( '_anna_active_tab' === $key ) {
				continue;
			}

			$sanitized[ $key ] = anna_sanitize_single_option( $key, $value );
		}
	}

	unset( $sanitized['_anna_active_tab'] );

	return $sanitized;
}
