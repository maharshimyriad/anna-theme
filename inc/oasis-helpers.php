<?php
/**
 * Oasis page helpers.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default Oasis page content (design copy).
 *
 * @return array<string, mixed>
 */
function anna_get_oasis_default_content() {
	return array(
		'hero_eyebrow'     => 'Starting 1 July 2026 — join the waitlist',
		'hero_heading'     => 'Oasis',
		'hero_subheading'  => "A women's wellness community for sustainable health and wellbeing.",
		'hero_body'        => "Most women I work with don't need more information. They need a place to land.\n\nSomewhere they can slow down, reconnect with themselves, and take the next small step with the right guidance and support around them. A space that's theirs. Steady, grounded, and always there.\n\nThat's why I built Oasis.",
		'hero_image_id'    => 0,
		'hero_button_text' => 'Join the waitlist',
		'hero_button_url'  => '#contact',

		'what_eyebrow'      => 'What Oasis Is',
		'what_heading'      => 'You always have somewhere to return to.',
		'what_body'         => "A space you can return to every single day for a meditation, a reflection, a walk and talk session, or simply a moment to breathe. Each week I'll guide you live and each fortnight we come together to celebrate progress and reconnect.",
		'what_footer_line'  => 'Guided, ongoing support that fits your life.',

		'begun_eyebrow'       => 'Where Oasis began',
		'begun_heading'       => 'Anna Baylis',
		'begun_subheading'    => 'Olympian · Hawaii Ironman finisher · Life coach · Personal trainer',
		'begun_body'          => "For more than two decades I've worked in high-performance sport and coaching — helping people understand their bodies, their minds, and what it actually takes to change.\n\nWhat I've learned is that wellbeing doesn't come from extremes. It comes from strong foundations, consistent rhythm, and having somewhere steady to return to.\n\nI've sat with psychologists and coaches myself. I know what it feels like to rebuild when life falls apart — and what it takes to find your way back to yourself.\n\nOasis grew out of M.O.V.E., the women's wellness program I ran for four years. The principles haven't changed. The container has simply become deeper, more spacious, and more sustainable.",
		'begun_quote'         => "That's not theory. That's my life.",
		'begun_closing'       => '',
		'begun_image_id'      => 0,
		'begun_callout_label' => 'M.O.V.E. — the foundation',
		'begun_callout_body'  => 'Movement · Outdoors · Variety · Education. The principles that guided M.O.V.E. for four years are still at the heart of everything inside Oasis.',

		'inside_eyebrow'      => 'Inside Oasis',
		'inside_heading'      => "What you'll find here",
		'inside_body'         => "What I saw again and again through M.O.V.E. was that the women who thrived weren't the ones who pushed hardest. They were the ones who had something steady to come back to. A rhythm, a guide, and a community that felt like theirs.",
		'inside_highlight'    => "That's exactly what I've built into Oasis.",
		'inside_pills_intro'  => 'What women told me they valued most through M.O.V.E.',
		'inside_pill_items'   => array(
			array( 'text' => 'Consistency and structure they could rely on' ),
			array( 'text' => 'Someone to guide them through' ),
			array( 'text' => 'Something to look forward to' ),
			array( 'text' => 'Connection beyond home and work' ),
			array( 'text' => 'Trust built over time' ),
			array( 'text' => 'A space that felt like theirs' ),
		),
		'inside_schedule_items' => array(
			array( 'title' => 'Monday — Movement.', 'body' => 'A filmed movement practice — core, yoga, strength or cardio, all doable at home. We start the week in the body.' ),
			array( 'title' => 'Tuesday — Teaching video.', 'body' => "A deep dive into the mind, body and nervous system. Each video connects to the season we are in and builds on the week before." ),
			array( 'title' => 'Wednesday — Walk and Talk live at 1pm via Zoom.', 'body' => 'A weekly live session where we connect, share, go deeper and do the inner work together. Oasis Experience members only, with recordings available after.' ),
			array( 'title' => 'Thursday — Journal prompt and reflection.', 'body' => "A prompt that connects directly to the week's teaching. Space to write, reflect and integrate." ),
			array( 'title' => 'Friday — Nutrition.', 'body' => 'A recipe and short tip video built around gut health and real food. Simple, seasonal and genuinely delicious.' ),
			array( 'title' => 'Saturday — Rest.', 'body' => 'A day off. Cook the Friday recipe. Relisten. Breathe.' ),
			array( 'title' => 'Sunday — Breathwork.', 'body' => 'A short breathwork practice — 3 to 5 minutes to close the week and settle the nervous system before Monday begins again.' ),
		),

		'how_eyebrow'    => 'How it works',
		'how_heading'    => 'The rhythm of Oasis',
		'how_intro'      => 'Oasis moves with the seasons. Not a rigid course to complete — a living rhythm you return to, again and again, each time with more awareness and depth.',
		'how_footer'     => 'You can join at any point in the cycle and return to it as many times as you need.',
		'how_card_items' => array(
			array( 'icon' => 'roots', 'title' => 'Roots', 'body' => 'Safety, self-awareness and strong foundations. Slowing down and reconnecting with what matters.' ),
			array( 'icon' => 'expression', 'title' => 'Expression', 'body' => 'Your emotional world. Reconnecting with your emotions, your voice, and the parts of you that have been protecting you.' ),
			array( 'icon' => 'growth', 'title' => 'Growth', 'body' => 'Building habits that fit your real life and who you are becoming. Taking aligned action from a place of self-trust.' ),
			array( 'icon' => 'integration', 'title' => 'Integration', 'body' => 'Bringing it all together. Living it. Embodying it. Sustaining it.' ),
		),

		'choose_eyebrow'    => 'Choose your experience',
		'choose_heading'    => 'Two ways to join',
		'choose_intro'      => 'Both designed around real life. Stay as long as it serves you.',
		'choose_footer'     => 'Minimum 3-month commitment, then cancel anytime. No lock-in contracts, no pressure to stay longer than it serves you.',
		'choose_plan_items' => array(
			array(
				'title'        => 'The Garden',
				'price'        => '$49',
				'price_suffix' => '/ month AUD',
				'annual'       => '$490/year — 2 months free',
				'founding'     => 'Founding member: $39/month, locked in for life',
				'badge'        => '',
				'featured'     => 0,
				'features'     => array(
					array( 'text' => 'Full resource garden access' ),
					array( 'text' => 'All tools, prompts and frameworks' ),
					array( 'text' => 'Daily meditations and reflections' ),
					array( 'text' => 'Seasonal pathways — self-paced' ),
					array( 'text' => 'Your space to return to, always' ),
				),
			),
			array(
				'title'        => 'The Oasis Experience',
				'price'        => '$89',
				'price_suffix' => '/ month AUD',
				'annual'       => '$890/year — 2 months free',
				'founding'     => 'Founding member: $69/month, locked in for life',
				'badge'        => 'Most supported',
				'featured'     => 1,
				'features'     => array(
					array( 'text' => 'Everything in The Garden' ),
					array( 'text' => 'One weekly live Walk and Talk via Zoom' ),
					array( 'text' => 'Session recorded and on-platform after' ),
					array( 'text' => 'One fortnightly Celebration of Self' ),
					array( 'text' => 'Active, supportive community' ),
					array( 'text' => 'Consistency, structure and guidance — ongoing' ),
				),
			),
		),

		'ready_eyebrow' => 'Is Oasis right for you?',
		'ready_heading' => 'You might be ready for Oasis if...',
		'ready_items'   => array(
			array( 'text' => "You know what you need to do but you're not doing it" ),
			array( 'text' => 'You find it hard to maintain habits or care consistently for your body' ),
			array( 'text' => 'You put everyone else first and rarely make space for yourself' ),
			array( 'text' => 'You feel isolated and crave a community that actually gets it' ),
			array( 'text' => 'You want to move your body and create healthy habits but life keeps getting in the way' ),
			array( 'text' => 'For any woman who is feeling the call for community, for a space to connect, learn, grow and heal.' ),
			array( 'text' => "You're tired of scrolling for answers and want one grounded place with real structure and guidance from lived experience" ),
			array( 'text' => "You've tried to change before and nothing seems to stick" ),
		),
	);
}

/**
 * Oasis defaults keyed for theme options (oasis_pg_*).
 *
 * @return array<string, mixed>
 */
function anna_get_oasis_theme_option_defaults() {
	$out = array();
	foreach ( anna_get_oasis_default_content() as $key => $value ) {
		$out[ 'oasis_pg_' . $key ] = $value;
	}
	return $out;
}

/**
 * Theme option key map.
 *
 * @return array<string, string>
 */
function anna_get_oasis_page_option_map() {
	$map = array();
	foreach ( array_keys( anna_get_oasis_default_content() ) as $key ) {
		$map[ $key ] = 'oasis_pg_' . $key;
	}
	return $map;
}

/**
 * @param mixed $items Raw rows.
 * @return array<int, array{text:string}>
 */
function anna_normalize_oasis_text_items( $items ) {
	if ( ! is_array( $items ) ) {
		return array();
	}
	$out = array();
	foreach ( $items as $row ) {
		$text = is_string( $row ) ? $row : ( is_array( $row ) ? ( $row['text'] ?? '' ) : '' );
		$text = sanitize_text_field( $text );
		if ( '' !== trim( $text ) ) {
			$out[] = array( 'text' => $text );
		}
	}
	return $out;
}

/**
 * @param mixed $items Raw rows.
 * @return array<int, array{title:string,body:string}>
 */
function anna_normalize_oasis_schedule_items( $items ) {
	if ( ! is_array( $items ) ) {
		return array();
	}
	$out = array();
	foreach ( $items as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$title = sanitize_text_field( $row['title'] ?? '' );
		$body  = sanitize_textarea_field( $row['body'] ?? '' );
		if ( '' === trim( $title ) && '' === trim( $body ) ) {
			continue;
		}
		$out[] = array( 'title' => $title, 'body' => $body );
	}
	return $out;
}

/**
 * @param mixed $items Raw rows.
 * @return array<int, array{icon:string,title:string,body:string}>
 */
function anna_normalize_oasis_how_cards( $items ) {
	if ( ! is_array( $items ) ) {
		return array();
	}
	$out = array();
	foreach ( $items as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$icon  = sanitize_key( $row['icon'] ?? 'roots' );
		$title = sanitize_text_field( $row['title'] ?? '' );
		$body  = sanitize_textarea_field( $row['body'] ?? '' );
		if ( '' === trim( $title ) && '' === trim( $body ) ) {
			continue;
		}
		$out[] = array( 'icon' => $icon, 'title' => $title, 'body' => $body );
	}
	return $out;
}

/**
 * @param mixed $items Raw rows.
 * @return array<int, array<string, mixed>>
 */
function anna_normalize_oasis_plan_items( $items ) {
	if ( ! is_array( $items ) ) {
		return array();
	}
	$out = array();
	foreach ( $items as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$title = sanitize_text_field( $row['title'] ?? '' );
		if ( '' === trim( $title ) ) {
			continue;
		}
		$features = array();
		if ( isset( $row['features_text'] ) && is_string( $row['features_text'] ) ) {
			$lines = preg_split( '/\r\n|\r|\n/', $row['features_text'] );
			$features = anna_normalize_oasis_text_items( $lines );
		} elseif ( isset( $row['features'] ) && is_array( $row['features'] ) ) {
			$features = anna_normalize_oasis_text_items( $row['features'] );
		}
		$out[] = array(
			'title'        => $title,
			'price'        => sanitize_text_field( $row['price'] ?? '' ),
			'price_suffix' => sanitize_text_field( $row['price_suffix'] ?? '' ),
			'annual'       => sanitize_text_field( $row['annual'] ?? '' ),
			'founding'     => sanitize_text_field( $row['founding'] ?? '' ),
			'badge'        => sanitize_text_field( $row['badge'] ?? '' ),
			'featured'     => ! empty( $row['featured'] ) ? 1 : 0,
			'features'     => $features,
		);
	}
	return $out;
}

/**
 * Load repeater from theme options.
 *
 * @param string $option_key Option key without prefix.
 * @return array
 */
function anna_get_oasis_repeater_from_options( $option_key ) {
	$full_key  = 'oasis_pg_' . $option_key;
	$defaults  = anna_get_oasis_default_content();
	$saved     = anna_get_option( $full_key, array() );

	if ( is_array( $saved ) && ! empty( $saved ) ) {
		switch ( $option_key ) {
			case 'inside_pill_items':
			case 'ready_items':
				return anna_normalize_oasis_text_items( $saved );
			case 'inside_schedule_items':
				return anna_normalize_oasis_schedule_items( $saved );
			case 'how_card_items':
				return anna_normalize_oasis_how_cards( $saved );
			case 'choose_plan_items':
				return anna_normalize_oasis_plan_items( $saved );
		}
	}

	$default = $defaults[ $option_key ] ?? array();
	switch ( $option_key ) {
		case 'inside_pill_items':
		case 'ready_items':
			return anna_normalize_oasis_text_items( $default );
		case 'inside_schedule_items':
			return anna_normalize_oasis_schedule_items( $default );
		case 'how_card_items':
			return anna_normalize_oasis_how_cards( $default );
		case 'choose_plan_items':
			return anna_normalize_oasis_plan_items( $default );
	}
	return array();
}

/**
 * Get Oasis page content.
 *
 * @return array<string, mixed>
 */
function anna_get_oasis_page_content() {
	$defaults   = anna_get_oasis_default_content();
	$theme_defs = anna_get_default_options();
	$content    = array();

	$repeaters = array(
		'inside_pill_items',
		'inside_schedule_items',
		'how_card_items',
		'choose_plan_items',
		'ready_items',
	);

	$image_keys = array( 'hero_image_id', 'begun_image_id' );
	$textarea_keys = array( 'hero_body', 'what_body', 'begun_body', 'begun_closing', 'inside_body' );

	foreach ( $defaults as $key => $default_value ) {
		$option_key = 'oasis_pg_' . $key;

		if ( in_array( $key, $repeaters, true ) ) {
			$content[ $key ] = anna_get_oasis_repeater_from_options( $key );
			continue;
		}

		$fallback = $theme_defs[ $option_key ] ?? $default_value;
		if ( in_array( $key, $image_keys, true ) ) {
			$content[ $key ] = absint( anna_get_option( $option_key, $fallback ) );
		} else {
			$content[ $key ] = anna_get_option( $option_key, $fallback );
		}
	}

	$post_id = anna_get_current_page_content_id();
	if ( $post_id && function_exists( 'anna_content_get_oasis_page_content' ) ) {
		$saved = anna_content_get_oasis_page_content( $post_id );
		if ( is_array( $saved ) ) {
			$merge = array();
			foreach ( $saved as $key => $value ) {
				if ( is_array( $value ) ) {
					if ( in_array( $key, array( 'inside_pill_items', 'ready_items' ), true ) ) {
						$n = anna_normalize_oasis_text_items( $value );
						if ( ! empty( $n ) ) {
							$merge[ $key ] = $n;
						}
						continue;
					}
					if ( 'inside_schedule_items' === $key ) {
						$n = anna_normalize_oasis_schedule_items( $value );
						if ( ! empty( $n ) ) {
							$merge[ $key ] = $n;
						}
						continue;
					}
					if ( 'how_card_items' === $key ) {
						$n = anna_normalize_oasis_how_cards( $value );
						if ( ! empty( $n ) ) {
							$merge[ $key ] = $n;
						}
						continue;
					}
					if ( 'choose_plan_items' === $key ) {
						$n = anna_normalize_oasis_plan_items( $value );
						if ( ! empty( $n ) ) {
							$merge[ $key ] = $n;
						}
						continue;
					}
					continue;
				}
				if ( '' !== trim( (string) $value ) ) {
					$merge[ $key ] = $value;
				}
			}
			if ( ! empty( $merge ) ) {
				$content = wp_parse_args( $merge, $content );
			}
		}
	}

	return $content;
}

/**
 * Sanitize Oasis options subset for settings save.
 *
 * @param string $key   Option key.
 * @param mixed  $value Raw value.
 * @return mixed
 */
function anna_sanitize_oasis_option( $key, $value ) {
	if ( in_array( $key, array( 'oasis_pg_inside_pill_items', 'oasis_pg_ready_items' ), true ) ) {
		return anna_normalize_oasis_text_items( $value );
	}
	if ( 'oasis_pg_inside_schedule_items' === $key ) {
		return anna_normalize_oasis_schedule_items( $value );
	}
	if ( 'oasis_pg_how_card_items' === $key ) {
		return anna_normalize_oasis_how_cards( $value );
	}
	if ( 'oasis_pg_choose_plan_items' === $key ) {
		return anna_normalize_oasis_plan_items( $value );
	}
	if ( in_array( $key, array( 'oasis_pg_hero_image_id', 'oasis_pg_begun_image_id' ), true ) ) {
		return absint( $value );
	}
	if ( in_array( $key, array( 'oasis_pg_hero_button_url' ), true ) ) {
		return esc_url_raw( $value );
	}
	$textarea_keys = array(
		'oasis_pg_hero_body',
		'oasis_pg_what_body',
		'oasis_pg_begun_body',
		'oasis_pg_begun_closing',
		'oasis_pg_begun_callout_body',
		'oasis_pg_inside_body',
		'oasis_pg_how_intro',
		'oasis_pg_choose_footer',
	);
	if ( in_array( $key, $textarea_keys, true ) ) {
		return sanitize_textarea_field( $value );
	}
	if ( in_array( $key, array( 'oasis_pg_hero_heading' ), true ) ) {
		return sanitize_textarea_field( $value );
	}
	return sanitize_text_field( $value );
}
