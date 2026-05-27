<?php
/**
 * Helper functions.
 *
 * Reusable utility functions used throughout the theme.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retrieve a theme option value with fallback.
 *
 * @param  string $key     Option key (without prefix).
 * @param  mixed  $default Fallback value.
 * @return mixed
 */
function anna_get_option( $key, $default = '' ) {
	$options = get_option( 'anna_theme_options', array() );
	return isset( $options[ $key ] ) && $options[ $key ] !== '' ? $options[ $key ] : $default;
}

/**
 * Get the current page ID used for dynamic content lookup.
 *
 * @return int
 */
function anna_get_current_page_content_id() {
	if ( is_front_page() ) {
		return (int) get_queried_object_id();
	}

	if ( is_page() || is_singular() ) {
		return (int) get_queried_object_id();
	}

	return 0;
}

/**
 * Get homepage hero content from page data, falling back to theme options.
 *
 * @return array
 */
function anna_get_homepage_hero_content() {
	$defaults = anna_get_default_options();
	$content  = array(
		'eyebrow'     => anna_get_option( 'hero_eyebrow', $defaults['hero_eyebrow'] ),
		'heading'     => anna_get_option( 'hero_heading', $defaults['hero_heading'] ),
		'description' => anna_get_option( 'hero_description', $defaults['hero_description'] ),
		'trust_text'  => anna_get_option( 'hero_trust_text', $defaults['hero_trust_text'] ),
		'image_id'    => absint( anna_get_option( 'hero_image_id', $defaults['hero_image_id'] ) ),
		'stats'       => anna_get_stats(),
		'primary_cta' => anna_get_cta( 'primary' ),
		'secondary_cta' => anna_get_cta( 'secondary' ),
	);

	$post_id = anna_get_current_page_content_id();
	if ( $post_id && function_exists( 'anna_content_get_page_section' ) ) {
		$hero = anna_content_get_page_section( $post_id, 'hero' );

		if ( ! empty( $hero['eyebrow'] ) ) {
			$content['eyebrow'] = $hero['eyebrow'];
		}

		if ( ! empty( $hero['heading'] ) ) {
			$content['heading'] = nl2br( $hero['heading'] );
		}

		if ( ! empty( $hero['description'] ) ) {
			$content['description'] = $hero['description'];
		}

		if ( ! empty( $hero['trust_text'] ) ) {
			$content['trust_text'] = $hero['trust_text'];
		}

		if ( ! empty( $hero['image_id'] ) ) {
			$content['image_id'] = absint( $hero['image_id'] );
		}

		if ( ! empty( $hero['primary_button_text'] ) ) {
			$content['primary_cta']['text'] = $hero['primary_button_text'];
		}

		if ( ! empty( $hero['primary_button_url'] ) ) {
			$content['primary_cta']['url'] = $hero['primary_button_url'];
		}

		if ( ! empty( $hero['secondary_button_text'] ) ) {
			$content['secondary_cta']['text'] = $hero['secondary_button_text'];
		}

		if ( ! empty( $hero['secondary_button_url'] ) ) {
			$content['secondary_cta']['url'] = $hero['secondary_button_url'];
		}

		$stats = array();
		for ( $i = 1; $i <= 3; $i++ ) {
			$value = $hero[ 'stat_' . $i . '_value' ] ?? '';
			$label = $hero[ 'stat_' . $i . '_label' ] ?? '';
			if ( '' !== $value || '' !== $label ) {
				$stats[] = array(
					'value' => $value,
					'label' => $label,
				);
			}
		}

		if ( ! empty( $stats ) ) {
			$content['stats'] = $stats;
		}
	}

	return $content;
}

/**
 * Get intro/recognition content from page data, with legacy fallback.
 *
 * @return array
 */
function anna_get_intro_section_content() {
	$content = array(
		'intro_eyebrow'           => anna_get_option( 'intro_eyebrow', '' ),
		'intro_heading'           => anna_get_option( 'intro_heading', 'Real change. From the inside out.' ),
		'intro_body'              => anna_get_option( 'intro_body', '' ),
		'intro_quote'             => anna_get_option( 'intro_quote', '' ),
		'intro_quote_cite'        => anna_get_option( 'intro_quote_cite', '' ),
		'recognition_eyebrow'     => anna_get_option( 'recognition_eyebrow', '' ),
		'recognition_heading'     => anna_get_option( 'recognition_heading', 'You might recognise yourself here' ),
		'recognition_description' => anna_get_option( 'recognition_description', '' ),
		'recognition_items'       => anna_get_lines_option(
			'recognition_items_text',
			array(
				'You feel stuck, disconnected or like you\'re going through the motions',
				'You know what you need to do but you\'re not doing it',
				'You\'ve tried therapy, programs and self-help and something still feels missing',
				'You put everyone else first and run on empty',
				'You sense there\'s more available to you but don\'t know how to access it',
				'You want to feel genuinely well, not just functional',
			)
		),
	);

	$post_id = anna_get_current_page_content_id();
	if ( $post_id && function_exists( 'anna_content_get_page_section' ) ) {
		$data = anna_content_get_page_section( $post_id, 'intro' );
		if ( ! empty( $data['intro_eyebrow'] ) ) {
			$content['intro_eyebrow'] = $data['intro_eyebrow'];
		}
		if ( ! empty( $data['intro_heading'] ) ) {
			$content['intro_heading'] = $data['intro_heading'];
		}
		if ( ! empty( $data['intro_body'] ) ) {
			$content['intro_body'] = $data['intro_body'];
		}
		if ( ! empty( $data['intro_quote'] ) ) {
			$content['intro_quote'] = $data['intro_quote'];
		}
		if ( ! empty( $data['intro_quote_cite'] ) ) {
			$content['intro_quote_cite'] = $data['intro_quote_cite'];
		}
		if ( ! empty( $data['recognition_eyebrow'] ) ) {
			$content['recognition_eyebrow'] = $data['recognition_eyebrow'];
		}
		if ( ! empty( $data['recognition_heading'] ) ) {
			$content['recognition_heading'] = $data['recognition_heading'];
		}
		if ( ! empty( $data['recognition_description'] ) ) {
			$content['recognition_description'] = $data['recognition_description'];
		}
		if ( ! empty( $data['recognition_items_text'] ) ) {
			$content['recognition_items'] = preg_split( '/\r\n|\r|\n/', $data['recognition_items_text'] );
			$content['recognition_items'] = array_values( array_filter( array_map( 'trim', $content['recognition_items'] ) ) );
		}
	}

	return $content;
}

/**
 * Get services section content from page data, with legacy fallback.
 *
 * @return array
 */
function anna_get_services_section_content() {
	$content = array(
		'eyebrow'     => anna_get_option( 'services_eyebrow', '' ),
		'heading'     => anna_get_option( 'services_heading', 'What\'s the change you\'re needing?' ),
		'description' => anna_get_option( 'services_description', '' ),
		'cta_text'    => anna_get_option( 'services_cta_text', '' ),
		'cta_url'     => anna_get_option( 'services_cta_url', '#' ),
	);

	$post_id = anna_get_current_page_content_id();
	if ( $post_id && function_exists( 'anna_content_get_page_section' ) ) {
		$data = anna_content_get_page_section( $post_id, 'services' );
		foreach ( array( 'eyebrow', 'heading', 'description', 'cta_text', 'cta_url' ) as $key ) {
			if ( ! empty( $data[ $key ] ) ) {
				$content[ $key ] = $data[ $key ];
			}
		}
	}

	return $content;
}

/**
 * Get about section content from page data, with legacy fallback.
 *
 * @return array
 */
function anna_get_about_section_content() {
	$content = array(
		'eyebrow'        => anna_get_option( 'about_eyebrow', '' ),
		'heading'        => anna_get_option( 'about_heading', 'Olympian. Life Coach. Motivational Speaker.' ),
		'body'           => anna_get_option( 'about_body', '' ),
		'quote'          => anna_get_option( 'about_quote', '' ),
		'image_id'       => anna_get_option( 'about_image_id', '' ),
		'badge_number'   => anna_get_option( 'about_badge_number', '' ),
		'badge_text'     => anna_get_option( 'about_badge_text', '' ),
		'expertise'      => anna_get_lines_option( 'about_expertise_text', array() ),
		'cta_text'       => anna_get_option( 'about_cta_text', '' ),
		'cta_url'        => anna_get_option( 'about_cta_url', '#' ),
	);

	$post_id = anna_get_current_page_content_id();
	if ( $post_id && function_exists( 'anna_content_get_page_section' ) ) {
		$data = anna_content_get_page_section( $post_id, 'about' );
		foreach ( array( 'eyebrow', 'heading', 'body', 'quote', 'badge_number', 'badge_text', 'cta_text', 'cta_url' ) as $key ) {
			if ( ! empty( $data[ $key ] ) ) {
				$content[ $key ] = $data[ $key ];
			}
		}
		if ( ! empty( $data['image_id'] ) ) {
			$content['image_id'] = absint( $data['image_id'] );
		}
		if ( ! empty( $data['expertise_text'] ) ) {
			$content['expertise'] = preg_split( '/\r\n|\r|\n/', $data['expertise_text'] );
			$content['expertise'] = array_values( array_filter( array_map( 'trim', $content['expertise'] ) ) );
		}
	}

	return $content;
}

/**
 * Get testimonials section content from page data, with legacy fallback.
 *
 * @return array
 */
function anna_get_testimonials_section_content() {
	$content = array(
		'eyebrow'  => anna_get_option( 'testimonials_eyebrow', '' ),
		'heading'  => anna_get_option( 'testimonials_heading', '102 five-star Google reviews' ),
		'summary'  => anna_get_option( 'testimonials_summary', '' ),
		'cta_text' => anna_get_option( 'testimonials_cta_text', '' ),
		'cta_url'  => anna_get_option( 'testimonials_cta_url', '#' ),
	);

	$post_id = anna_get_current_page_content_id();
	if ( $post_id && function_exists( 'anna_content_get_page_section' ) ) {
		$data = anna_content_get_page_section( $post_id, 'testimonials' );
		foreach ( array( 'eyebrow', 'heading', 'summary', 'cta_text', 'cta_url' ) as $key ) {
			if ( ! empty( $data[ $key ] ) ) {
				$content[ $key ] = $data[ $key ];
			}
		}
	}

	return $content;
}

/**
 * Get final CTA section content from page data, with legacy fallback.
 *
 * @return array
 */
function anna_get_final_cta_section_content() {
	$content = array(
		'eyebrow'       => anna_get_option( 'cta_eyebrow', '' ),
		'heading'       => anna_get_option( 'cta_heading', '' ),
		'description'   => anna_get_option( 'cta_description', '' ),
		'trust_text'    => anna_get_option( 'cta_trust', '' ),
		'primary_cta'   => anna_get_cta( 'primary' ),
		'secondary_cta' => anna_get_cta( 'secondary' ),
	);

	$post_id = anna_get_current_page_content_id();
	if ( $post_id && function_exists( 'anna_content_get_page_section' ) ) {
		$data = anna_content_get_page_section( $post_id, 'cta' );
		foreach ( array( 'eyebrow', 'heading', 'description', 'trust_text' ) as $key ) {
			if ( ! empty( $data[ $key ] ) ) {
				$content[ $key ] = $data[ $key ];
			}
		}
		if ( ! empty( $data['primary_button_text'] ) ) {
			$content['primary_cta']['text'] = $data['primary_button_text'];
		}
		if ( ! empty( $data['primary_button_url'] ) ) {
			$content['primary_cta']['url'] = $data['primary_button_url'];
		}
		if ( ! empty( $data['secondary_button_text'] ) ) {
			$content['secondary_cta']['text'] = $data['secondary_button_text'];
		}
		if ( ! empty( $data['secondary_button_url'] ) ) {
			$content['secondary_cta']['url'] = $data['secondary_button_url'];
		}
	}

	return $content;
}

/**
 * Default About page content keys mapped to theme option names.
 *
 * @return array<string, string> Template key => option key.
 */
function anna_get_about_page_option_map() {
	return array(
		'hero_eyebrow'        => 'about_pg_hero_eyebrow',
		'hero_heading'        => 'about_pg_hero_heading',
		'hero_subheading'     => 'about_pg_hero_subheading',
		'hero_description'    => 'about_pg_hero_description',
		'hero_image_id'       => 'about_pg_hero_image_id',
		'story_eyebrow'       => 'about_pg_story_eyebrow',
		'story_heading'       => 'about_pg_story_heading',
		'story_body'          => 'about_pg_story_body',
		'story_image_id'      => 'about_pg_story_image_id',
		'rock_heading'        => 'about_pg_rock_heading',
		'rock_left_body'      => 'about_pg_rock_left_body',
		'rock_right_body'     => 'about_pg_rock_right_body',
		'coach_heading'       => 'about_pg_coach_heading',
		'coach_left_body'     => 'about_pg_coach_left_body',
		'coach_right_body'    => 'about_pg_coach_right_body',
		'coach_quote'         => 'about_pg_coach_quote',
		'approach_eyebrow'    => 'about_pg_approach_eyebrow',
		'approach_heading'    => 'about_pg_approach_heading',
		'approach_intro'      => 'about_pg_approach_intro',
		'approach_left_body'  => 'about_pg_approach_left_body',
		'approach_right_body' => 'about_pg_approach_right_body',
		'qual_heading'        => 'about_pg_qual_heading',
		'qual_intro'          => 'about_pg_qual_intro',
		'life_eyebrow'        => 'about_pg_life_eyebrow',
		'life_heading'        => 'about_pg_life_heading',
		'life_body'           => 'about_pg_life_body',
		'life_image_id'       => 'about_pg_life_image_id',
	);
}

/**
 * Get About page content from theme options (same pattern as homepage sections).
 *
 * @return array
 */
function anna_get_about_page_content() {
	$defaults  = anna_get_default_options();
	$option_map = anna_get_about_page_option_map();
	$content   = array();

	foreach ( $option_map as $template_key => $option_key ) {
		$default = $defaults[ $option_key ] ?? '';

		if ( str_ends_with( $template_key, '_image_id' ) ) {
			$content[ $template_key ] = absint( anna_get_option( $option_key, $default ) );
			continue;
		}

		$content[ $template_key ] = anna_get_option( $option_key, $default );
	}

	$qual_default = isset( $defaults['about_pg_qual_items_text'] )
		? preg_split( '/\r\n|\r|\n/', $defaults['about_pg_qual_items_text'] )
		: array();

	$content['qual_items'] = anna_get_lines_option( 'about_pg_qual_items_text', $qual_default );

	$post_id = anna_get_current_page_content_id();
	if ( $post_id && function_exists( 'anna_content_get_about_page_content' ) ) {
		$saved = anna_content_get_about_page_content( $post_id );
		if ( is_array( $saved ) ) {
			$content = wp_parse_args( $saved, $content );
		}
	}

	if ( is_string( $content['qual_items'] ) ) {
		$content['qual_items'] = preg_split( '/\r\n|\r|\n/', $content['qual_items'] );
	}

	$content['qual_items'] = array_values( array_filter( array_map( 'trim', (array) $content['qual_items'] ) ) );

	return $content;
}

/**
 * Get a newline-separated option as an array of trimmed lines.
 *
 * @param string $key     Option key.
 * @param array  $default Fallback lines.
 * @return array
 */
function anna_get_lines_option( $key, $default = array() ) {
	$value = anna_get_option( $key, '' );

	if ( is_array( $value ) ) {
		$value = implode( "\n", $value );
	}

	if ( ! is_string( $value ) || '' === trim( $value ) ) {
		return $default;
	}

	$lines = preg_split( '/\r\n|\r|\n/', $value );
	$lines = array_map( 'trim', $lines );
	$lines = array_filter( $lines );

	return array_values( $lines );
}

/**
 * Output a theme option value (escaped).
 *
 * @param  string $key     Option key.
 * @param  mixed  $default Fallback value.
 */
function anna_option( $key, $default = '' ) {
	echo esc_html( anna_get_option( $key, $default ) );
}

/**
 * Get the social links array.
 *
 * @return array Associative array of platform => URL.
 */
function anna_get_social_links() {
	$defaults = array(
		'instagram' => '',
		'facebook'  => '',
		'linkedin'  => '',
		'twitter'   => '',
		'youtube'   => '',
		'tiktok'    => '',
	);

	$saved = anna_get_option( 'social_links', array() );
	return wp_parse_args( $saved, $defaults );
}

/**
 * Render social links as an accessible list.
 *
 * @param string $class Optional CSS class modifier.
 */
function anna_social_links( $class = '' ) {
	$links = anna_get_social_links();
	$icons = array(
		'instagram' => '<svg aria-hidden="true" focusable="false" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>',
		'facebook'  => '<svg aria-hidden="true" focusable="false" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
		'linkedin'  => '<svg aria-hidden="true" focusable="false" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
		'twitter'   => '<svg aria-hidden="true" focusable="false" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.748l7.73-8.835L1.254 2.25H8.08l4.259 5.63zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
		'youtube'   => '<svg aria-hidden="true" focusable="false" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23.495 6.205a3.007 3.007 0 0 0-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 0 0 .527 6.205a31.247 31.247 0 0 0-.522 5.805 31.247 31.247 0 0 0 .522 5.783 3.007 3.007 0 0 0 2.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 0 0 2.088-2.088 31.247 31.247 0 0 0 .5-5.783 31.247 31.247 0 0 0-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/></svg>',
		'tiktok'    => '<svg aria-hidden="true" focusable="false" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>',
	);

	$platform_labels = array(
		'instagram' => __( 'Instagram', 'anna-baylis' ),
		'facebook'  => __( 'Facebook', 'anna-baylis' ),
		'linkedin'  => __( 'LinkedIn', 'anna-baylis' ),
		'twitter'   => __( 'X (Twitter)', 'anna-baylis' ),
		'youtube'   => __( 'YouTube', 'anna-baylis' ),
		'tiktok'    => __( 'TikTok', 'anna-baylis' ),
	);

	$active = array_filter( $links );

	if ( empty( $active ) ) {
		return;
	}

	$class_attr = $class ? ' anna-social-links--' . esc_attr( $class ) : '';

	echo '<ul class="anna-social-links' . $class_attr . '" aria-label="' . esc_attr__( 'Social media profiles', 'anna-baylis' ) . '">';

	foreach ( $active as $platform => $url ) {
		if ( empty( $url ) ) {
			continue;
		}
		$label = $platform_labels[ $platform ] ?? ucfirst( $platform );
		$icon  = $icons[ $platform ] ?? '';

		echo '<li class="anna-social-links__item">';
		echo '<a href="' . esc_url( $url ) . '" class="anna-social-links__link anna-social-links__link--' . esc_attr( $platform ) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( sprintf( __( 'Follow on %s', 'anna-baylis' ), $label ) ) . '">';
		echo wp_kses( $icon, anna_allowed_svg_tags() );
		echo '<span class="anna-sr-only">' . esc_html( $label ) . '</span>';
		echo '</a>';
		echo '</li>';
	}

	echo '</ul>';
}

/**
 * Return allowed SVG tags for wp_kses.
 *
 * @return array
 */
function anna_allowed_svg_tags() {
	return array(
		'svg'      => array(
			'aria-hidden' => true,
			'focusable'   => true,
			'width'       => true,
			'height'      => true,
			'viewbox'     => true,
			'fill'        => true,
			'xmlns'       => true,
		),
		'path'     => array( 'd' => true, 'fill' => true ),
		'circle'   => array( 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true ),
		'rect'     => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ),
		'polyline' => array( 'points' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true ),
		'line'     => array( 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'stroke' => true, 'stroke-width' => true ),
		'g'        => array( 'fill' => true, 'stroke' => true ),
	);
}

/**
 * Get star rating HTML.
 *
 * @param  int $rating Rating out of 5.
 * @return string
 */
function anna_star_rating( $rating = 5 ) {
	$rating = absint( $rating );
	$rating = min( 5, max( 0, $rating ) );

	$output  = '<span class="anna-stars" aria-label="' . esc_attr( sprintf( __( '%d out of 5 stars', 'anna-baylis' ), $rating ) ) . '" role="img">';
	$output .= '<meter class="anna-stars__meter anna-sr-only" min="0" max="5" value="' . esc_attr( $rating ) . '">' . esc_html( $rating ) . '/5</meter>';

	for ( $i = 1; $i <= 5; $i++ ) {
		$filled  = $i <= $rating ? 'anna-stars__star--filled' : 'anna-stars__star--empty';
		$output .= '<span class="anna-stars__star ' . $filled . '" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M9.60889 1.91642C9.68358 1.76545 9.8374 1.66992 10.0058 1.66992C10.1742 1.66992 10.328 1.76545 10.4027 1.91642L12.3329 5.8275C12.591 6.35005 13.0892 6.71248 13.6657 6.79712L17.9823 7.42905C18.1492 7.45323 18.2879 7.57009 18.34 7.73047C18.3921 7.89086 18.3487 8.06695 18.228 8.18469L15.1062 11.2256C14.6883 11.633 14.4974 12.22 14.5957 12.7954L15.3327 17.0918C15.3621 17.2587 15.2939 17.4277 15.1568 17.5273C15.0198 17.6269 14.838 17.6396 14.6884 17.5599L10.8297 15.5304C10.3136 15.2593 9.69719 15.2593 9.18106 15.5304L5.32314 17.5599C5.17368 17.6391 4.99222 17.6262 4.85545 17.5267C4.71869 17.4272 4.65051 17.2584 4.67973 17.0918L5.41589 12.7962C5.51461 12.2205 5.32366 11.6331 4.90534 11.2256L1.78357 8.18552C1.66184 8.0679 1.61776 7.89116 1.67 7.73013C1.72224 7.56909 1.86166 7.45192 2.02924 7.42821L6.34507 6.79712C6.92222 6.71313 7.42118 6.35058 7.67951 5.8275L9.60889 1.91642Z" fill="#A1C842" stroke="#A1C842" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
</span>';
	}

	$output .= '</span>';
	return $output;
}

/**
 * Check if a section is enabled in admin settings.
 *
 * @param  string $section Section key.
 * @return bool
 */
function anna_section_enabled( $section ) {
	$key     = 'section_' . $section . '_enabled';
	$enabled = anna_get_option( $key, true );
	return (bool) $enabled;
}

/**
 * Get the CTA data from options.
 *
 * @param  string $type 'primary' or 'secondary'.
 * @return array
 */
function anna_get_cta( $type = 'primary' ) {
	$defaults = anna_get_default_options();

	return array(
		'text' => anna_get_option( 'cta_' . $type . '_text', $defaults[ 'cta_' . $type . '_text' ] ?? '' ),
		'url'  => anna_get_option( 'cta_' . $type . '_url', $defaults[ 'cta_' . $type . '_url' ] ?? '#' ),
	);
}

/**
 * Output a CTA button.
 *
 * @param string $type    'primary', 'secondary', or 'ghost'.
 * @param string $text    Override button text.
 * @param string $url     Override URL.
 * @param string $classes Additional CSS classes.
 */
function anna_cta_button( $type = 'primary', $text = '', $url = '', $classes = '' ) {
	if ( ! $text ) {
		$cta  = anna_get_cta( $type );
		$text = $cta['text'];
		$url  = $cta['url'];
	}

	$modifier = 'anna-btn--' . esc_attr( $type );
	$class    = 'anna-btn ' . $modifier . ( $classes ? ' ' . esc_attr( $classes ) : '' );

	printf(
		'<a href="%s" class="%s">%s</a>',
		esc_url( $url ),
		esc_attr( $class ),
		esc_html( $text )
	);
}
