<?php
/**
 * MOVE page helpers.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default MOVE page content (from move-master-design.png).
 *
 * @return array<string, mixed>
 */
function anna_get_move_default_content() {
	return array(
		'hero_eyebrow'  => 'M.O.V.E',
		'hero_heading'  => 'Where it all began.',
		'hero_image_id' => 0,

		'evolution_heading'         => 'Opening — the evolution',
		'evolution_body'            => "For four years I guided women through deep, structured transformation inside M.O.V.E — a four month coaching journey built on movement, nature, variety and education.\n\nThe results were profound. The connections were lasting. And what I witnessed in those women — the courage, the breakthroughs, the genuine change — became the foundation of everything I now build.",
		'evolution_callout'         => 'M.O.V.E has evolved into OASIS. An expansion of everything M.O.V.E stood for — taken deeper, made ongoing and opened up to more women.',
		'evolution_gallery_heading' => 'Moments from past M.O.V.E journeys',
		'evolution_gallery_items'   => array(),

		'was_heading' => 'What M.O.V.E was',
		'was_body'    => "M.O.V.E was a four month group coaching program for women. Small groups. Deep work. Real transformation.\n\nInside M.O.V.E, women strengthened emotional regulation, built sustainable wellbeing habits, developed confident boundaries, improved their mental and physical health and reconnected with who they truly are.\n\nThe program combined group coaching sessions, guided movement in nature, workshops and integration sessions, personal reflection tools, 1-1 coaching support and a small supportive community.\n\nWomen left M.O.V.E feeling clearer, calmer, stronger, more self-assured and more energised. Not temporarily motivated — genuinely changed.",

		'said_heading' => 'What women said',
		'said_items'   => array(
			array( 'quote' => 'So worth every drop — I finally prioritized myself without guilt.' ),
			array( 'quote' => 'I stopped reacting emotionally and started responding calmly.' ),
			array( 'quote' => 'One of the best things I have ever done for myself.' ),
			array( 'quote' => 'Truly transformative.' ),
		),

		'reviews_eyebrow' => 'What Clients Say',
		'reviews_heading' => '102 five-star Google reviews.',
		'reviews_summary' => '5.0 - Google Reviews - based on 102 reviews',
		'reviews_items'   => array(
			array(
				'quote'  => 'Working with Anna has been one of the best things I could do for myself and my family. The most transformative part of our work was understanding how my inner child was influencing my daily reactions. Anna provided such a safe, supportive space to explore this.',
				'name'   => 'Renee Berger',
				'role'   => 'a year ago',
				'rating' => 5,
			),
			array(
				'quote'  => 'Anna has been a truly remarkable coach and always shows up for you and validates your feelings. As a result of working with her I am more in touch with my feelings, have a better relationship with my wife, am more comfortable in my own skin and overall happier with life.',
				'name'   => 'Sarah Clarke',
				'role'   => 'a year ago',
				'rating' => 5,
			),
			array(
				'quote'  => 'I have been in the mental health system for years. Not even the meds they so quickly handed out compares to the progress I made with Anna. She is able to help you make peace with the past. If you are someone with any kind of emotional trauma I 100% highly recommend Anna.',
				'name'   => 'Laura Hodges',
				'role'   => 'a year ago',
				'rating' => 5,
			),
		),

		'pillars_heading' => 'The four pillars of M.O.V.E',
		'pillar_items'    => array(
			array(
				'title' => 'Movement',
				'body'  => "Movement has always been a fundamental part of my life. It allowed me to develop body awareness, maintain physical and mental health, connect with my heart and feel invigorated and alive. I experienced some of my most challenging moments while moving through my athletic career and they helped me grow. My most creative moments arise when the energy of nature flows through me, regenerating, healing and inspiring me.\n\nMovement improves organ function, boosts immunity, strengthens the cardiovascular and respiratory systems, increases bone density, tones and strengthens muscles and releases endorphins providing energy and mental clarity. In the M.O.V.E sessions, we got energy flowing, engaging all the senses while connecting with nature and each other.",
			),
			array(
				'title' => 'Outdoors',
				'body'  => "Nature has always drawn me in. I feel most at home, most connected and most free to help others when we are outdoors. Our current way of life has distanced us from nature keeping us indoors, depriving us of sunlight and fresh air. In M.O.V.E we returned to our roots, learned from and in nature, and healed through grounding, breathing, being present and engaging with all five senses.",
			),
			array(
				'title' => 'Variety',
				'body'  => 'Variety is the spice of life. It keeps us engaged, motivated and fosters genuine growth. Often we find ourselves trapped in monotonous routines feeling stagnant, unmotivated and uninspired. In M.O.V.E we infused variety into every session expanding awareness, igniting creativity and engaging the mind, body and spirit through a change of locations, a range of topics and mindfulness exercises.',
			),
			array(
				'title' => 'Education',
				'body'  => "In the M.O.V.E sessions we embarked on what I call Education for Life exploring the Tree of Life together. Values, beliefs, nutrition, emotions, purpose, routines, rituals, boundaries, relationships and so much more. This comprehensive exploration equipped women with the insights and awareness to make choices that aligned with their values and reflected the person they were becoming.\n\nLoving yourself, letting go and looking within. Learning the importance of emotions, how to release the energies they store in our bodies and how to remove the blocks from our past to allow energy to flow.",
			),
		),
		'pillars_body' => '',

		'evolved_heading'               => 'M.O.V.E has evolved into Oasis',
		'evolved_body'                  => 'Everything M.O.V.E stood for is alive inside Oasis — and it goes further. More depth. More consistency. More connection. An ongoing community for women who want to keep doing the real work, season after season.',
		'evolved_button_primary_text'   => 'Explore Oasis',
		'evolved_button_primary_url'    => '/oasis/',
		'evolved_button_secondary_text' => 'Join the Waitlist',
		'evolved_button_secondary_url'  => '/oasis/#oasis-waitlist',
		'evolved_button_tertiary_text'  => 'More info about it',
		'evolved_button_tertiary_url'   => '/oasis/',
	);
}

/**
 * @return array<string, mixed>
 */
function anna_get_move_theme_option_defaults() {
	$out = array();
	foreach ( anna_get_move_default_content() as $key => $value ) {
		$out[ 'move_pg_' . $key ] = $value;
	}
	return $out;
}

/**
 * @return array<string, string>
 */
function anna_get_move_page_option_map() {
	$map = array();
	foreach ( array_keys( anna_get_move_default_content() ) as $key ) {
		$map[ $key ] = 'move_pg_' . $key;
	}
	return $map;
}

/**
 * @param mixed $items Raw gallery rows.
 * @return array<int, array{image_id:int}>
 */
function anna_normalize_move_gallery_items( $items ) {
	if ( ! is_array( $items ) ) {
		return array();
	}

	$out = array();
	foreach ( $items as $row ) {
		$image_id = is_array( $row ) ? absint( $row['image_id'] ?? 0 ) : absint( $row );
		if ( $image_id > 0 ) {
			$out[] = array( 'image_id' => $image_id );
		}
	}
	return $out;
}

/**
 * @param mixed $items Raw quote rows.
 * @return array<int, array{quote:string}>
 */
function anna_normalize_move_quote_items( $items ) {
	if ( ! is_array( $items ) ) {
		return array();
	}

	$out = array();
	foreach ( $items as $row ) {
		$quote = is_string( $row ) ? $row : ( is_array( $row ) ? (string) ( $row['quote'] ?? '' ) : '' );
		$quote = sanitize_textarea_field( $quote );
		if ( '' !== trim( $quote ) ) {
			$out[] = array( 'quote' => $quote );
		}
	}
	return $out;
}

/**
 * @param mixed $items Raw pillar rows.
 * @return array<int, array{title:string,body:string}>
 */
function anna_normalize_move_pillar_items( $items ) {
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
		$out[] = array(
			'title' => $title,
			'body'  => sanitize_textarea_field( $row['body'] ?? '' ),
		);
	}
	return $out;
}

/**
 * @param mixed $items Raw review rows.
 * @return array<int, array{quote:string,name:string,role:string,rating:int}>
 */
function anna_normalize_move_review_items( $items ) {
	if ( ! is_array( $items ) ) {
		return array();
	}

	$out = array();
	foreach ( $items as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$quote = sanitize_textarea_field( $row['quote'] ?? '' );
		if ( '' === trim( $quote ) ) {
			continue;
		}
		$out[] = array(
			'quote'  => $quote,
			'name'   => sanitize_text_field( $row['name'] ?? '' ),
			'role'   => sanitize_text_field( $row['role'] ?? '' ),
			'rating' => max( 1, min( 5, absint( $row['rating'] ?? 5 ) ) ),
		);
	}
	return $out;
}

/**
 * @param string $option_key Key without prefix.
 * @return array
 */
function anna_get_move_repeater_from_options( $option_key ) {
	$full_key = 'move_pg_' . $option_key;
	$defaults = anna_get_move_default_content();
	$saved    = anna_get_option( $full_key, array() );

	if ( is_array( $saved ) && ! empty( $saved ) ) {
		switch ( $option_key ) {
			case 'evolution_gallery_items':
				return anna_normalize_move_gallery_items( $saved );
			case 'said_items':
				return anna_normalize_move_quote_items( $saved );
			case 'pillar_items':
				return anna_normalize_move_pillar_items( $saved );
			case 'reviews_items':
				return anna_normalize_move_review_items( $saved );
		}
	}

	$default = $defaults[ $option_key ] ?? array();
	switch ( $option_key ) {
		case 'evolution_gallery_items':
			return anna_normalize_move_gallery_items( $default );
		case 'said_items':
			return anna_normalize_move_quote_items( $default );
		case 'pillar_items':
			return anna_normalize_move_pillar_items( $default );
		case 'reviews_items':
			return anna_normalize_move_review_items( $default );
	}
	return array();
}

/**
 * Get merged MOVE page content.
 *
 * @return array<string, mixed>
 */
function anna_get_move_page_content() {
	$defaults   = anna_get_move_default_content();
	$theme_defs = anna_get_default_options();
	$content    = array();

	$repeaters  = array( 'evolution_gallery_items', 'said_items', 'pillar_items', 'reviews_items' );
	$image_keys = array( 'hero_image_id' );

	foreach ( $defaults as $key => $default_value ) {
		$option_key = 'move_pg_' . $key;

		if ( in_array( $key, $repeaters, true ) ) {
			$content[ $key ] = anna_get_move_repeater_from_options( $key );
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
	if ( $post_id && function_exists( 'anna_content_get_move_page_content' ) ) {
		$saved = anna_content_get_move_page_content( $post_id );
		if ( is_array( $saved ) ) {
			$merge = array();
			foreach ( $saved as $key => $value ) {
				if ( is_array( $value ) ) {
					$normalized = array();
					switch ( $key ) {
						case 'evolution_gallery_items':
							$normalized = anna_normalize_move_gallery_items( $value );
							break;
						case 'said_items':
							$normalized = anna_normalize_move_quote_items( $value );
							break;
						case 'pillar_items':
							$normalized = anna_normalize_move_pillar_items( $value );
							break;
						case 'reviews_items':
							$normalized = anna_normalize_move_review_items( $value );
							break;
					}
					if ( ! empty( $normalized ) ) {
						$merge[ $key ] = $normalized;
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
 * Sanitize MOVE theme options.
 *
 * @param string $key   Option key.
 * @param mixed  $value Raw value.
 * @return mixed
 */
function anna_sanitize_move_option( $key, $value ) {
	if ( 'move_pg_evolution_gallery_items' === $key ) {
		return anna_normalize_move_gallery_items( $value );
	}
	if ( 'move_pg_said_items' === $key ) {
		return anna_normalize_move_quote_items( $value );
	}
	if ( 'move_pg_pillar_items' === $key ) {
		return anna_normalize_move_pillar_items( $value );
	}
	if ( 'move_pg_reviews_items' === $key ) {
		return anna_normalize_move_review_items( $value );
	}
	if ( 'move_pg_hero_image_id' === $key ) {
		return absint( $value );
	}

	$url_keys = array(
		'move_pg_evolved_button_primary_url',
		'move_pg_evolved_button_secondary_url',
		'move_pg_evolved_button_tertiary_url',
	);
	if ( in_array( $key, $url_keys, true ) ) {
		return esc_url_raw( $value );
	}

	$textarea_keys = array(
		'move_pg_evolution_body',
		'move_pg_evolution_callout',
		'move_pg_was_body',
		'move_pg_pillars_body',
		'move_pg_evolved_body',
	);
	if ( in_array( $key, $textarea_keys, true ) ) {
		return sanitize_textarea_field( $value );
	}

	return sanitize_text_field( $value );
}
