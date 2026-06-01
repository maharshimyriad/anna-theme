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
			'about_pg_people_eyebrow', 'about_pg_people_heading', 'about_pg_people_body', 'about_pg_people_items', 'about_pg_people_items_text',
			'about_pg_connect_eyebrow', 'about_pg_connect_heading', 'about_pg_connect_button_text', 'about_pg_connect_button_url',
		),
		'oasis_page' => array_keys( function_exists( 'anna_get_oasis_theme_option_defaults' ) ? anna_get_oasis_theme_option_defaults() : array() ),
		'speaking_page' => array_keys( function_exists( 'anna_get_speaking_theme_option_defaults' ) ? anna_get_speaking_theme_option_defaults() : array() ),
		'coaching_page' => array(
			'coaching_pg_hero_eyebrow', 'coaching_pg_hero_heading', 'coaching_pg_hero_description', 'coaching_pg_hero_tags_text', 'coaching_pg_hero_image_id',
			'coaching_pg_hero_button_text', 'coaching_pg_hero_button_url',
			'coaching_pg_what_eyebrow', 'coaching_pg_what_heading', 'coaching_pg_what_body',
			'coaching_pg_what_button_text', 'coaching_pg_what_button_url', 'coaching_pg_what_card_heading', 'coaching_pg_what_card_items',
			'coaching_pg_pillars_eyebrow', 'coaching_pg_pillars_heading', 'coaching_pg_pillar_items',
			'coaching_pg_work_eyebrow', 'coaching_pg_work_heading', 'coaching_pg_work_gains_heading',
			'coaching_pg_work_topics_items', 'coaching_pg_work_gains_items',
			'coaching_pg_expect_eyebrow', 'coaching_pg_expect_heading_line1', 'coaching_pg_expect_heading_line2',
			'coaching_pg_expect_body', 'coaching_pg_expect_quote', 'coaching_pg_expect_button_text', 'coaching_pg_expect_button_url',
			'coaching_pg_expect_info_cards', 'coaching_pg_faq_heading', 'coaching_pg_faq_items',
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

	$url_fields = array( 'header_cta_url', 'cta_primary_url', 'cta_secondary_url', 'services_cta_url', 'about_cta_url', 'privacy_url', 'terms_url', 'testimonials_cta_url', 'about_pg_coach_button_url', 'about_pg_connect_button_url', 'coaching_pg_hero_button_url', 'coaching_pg_what_button_url', 'coaching_pg_expect_button_url', 'oasis_pg_hero_button_url', 'oasis_pg_what_footer_url', 'oasis_pg_begun_link_url', 'oasis_pg_waitlist_button_url', 'speaking_pg_hero_button_url', 'speaking_pg_hero_secondary_url', 'speaking_pg_bring_button_url', 'speaking_pg_experience_link_url' );
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
		'about_pg_people_body', 'about_pg_people_items_text',
		'about_pg_hero_tags_text',
		'coaching_pg_hero_description', 'coaching_pg_what_body', 'coaching_pg_expect_body', 'coaching_pg_expect_quote',
		'coaching_pg_hero_heading',
	);
	if ( in_array( $key, $textarea_fields, true ) ) {
		return sanitize_textarea_field( $value );
	}

	$int_fields = array(
		'site_logo_id', 'hero_image_id', 'about_image_id', 'intro_image_id', 'recognition_image_id', 'cta_image_id', 'seo_og_image_id',
		'about_pg_hero_image_id', 'about_pg_story_image_id', 'about_pg_coach_image_id',
		'coaching_pg_hero_image_id',
		'oasis_pg_hero_image_id', 'oasis_pg_begun_image_id',
		'speaking_pg_hero_image_id', 'speaking_pg_bring_image_id',
	);
	if ( in_array( $key, $int_fields, true ) ) {
		return absint( $value );
	}

	if ( 'about_pg_people_items' === $key ) {
		if ( ! is_array( $value ) ) {
			return array();
		}

		$items = array();
		foreach ( $value as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$logo_id  = absint( $row['logo_id'] ?? 0 );
			$initials = sanitize_text_field( $row['initials'] ?? '' );
			$title    = sanitize_text_field( $row['title'] ?? '' );
			$org      = sanitize_textarea_field( $row['description'] ?? $row['org'] ?? '' );

			if ( 0 === $logo_id && '' === trim( $initials ) && '' === trim( $title ) && '' === trim( $org ) ) {
				continue;
			}

			$items[] = array(
				'logo_id'  => $logo_id,
				'initials' => $initials,
				'title'    => $title,
				'org'      => $org,
			);
		}

		return $items;
	}

	if ( in_array( $key, array( 'coaching_pg_work_topics_items', 'coaching_pg_work_gains_items', 'coaching_pg_what_card_items' ), true ) ) {
		return function_exists( 'anna_normalize_coaching_text_items' ) ? anna_normalize_coaching_text_items( $value ) : array();
	}

	if ( 'coaching_pg_pillar_items' === $key ) {
		return function_exists( 'anna_normalize_coaching_pillar_items' ) ? anna_normalize_coaching_pillar_items( $value ) : array();
	}

	if ( 'coaching_pg_expect_info_cards' === $key ) {
		return function_exists( 'anna_normalize_coaching_info_cards' ) ? anna_normalize_coaching_info_cards( $value ) : array();
	}

	if ( 'coaching_pg_faq_items' === $key ) {
		return function_exists( 'anna_normalize_coaching_faq_items' ) ? anna_normalize_coaching_faq_items( $value ) : array();
	}

	if ( str_starts_with( $key, 'oasis_pg_' ) && function_exists( 'anna_sanitize_oasis_option' ) ) {
		return anna_sanitize_oasis_option( $key, $value );
	}

	if ( str_starts_with( $key, 'speaking_pg_' ) && function_exists( 'anna_sanitize_speaking_option' ) ) {
		return anna_sanitize_speaking_option( $key, $value );
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
		'about_pg_hero_image_id', 'about_pg_story_image_id', 'about_pg_coach_image_id',
		'coaching_pg_hero_image_id',
		'oasis_pg_hero_image_id', 'oasis_pg_begun_image_id',
		'speaking_pg_hero_image_id', 'speaking_pg_bring_image_id',
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
