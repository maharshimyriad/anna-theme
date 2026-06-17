<?php
/**
 * Yoast SEO post_content synchronisation.
 *
 * Yoast analyses post_content for word count, keyphrase density, and
 * readability scores. Because this theme stores all page copy in custom
 * post meta rather than post_content, Yoast reports 0 words on every
 * managed page.
 *
 * This trait collects all human-readable text fields from whichever custom
 * meta array belongs to the saved page, strips non-content fields (image IDs,
 * URLs, shortcodes, button labels that carry no prose) and writes the result
 * into post_content so Yoast can do its analysis.
 *
 * IMPORTANT — this does NOT change how the frontend works. Templates still
 * read exclusively from post meta via the existing helper functions. The
 * post_content value is purely for Yoast (and the WP search index).
 *
 * Loop-prevention: wp_update_post() fires save_post_page again. We guard
 * against infinite recursion with a static flag that is set before the call
 * and checked at the top of the sync entry-point.
 *
 * @package Anna_Content_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Anna_Yoast_Sync {

	// -------------------------------------------------------------------------
	// Public entry point
	// -------------------------------------------------------------------------

	/**
	 * Register the Yoast content analysis override filter.
	 *
	 * Hooked on init so it is registered early enough for Yoast to pick up.
	 * Yoast calls wpseo_post_content_analysis_override before running its
	 * analysis, passing the current post object. We return the same plain-text
	 * string that we write into post_content on save — so Yoast always analyses
	 * fresh meta content even when the classic editor is hidden or removed.
	 */
	public function register_yoast_content_filter() {
		add_filter(
			'wpseo_post_content_analysis_override',
			array( $this, 'provide_yoast_analysis_content' ),
			10,
			2
		);
	}

	/**
	 * Supply the collected meta text to Yoast's analysis engine.
	 *
	 * @param string|null $override Existing override, if any.
	 * @param WP_Post     $post     Post being analysed.
	 * @return string
	 */
	public function provide_yoast_analysis_content( $override, $post ) {
		if ( ! $post instanceof WP_Post || 'page' !== $post->post_type ) {
			return $override;
		}

		$parts = $this->collect_text_parts_for_post( $post->ID );
		if ( empty( $parts ) ) {
			return $override;
		}

		$combined = implode( "\n\n", array_filter( $parts, 'strlen' ) );
		$combined = wp_strip_all_tags( $combined );
		$combined = html_entity_decode( $combined, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$combined = trim( $combined );

		return '' !== $combined ? $combined : $override;
	}

	/**
	 * Collect all textual content for the given page and write it to post_content.
	 *
	 * Called from save_page_content() after all meta has been saved, so we are
	 * always reading freshly committed data.
	 *
	 * @param int $post_id Page ID.
	 */
	public function sync_post_content_for_yoast( $post_id ) {
		// Prevent the wp_update_post() call below from triggering another sync.
		static $syncing = false;
		if ( $syncing ) {
			return;
		}

		$post_id = absint( $post_id );
		if ( ! $post_id ) {
			return;
		}

		$parts = $this->collect_text_parts_for_post( $post_id );

		if ( empty( $parts ) ) {
			return;
		}

		// Join sections with blank lines so Yoast sees sentence breaks and
		// paragraph structure, which helps readability scoring.
		$combined = implode( "\n\n", array_filter( $parts, 'strlen' ) );
		$combined = wp_strip_all_tags( $combined );
		$combined = html_entity_decode( $combined, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$combined = trim( $combined );

		if ( '' === $combined ) {
			return;
		}

		// Unhook save_post_page temporarily so our update does not re-trigger
		// the full save pipeline (the static flag above is a second safety net).
		$syncing = true;
		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_content' => $combined,
			)
		);
		$syncing = false;
	}

	// -------------------------------------------------------------------------
	// Dispatcher — picks the right collector per page type
	// -------------------------------------------------------------------------

	/**
	 * Return an array of plain-text strings, one per logical section.
	 * Each string may contain newlines separating fields within a section.
	 *
	 * @param int $post_id Page ID.
	 * @return string[]
	 */
	private function collect_text_parts_for_post( $post_id ) {
		$parts = array();

		// --- Home page ---
		// Home content is stored by the theme in _anna_content_home_page as a
		// nested array keyed by section name (hero, intro, services, about, …).
		if ( function_exists( 'anna_is_home_content_page' ) && anna_is_home_content_page( $post_id ) ) {
			return $this->collect_home_page_text( $post_id );
		}

		// --- Fixed page types managed by the content-manager plugin ---
		$template = get_page_template_slug( $post_id );
		$slug     = get_post_field( 'post_name', $post_id );

		// About page.
		if ( 'about' === $slug || 'page-about.php' === $template ) {
			$data = get_post_meta( $post_id, '_anna_content_about_page', true );
			if ( is_array( $data ) ) {
				$parts = array_merge( $parts, $this->collect_about_page_text( $data ) );
			}
		}

		// Coaching page.
		if ( 'coaching' === $slug || 'page-coaching.php' === $template ) {
			$data = get_post_meta( $post_id, '_anna_content_coaching_page', true );
			if ( is_array( $data ) ) {
				$parts = array_merge( $parts, $this->collect_coaching_page_text( $data ) );
			}
		}

		// Oasis page.
		if ( 'oasis' === $slug || 'page-oasis.php' === $template ) {
			$data = get_post_meta( $post_id, '_anna_content_oasis_page', true );
			if ( is_array( $data ) ) {
				$parts = array_merge( $parts, $this->collect_oasis_page_text( $data ) );
			}
		}

		// Speaking page.
		if ( 'speaking' === $slug || 'page-speaking.php' === $template ) {
			$data = get_post_meta( $post_id, '_anna_content_speaking_page', true );
			if ( is_array( $data ) ) {
				$parts = array_merge( $parts, $this->collect_speaking_page_text( $data ) );
			}
		}

		// Mental Health Support page.
		if ( 'mental-health-support' === $slug || 'page-mental-health-support.php' === $template ) {
			$data = get_post_meta( $post_id, '_anna_content_mhs_page', true );
			if ( is_array( $data ) ) {
				$parts = array_merge( $parts, $this->collect_mhs_page_text( $data ) );
			}
		}

		// MOVE page.
		if ( 'move' === $slug || 'page-move.php' === $template ) {
			$data = get_post_meta( $post_id, '_anna_content_move_page', true );
			if ( is_array( $data ) ) {
				$parts = array_merge( $parts, $this->collect_move_page_text( $data ) );
			}
		}

		// Reviews page.
		if ( 'reviews' === $slug || 'page-reviews.php' === $template ) {
			$data = get_post_meta( $post_id, '_anna_content_reviews_page', true );
			if ( is_array( $data ) ) {
				$parts = array_merge( $parts, $this->collect_reviews_page_text( $data ) );
			}
		}

		// Contact page.
		if ( 'contact' === $slug || 'what-is-a-life-coach' === $slug || 'page-contact.php' === $template ) {
			$data = get_post_meta( $post_id, '_anna_content_contact_page', true );
			if ( is_array( $data ) ) {
				$parts = array_merge( $parts, $this->collect_contact_page_text( $data ) );
			}
		}

		// Blog page.
		if ( 'blog' === $slug || 'page-blog.php' === $template ) {
			$data = get_post_meta( $post_id, '_anna_content_blog_page', true );
			if ( is_array( $data ) ) {
				$parts = array_merge( $parts, $this->collect_blog_page_text( $data ) );
			}
		}

		// --- Scaffolded / flexible pages ---
		// These use a dynamic meta key: _anna_content_{code}_page.
		// We detect them via anna_get_flexible_page_config() and collect
		// text/textarea fields only (media and url fields are excluded by
		// the scaffolded collector which reads field type from config).
		if ( empty( $parts ) && function_exists( 'anna_get_flexible_page_config' ) ) {
			$config = anna_get_flexible_page_config( $post_id );
			if ( $config ) {
				$code = $config['code'] ?? '';
				if ( $code ) {
					$data = get_post_meta( $post_id, '_anna_content_' . $code . '_page', true );
					if ( is_array( $data ) ) {
						$parts = array_merge( $parts, $this->collect_scaffolded_page_text( $data, $post_id, $config ) );
					}
				}
			}
		}

		return $parts;
	}


	// -------------------------------------------------------------------------
	// Per-page-type text collectors
	// -------------------------------------------------------------------------

	/**
	 * Home page — nested array structure: $data['hero'], $data['intro'], etc.
	 *
	 * @param int $post_id Page ID.
	 * @return string[]
	 */
	private function collect_home_page_text( $post_id ) {
		$data = get_post_meta( $post_id, '_anna_content_home_page', true );
		if ( ! is_array( $data ) ) {
			return array();
		}

		$parts = array();

		// Fields to pull from each home section (excludes _id, _url keys and shortcodes).
		$section_fields = array(
			'hero'         => array( 'eyebrow', 'heading', 'description', 'trust_text',
				'stat_1_value', 'stat_1_label', 'stat_2_value', 'stat_2_label', 'stat_3_value', 'stat_3_label' ),
			'intro'        => array( 'intro_eyebrow', 'intro_heading', 'intro_body', 'intro_quote',
				'intro_quote_cite', 'recognition_eyebrow', 'recognition_heading', 'recognition_description',
				'recognition_items_text' ),
			'services'     => array( 'eyebrow', 'heading', 'description',
				'card_1_title', 'card_1_excerpt', 'card_2_title', 'card_2_excerpt',
				'card_3_title', 'card_3_excerpt' ),
			'about'        => array( 'eyebrow', 'heading', 'body', 'quote', 'badge_number',
				'badge_text', 'expertise_text' ),
			'testimonials' => array( 'eyebrow', 'heading', 'summary' ),
			'cta'          => array( 'eyebrow', 'heading', 'description', 'trust_text' ),
		);

		foreach ( $section_fields as $section => $keys ) {
			if ( ! isset( $data[ $section ] ) || ! is_array( $data[ $section ] ) ) {
				continue;
			}
			$section_text = $this->pluck_text_values( $data[ $section ], $keys );
			if ( $section_text ) {
				$parts[] = $section_text;
			}
		}

		return $parts;
	}

	/**
	 * About page — meta key _anna_content_about_page.
	 *
	 * @param array $data Saved meta array.
	 * @return string[]
	 */
	private function collect_about_page_text( $data ) {
		$scalar_keys = array(
			'hero_eyebrow', 'hero_description', 'hero_subheading',
			'story_eyebrow', 'rock_heading',
			'coach_eyebrow', 'coach_title',
			'work_eyebrow', 'work_heading',
			'work_card_1_title', 'work_card_1_body',
			'work_card_2_title', 'work_card_2_body',
			'work_card_3_title', 'work_card_3_body',
			'work_card_4_title', 'work_card_4_body',
			'people_eyebrow', 'people_heading', 'people_body',
			'connect_eyebrow', 'connect_heading',
			// Textarea / HTML fields.
			'hero_heading', 'story_heading', 'story_body',
			'rock_left_body', 'rock_right_body', 'coach_body', 'work_body',
		);

		$parts = array( $this->pluck_text_values( $data, $scalar_keys ) );

		// people_items repeater: each row has initials, title, org.
		if ( ! empty( $data['people_items'] ) && is_array( $data['people_items'] ) ) {
			foreach ( $data['people_items'] as $item ) {
				if ( ! is_array( $item ) ) {
					continue;
				}
				$parts[] = $this->pluck_text_values( $item, array( 'initials', 'title', 'org' ) );
			}
		}

		return array_filter( $parts, 'strlen' );
	}

	/**
	 * Coaching page — meta key _anna_content_coaching_page.
	 *
	 * @param array $data Saved meta array.
	 * @return string[]
	 */
	private function collect_coaching_page_text( $data ) {
		$scalar_keys = array(
			'hero_eyebrow', 'hero_heading', 'hero_description',
			'what_eyebrow', 'what_heading', 'what_body', 'what_card_heading',
			'pillars_eyebrow', 'pillars_heading',
			'work_eyebrow', 'work_heading', 'work_gains_heading',
			'expect_eyebrow', 'expect_heading_line1', 'expect_heading_line2',
			'expect_body', 'expect_quote',
			'faq_heading',
		);

		$parts = array( $this->pluck_text_values( $data, $scalar_keys ) );

		// what_card_items repeater — each item is a string or array with 'text'.
		$parts[] = $this->collect_simple_text_repeater( $data['what_card_items'] ?? array() );

		// pillar_items repeater — each item has title, body.
		if ( ! empty( $data['pillar_items'] ) && is_array( $data['pillar_items'] ) ) {
			foreach ( $data['pillar_items'] as $item ) {
				if ( is_array( $item ) ) {
					$parts[] = $this->pluck_text_values( $item, array( 'title', 'body', 'text' ) );
				}
			}
		}

		// work_topics_items and work_gains_items — simple text arrays.
		$parts[] = $this->collect_simple_text_repeater( $data['work_topics_items'] ?? array() );
		$parts[] = $this->collect_simple_text_repeater( $data['work_gains_items'] ?? array() );

		// expect_info_cards — each item has title, body.
		if ( ! empty( $data['expect_info_cards'] ) && is_array( $data['expect_info_cards'] ) ) {
			foreach ( $data['expect_info_cards'] as $item ) {
				if ( is_array( $item ) ) {
					$parts[] = $this->pluck_text_values( $item, array( 'title', 'body' ) );
				}
			}
		}

		// faq_items — each item has question, answer.
		$parts[] = $this->collect_faq_repeater( $data['faq_items'] ?? array() );

		return array_filter( $parts, 'strlen' );
	}


	/**
	 * Oasis page — meta key _anna_content_oasis_page.
	 *
	 * @param array $data Saved meta array.
	 * @return string[]
	 */
	private function collect_oasis_page_text( $data ) {
		$scalar_keys = array(
			'hero_breadcrumb', 'hero_heading', 'hero_subheading', 'hero_body', 'hero_button_text',
			'what_eyebrow', 'what_heading', 'what_body', 'what_footer_line',
			'begun_eyebrow', 'begun_heading', 'begun_subheading', 'begun_body',
			'begun_quote', 'begun_closing', 'begun_callout_label', 'begun_callout_body', 'begun_link_text',
			'inside_eyebrow', 'inside_heading', 'inside_body', 'inside_highlight', 'inside_pills_intro',
			'how_eyebrow', 'how_heading', 'how_intro', 'how_footer',
			'choose_eyebrow', 'choose_heading', 'choose_intro', 'choose_footer',
			'ready_eyebrow', 'ready_heading',
			'waitlist_eyebrow', 'waitlist_heading', 'waitlist_button_text',
			'faq_heading',
		);

		$parts = array( $this->pluck_text_values( $data, $scalar_keys ) );

		// inside_pill_items — simple text strings.
		$parts[] = $this->collect_simple_text_repeater( $data['inside_pill_items'] ?? array() );

		// inside_schedule_items — each item may have label, time, or text.
		if ( ! empty( $data['inside_schedule_items'] ) && is_array( $data['inside_schedule_items'] ) ) {
			foreach ( $data['inside_schedule_items'] as $item ) {
				if ( is_array( $item ) ) {
					$parts[] = $this->pluck_text_values( $item, array( 'label', 'time', 'text', 'title', 'body' ) );
				}
			}
		}

		// how_card_items — each item may have title, body, step.
		if ( ! empty( $data['how_card_items'] ) && is_array( $data['how_card_items'] ) ) {
			foreach ( $data['how_card_items'] as $item ) {
				if ( is_array( $item ) ) {
					$parts[] = $this->pluck_text_values( $item, array( 'title', 'body', 'step', 'text' ) );
				}
			}
		}

		// choose_plan_items — each item may have title, price, body, features (array).
		if ( ! empty( $data['choose_plan_items'] ) && is_array( $data['choose_plan_items'] ) ) {
			foreach ( $data['choose_plan_items'] as $item ) {
				if ( ! is_array( $item ) ) {
					continue;
				}
				$parts[] = $this->pluck_text_values( $item, array( 'title', 'price', 'body', 'label', 'text' ) );
				// features is itself a nested array of strings.
				if ( ! empty( $item['features'] ) && is_array( $item['features'] ) ) {
					$parts[] = $this->collect_simple_text_repeater( $item['features'] );
				}
			}
		}

		// ready_items — simple text strings.
		$parts[] = $this->collect_simple_text_repeater( $data['ready_items'] ?? array() );

		// faq_items — question + answer.
		$parts[] = $this->collect_faq_repeater( $data['faq_items'] ?? array() );

		return array_filter( $parts, 'strlen' );
	}

	/**
	 * Speaking page — meta key _anna_content_speaking_page.
	 *
	 * @param array $data Saved meta array.
	 * @return string[]
	 */
	private function collect_speaking_page_text( $data ) {
		$scalar_keys = array(
			'hero_eyebrow', 'hero_heading', 'hero_body', 'hero_button_text', 'hero_secondary_text',
			'bring_eyebrow', 'bring_heading_line1', 'bring_heading_line2', 'bring_body', 'bring_quote',
			'topics_eyebrow', 'topics_heading', 'topics_body',
			'formats_eyebrow', 'formats_heading', 'formats_body', 'formats_audience_heading',
			'takeaway_eyebrow', 'takeaway_heading',
			'testimonials_eyebrow', 'testimonials_heading',
			'cta_eyebrow', 'cta_heading', 'cta_body', 'cta_button_text',
		);

		$parts = array( $this->pluck_text_values( $data, $scalar_keys ) );

		// hero_stat_items — each item has value, label.
		if ( ! empty( $data['hero_stat_items'] ) && is_array( $data['hero_stat_items'] ) ) {
			foreach ( $data['hero_stat_items'] as $item ) {
				if ( is_array( $item ) ) {
					$parts[] = $this->pluck_text_values( $item, array( 'value', 'label' ) );
				}
			}
		}

		// topics_card_items — each item has title, body.
		if ( ! empty( $data['topics_card_items'] ) && is_array( $data['topics_card_items'] ) ) {
			foreach ( $data['topics_card_items'] as $item ) {
				if ( is_array( $item ) ) {
					$parts[] = $this->pluck_text_values( $item, array( 'title', 'body', 'text' ) );
				}
			}
		}

		// formats_card_items — each item has number, title, body.
		if ( ! empty( $data['formats_card_items'] ) && is_array( $data['formats_card_items'] ) ) {
			foreach ( $data['formats_card_items'] as $item ) {
				if ( is_array( $item ) ) {
					$parts[] = $this->pluck_text_values( $item, array( 'number', 'title', 'body' ) );
				}
			}
		}

		// formats_audience_items and takeaway_items — simple text.
		$parts[] = $this->collect_simple_text_repeater( $data['formats_audience_items'] ?? array() );
		$parts[] = $this->collect_simple_text_repeater( $data['takeaway_items'] ?? array() );

		return array_filter( $parts, 'strlen' );
	}


	/**
	 * Mental Health Support page — meta key _anna_content_mhs_page.
	 *
	 * @param array $data Saved meta array.
	 * @return string[]
	 */
	private function collect_mhs_page_text( $data ) {
		$scalar_keys = array(
			'hero_eyebrow', 'hero_heading',
			'opening_heading', 'opening_body',
			'programs_heading', 'programs_body',
			'inner_heading', 'inner_body',
			'work_heading', 'work_body',
			'practice_heading', 'practice_body', 'practice_link_text',
			'ready_heading', 'ready_subheading', 'ready_body',
			'ready_button_primary_text', 'ready_button_secondary_text', 'ready_button_tertiary_text',
		);

		return array_filter(
			array( $this->pluck_text_values( $data, $scalar_keys ) ),
			'strlen'
		);
	}

	/**
	 * MOVE page — meta key _anna_content_move_page.
	 *
	 * @param array $data Saved meta array.
	 * @return string[]
	 */
	private function collect_move_page_text( $data ) {
		$scalar_keys = array(
			'hero_eyebrow', 'hero_heading',
			'evolution_heading', 'evolution_body', 'evolution_callout', 'evolution_gallery_heading',
			'was_heading', 'was_body',
			'said_heading',
			'reviews_eyebrow', 'reviews_heading', 'reviews_summary', 'reviews_cta_text',
			'pillars_heading',
			'evolved_heading', 'evolved_body',
			'evolved_button_primary_text', 'evolved_button_secondary_text', 'evolved_button_tertiary_text',
		);

		$parts = array( $this->pluck_text_values( $data, $scalar_keys ) );

		// said_items — each item has a 'quote' string.
		if ( ! empty( $data['said_items'] ) && is_array( $data['said_items'] ) ) {
			foreach ( $data['said_items'] as $item ) {
				if ( is_array( $item ) && ! empty( $item['quote'] ) ) {
					$parts[] = (string) $item['quote'];
				} elseif ( is_string( $item ) ) {
					$parts[] = $item;
				}
			}
		}

		// pillar_items — each item may have title, body.
		if ( ! empty( $data['pillar_items'] ) && is_array( $data['pillar_items'] ) ) {
			foreach ( $data['pillar_items'] as $item ) {
				if ( is_array( $item ) ) {
					$parts[] = $this->pluck_text_values( $item, array( 'title', 'body', 'text' ) );
				}
			}
		}

		// reviews_items — each item may have author, body, text.
		if ( ! empty( $data['reviews_items'] ) && is_array( $data['reviews_items'] ) ) {
			foreach ( $data['reviews_items'] as $item ) {
				if ( is_array( $item ) ) {
					$parts[] = $this->pluck_text_values( $item, array( 'author', 'body', 'text', 'quote' ) );
				}
			}
		}

		return array_filter( $parts, 'strlen' );
	}

	/**
	 * Reviews page — meta key _anna_content_reviews_page.
	 *
	 * @param array $data Saved meta array.
	 * @return string[]
	 */
	private function collect_reviews_page_text( $data ) {
		$scalar_keys = array(
			'hero_eyebrow', 'hero_heading', 'hero_rating_text',
			'google_reviews_text',
			'cta_heading', 'cta_body', 'cta_button_text',
		);

		return array_filter(
			array( $this->pluck_text_values( $data, $scalar_keys ) ),
			'strlen'
		);
	}

	/**
	 * Contact page — meta key _anna_content_contact_page.
	 *
	 * @param array $data Saved meta array.
	 * @return string[]
	 */
	private function collect_contact_page_text( $data ) {
		$scalar_keys = array(
			'hero_eyebrow', 'hero_heading',
			'info_heading',
			'cta_card_heading', 'cta_card_body', 'cta_card_button_text',
			'form_heading', 'form_button_text', 'form_response_note',
		);

		return array_filter(
			array( $this->pluck_text_values( $data, $scalar_keys ) ),
			'strlen'
		);
	}

	/**
	 * Blog page — meta key _anna_content_blog_page.
	 *
	 * @param array $data Saved meta array.
	 * @return string[]
	 */
	private function collect_blog_page_text( $data ) {
		$scalar_keys = array(
			'hero_heading', 'hero_description',
			'section_heading', 'section_subtext',
		);

		return array_filter(
			array( $this->pluck_text_values( $data, $scalar_keys ) ),
			'strlen'
		);
	}

	/**
	 * Scaffolded / flexible pages — meta key _anna_content_{code}_page.
	 *
	 * Reads the field type from the page config to distinguish text/textarea
	 * (include) from media/url/select (exclude).
	 *
	 * @param array $data    Saved meta array.
	 * @param int   $post_id Post ID.
	 * @param array $config  Page config from anna_get_flexible_page_config().
	 * @return string[]
	 */
	private function collect_scaffolded_page_text( $data, $post_id, $config ) {
		$parts = array();

		$sections = function_exists( 'anna_get_page_sections_for_post' )
			? anna_get_page_sections_for_post( $post_id )
			: ( $config['sections'] ?? array() );

		foreach ( $sections as $section ) {
			$section_values = array();
			foreach ( $section['fields'] as $key => $field ) {
				$type = $field['type'] ?? 'text';
				// Only include human-readable text fields.
				if ( in_array( $type, array( 'text', 'textarea' ), true ) ) {
					$value = isset( $data[ $key ] ) ? (string) $data[ $key ] : '';
					// Skip the 'empty--' sentinel used to intentionally blank fields.
					if ( '' !== $value && 'empty--' !== trim( $value ) ) {
						$section_values[] = $value;
					}
				}
			}
			if ( ! empty( $section_values ) ) {
				$parts[] = implode( "\n", $section_values );
			}
		}

		return $parts;
	}


	// -------------------------------------------------------------------------
	// Shared helpers
	// -------------------------------------------------------------------------

	/**
	 * Pull a list of named keys from a flat array, skip empty or sentinel values,
	 * and join them with newlines into a single string.
	 *
	 * Non-content values that are intentionally filtered out:
	 *   - Numeric-only strings (image attachment IDs stored as text).
	 *   - Values that look like URLs (http/https/# prefix).
	 *   - The 'empty--' sentinel used by this theme to blank out a field.
	 *   - Shortcode-like strings (start with '[').
	 *
	 * @param array    $data Array to pull from.
	 * @param string[] $keys Keys to collect.
	 * @return string
	 */
	private function pluck_text_values( $data, $keys ) {
		$lines = array();
		foreach ( $keys as $key ) {
			if ( ! isset( $data[ $key ] ) ) {
				continue;
			}

			$value = (string) $data[ $key ];
			if ( $this->yoast_sync_is_non_content( $value ) ) {
				continue;
			}

			$lines[] = $value;
		}
		return implode( "\n", $lines );
	}

	/**
	 * Collect text from a simple repeater whose items are either plain strings
	 * or single-key arrays like ['text' => '…'] or ['value' => '…'].
	 *
	 * @param array $items Repeater rows.
	 * @return string
	 */
	private function collect_simple_text_repeater( $items ) {
		if ( empty( $items ) || ! is_array( $items ) ) {
			return '';
		}

		$lines = array();
		foreach ( $items as $item ) {
			if ( is_string( $item ) ) {
				$v = $item;
			} elseif ( is_array( $item ) ) {
				// Try common single-value keys in priority order.
				$v = (string) ( $item['text'] ?? $item['value'] ?? $item['label'] ?? $item['title'] ?? '' );
			} else {
				continue;
			}

			if ( ! $this->yoast_sync_is_non_content( $v ) ) {
				$lines[] = $v;
			}
		}

		return implode( "\n", $lines );
	}

	/**
	 * Collect text from a FAQ-style repeater whose items have 'question' and 'answer'.
	 *
	 * @param array $items FAQ rows.
	 * @return string
	 */
	private function collect_faq_repeater( $items ) {
		if ( empty( $items ) || ! is_array( $items ) ) {
			return '';
		}

		$lines = array();
		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			foreach ( array( 'question', 'answer', 'title', 'body' ) as $key ) {
				if ( ! empty( $item[ $key ] ) ) {
					$v = (string) $item[ $key ];
					if ( ! $this->yoast_sync_is_non_content( $v ) ) {
						$lines[] = $v;
					}
				}
			}
		}

		return implode( "\n", $lines );
	}

	/**
	 * Return true if a string value should be excluded from the synced content.
	 *
	 * Excludes:
	 *   - Empty strings.
	 *   - The 'empty--' intentional-blank sentinel.
	 *   - Pure integers (attachment IDs stored as strings).
	 *   - URLs (http, https, //).
	 *   - Shortcodes (start with '[').
	 *   - Anchor-only hrefs ('#…').
	 *
	 * @param string $value Value to test.
	 * @return bool
	 */
	private function yoast_sync_is_non_content( $value ) {
		$value = trim( $value );

		if ( '' === $value ) {
			return true;
		}

		// Intentional blank sentinel.
		if ( 'empty--' === $value ) {
			return true;
		}

		// Pure numeric → likely an attachment ID.
		if ( ctype_digit( $value ) ) {
			return true;
		}

		// URL prefixes.
		if (
			0 === strpos( $value, 'http://' ) ||
			0 === strpos( $value, 'https://' ) ||
			0 === strpos( $value, '//' ) ||
			0 === strpos( $value, '#' )
		) {
			return true;
		}

		// Shortcodes.
		if ( '[' === $value[0] ) {
			return true;
		}

		return false;
	}
}
