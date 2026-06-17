<?php
/**
 * Yoast SEO post_content synchronisation.
 *
 * Generates structured HTML from custom meta fields and writes it into
 * post_content so Yoast SEO can analyse:
 *   - Word count and readability (plain text in <p> tags)
 *   - Keyphrase in subheadings (<h2>/<h3> tags around section headings)
 *   - Keyphrase distribution (paragraph structure)
 *   - Internal and outbound links (<a href> from button/link URL+text pairs)
 *
 * Frontend rendering is unchanged — templates still read exclusively from
 * post meta via the existing helper functions.
 *
 * Loop-prevention: wp_update_post() fires save_post_page again. A static
 * flag prevents infinite recursion.
 *
 * @package Anna_Content_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Anna_Yoast_Sync {

	// -------------------------------------------------------------------------
	// Yoast filter registration
	// -------------------------------------------------------------------------

	/**
	 * Register the Yoast content analysis override filter on init.
	 * Yoast calls wpseo_post_content_analysis_override before analysis,
	 * so we hand it structured HTML directly — no editor DOM needed.
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
	 * Supply structured HTML to Yoast's analysis engine.
	 *
	 * @param string|null $override Existing override, if any.
	 * @param WP_Post     $post     Post being analysed.
	 * @return string
	 */
	public function provide_yoast_analysis_content( $override, $post ) {
		if ( ! $post instanceof WP_Post || 'page' !== $post->post_type ) {
			return $override;
		}

		$html = $this->build_html_for_post( $post->ID );
		return '' !== $html ? $html : $override;
	}

	// -------------------------------------------------------------------------
	// Public sync entry point (called on save and bulk sync)
	// -------------------------------------------------------------------------

	/**
	 * Build structured HTML from meta fields and write it to post_content.
	 *
	 * @param int $post_id Page ID.
	 */
	public function sync_post_content_for_yoast( $post_id ) {
		static $syncing = false;
		if ( $syncing ) {
			return;
		}

		$post_id = absint( $post_id );
		if ( ! $post_id ) {
			return;
		}

		$html = $this->build_html_for_post( $post_id );
		if ( '' === $html ) {
			return;
		}

		$syncing = true;
		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_content' => $html,
			)
		);
		$syncing = false;
	}

	// -------------------------------------------------------------------------
	// HTML builder — dispatcher
	// -------------------------------------------------------------------------

	/**
	 * Build the full structured HTML string for a given page.
	 *
	 * @param int $post_id Page ID.
	 * @return string HTML.
	 */
	private function build_html_for_post( $post_id ) {
		// Home page.
		if ( function_exists( 'anna_is_home_content_page' ) && anna_is_home_content_page( $post_id ) ) {
			return $this->build_home_page_html( $post_id );
		}

		$template = get_page_template_slug( $post_id );
		$slug     = get_post_field( 'post_name', $post_id );
		$sections = array();

		// About.
		if ( 'about' === $slug || 'page-about.php' === $template ) {
			$data = get_post_meta( $post_id, '_anna_content_about_page', true );
			if ( is_array( $data ) ) {
				$sections = $this->build_about_page_html( $data );
			}
		}

		// Coaching.
		if ( 'coaching' === $slug || 'page-coaching.php' === $template ) {
			$data = get_post_meta( $post_id, '_anna_content_coaching_page', true );
			if ( is_array( $data ) ) {
				$sections = $this->build_coaching_page_html( $data );
			}
		}

		// Oasis.
		if ( 'oasis' === $slug || 'page-oasis.php' === $template ) {
			$data = get_post_meta( $post_id, '_anna_content_oasis_page', true );
			if ( is_array( $data ) ) {
				$sections = $this->build_oasis_page_html( $data );
			}
		}

		// Speaking.
		if ( 'speaking' === $slug || 'page-speaking.php' === $template ) {
			$data = get_post_meta( $post_id, '_anna_content_speaking_page', true );
			if ( is_array( $data ) ) {
				$sections = $this->build_speaking_page_html( $data );
			}
		}

		// Mental Health Support.
		if ( 'mental-health-support' === $slug || 'page-mental-health-support.php' === $template ) {
			$data = get_post_meta( $post_id, '_anna_content_mhs_page', true );
			if ( is_array( $data ) ) {
				$sections = $this->build_mhs_page_html( $data );
			}
		}

		// MOVE.
		if ( 'move' === $slug || 'page-move.php' === $template ) {
			$data = get_post_meta( $post_id, '_anna_content_move_page', true );
			if ( is_array( $data ) ) {
				$sections = $this->build_move_page_html( $data );
			}
		}

		// Reviews.
		if ( 'reviews' === $slug || 'page-reviews.php' === $template ) {
			$data = get_post_meta( $post_id, '_anna_content_reviews_page', true );
			if ( is_array( $data ) ) {
				$sections = $this->build_reviews_page_html( $data );
			}
		}

		// Contact.
		if ( 'contact' === $slug || 'what-is-a-life-coach' === $slug || 'page-contact.php' === $template ) {
			$data = get_post_meta( $post_id, '_anna_content_contact_page', true );
			if ( is_array( $data ) ) {
				$sections = $this->build_contact_page_html( $data );
			}
		}

		// Blog.
		if ( 'blog' === $slug || 'page-blog.php' === $template ) {
			$data = get_post_meta( $post_id, '_anna_content_blog_page', true );
			if ( is_array( $data ) ) {
				$sections = $this->build_blog_page_html( $data );
			}
		}

		// Scaffolded pages.
		if ( empty( $sections ) && function_exists( 'anna_get_flexible_page_config' ) ) {
			$config = anna_get_flexible_page_config( $post_id );
			if ( $config ) {
				$code = $config['code'] ?? '';
				if ( $code ) {
					$data = get_post_meta( $post_id, '_anna_content_' . $code . '_page', true );
					if ( is_array( $data ) ) {
						$sections = $this->build_scaffolded_page_html( $data, $post_id, $config );
					}
				}
			}
		}

		return implode( "\n", array_filter( $sections, 'strlen' ) );
	}


	// -------------------------------------------------------------------------
	// Per-page HTML builders
	// -------------------------------------------------------------------------

	/**
	 * Home page — nested sections in _anna_content_home_page.
	 *
	 * @param int $post_id Page ID.
	 * @return string HTML.
	 */
	private function build_home_page_html( $post_id ) {
		$data = get_post_meta( $post_id, '_anna_content_home_page', true );
		if ( ! is_array( $data ) ) {
			return '';
		}

		$out = array();

		// Hero.
		$hero = $data['hero'] ?? array();
		$out[] = $this->h2( $hero['heading'] ?? '' );
		$out[] = $this->img( $hero['image_id'] ?? 0 );
		$out[] = $this->p( $hero['description'] ?? '' );
		$out[] = $this->p( $hero['trust_text'] ?? '' );
		$out[] = $this->p( $this->stats_inline( $hero, array( 'stat_1_value', 'stat_1_label', 'stat_2_value', 'stat_2_label', 'stat_3_value', 'stat_3_label' ) ) );
		$out[] = $this->link( $hero['primary_button_url'] ?? '', $hero['primary_button_text'] ?? '' );
		$out[] = $this->link( $hero['secondary_button_url'] ?? '', $hero['secondary_button_text'] ?? '' );

		// Intro / recognition.
		$intro = $data['intro'] ?? array();
		$out[] = $this->h2( $intro['intro_heading'] ?? '' );
		$out[] = $this->p( $intro['intro_body'] ?? '' );
		$out[] = $this->p( $intro['intro_quote'] ?? '' );
		$out[] = $this->h3( $intro['recognition_heading'] ?? '' );
		$out[] = $this->p( $intro['recognition_description'] ?? '' );
		$out[] = $this->multiline_p( $intro['recognition_items_text'] ?? '' );

		// Services.
		$services = $data['services'] ?? array();
		$out[] = $this->h2( $services['heading'] ?? '' );
		$out[] = $this->p( $services['description'] ?? '' );
		foreach ( array( 1, 2, 3 ) as $n ) {
			$out[] = $this->h3( $services[ "card_{$n}_title" ] ?? '' );
			$out[] = $this->img( $services[ "card_{$n}_image_id" ] ?? 0 );
			$out[] = $this->p( $services[ "card_{$n}_excerpt" ] ?? '' );
			$out[] = $this->link( $services[ "card_{$n}_url" ] ?? '', $services[ "card_{$n}_link" ] ?? '' );
		}

		// About.
		$about = $data['about'] ?? array();
		$out[] = $this->h2( $about['heading'] ?? '' );
		$out[] = $this->img( $about['image_id'] ?? 0 );
		$out[] = $this->p( $about['body'] ?? '' );
		$out[] = $this->p( $about['quote'] ?? '' );
		$out[] = $this->link( $about['cta_url'] ?? '', $about['cta_text'] ?? '' );

		// Testimonials.
		$testimonials = $data['testimonials'] ?? array();
		$out[] = $this->h2( $testimonials['heading'] ?? '' );
		$out[] = $this->p( $testimonials['summary'] ?? '' );
		$out[] = $this->link( $testimonials['cta_url'] ?? '', $testimonials['cta_text'] ?? '' );

		// CTA.
		$cta = $data['cta'] ?? array();
		$out[] = $this->h2( $cta['heading'] ?? '' );
		$out[] = $this->p( $cta['description'] ?? '' );
		$out[] = $this->link( $cta['primary_button_url'] ?? '', $cta['primary_button_text'] ?? '' );
		$out[] = $this->link( $cta['secondary_button_url'] ?? '', $cta['secondary_button_text'] ?? '' );

		return implode( "\n", array_filter( $out, 'strlen' ) );
	}

	/**
	 * About page — _anna_content_about_page.
	 *
	 * @param array $data Saved meta.
	 * @return string[] HTML sections.
	 */
	private function build_about_page_html( $data ) {
		$out = array();

		$out[] = $this->h2( $data['hero_heading'] ?? '' );
		$out[] = $this->p( $data['hero_description'] ?? '' );

		$out[] = $this->h2( $data['story_heading'] ?? '' );
		$out[] = $this->img( $data['story_image_id'] ?? 0 );
		$out[] = $this->p( $data['story_body'] ?? '' );

		$out[] = $this->h2( $data['rock_heading'] ?? '' );
		$out[] = $this->p( $data['rock_left_body'] ?? '' );
		$out[] = $this->p( $data['rock_right_body'] ?? '' );

		$out[] = $this->h2( $data['coach_title'] ?? '' );
		$out[] = $this->img( $data['coach_image_id'] ?? 0 );
		$out[] = $this->p( $data['coach_body'] ?? '' );
		$out[] = $this->link( $data['coach_button_url'] ?? '', $data['coach_button_text'] ?? '' );

		$out[] = $this->h2( $data['work_heading'] ?? '' );
		$out[] = $this->p( $data['work_body'] ?? '' );
		foreach ( array( 1, 2, 3, 4 ) as $n ) {
			$out[] = $this->h3( $data[ "work_card_{$n}_title" ] ?? '' );
			$out[] = $this->p( $data[ "work_card_{$n}_body" ] ?? '' );
		}

		$out[] = $this->h2( $data['people_heading'] ?? '' );
		$out[] = $this->p( $data['people_body'] ?? '' );
		if ( ! empty( $data['people_items'] ) && is_array( $data['people_items'] ) ) {
			foreach ( $data['people_items'] as $item ) {
				if ( is_array( $item ) ) {
					$out[] = $this->p( trim( ( $item['title'] ?? '' ) . ' ' . ( $item['org'] ?? '' ) ) );
				}
			}
		}

		$out[] = $this->h2( $data['connect_heading'] ?? '' );
		$out[] = $this->link( $data['connect_button_url'] ?? '', $data['connect_button_text'] ?? '' );

		return $out;
	}

	/**
	 * Coaching page — _anna_content_coaching_page.
	 *
	 * @param array $data Saved meta.
	 * @return string[] HTML sections.
	 */
	private function build_coaching_page_html( $data ) {
		$out = array();

		$out[] = $this->h2( $data['hero_heading'] ?? '' );
		$out[] = $this->img( $data['hero_image_id'] ?? 0 );
		$out[] = $this->p( $data['hero_description'] ?? '' );
		$out[] = $this->link( $data['hero_button_url'] ?? '', $data['hero_button_text'] ?? '' );

		$out[] = $this->h2( $data['what_heading'] ?? '' );
		$out[] = $this->p( $data['what_body'] ?? '' );
		$out[] = $this->link( $data['what_button_url'] ?? '', $data['what_button_text'] ?? '' );
		$out[] = $this->items_list( $data['what_card_items'] ?? array() );

		$out[] = $this->h2( $data['pillars_heading'] ?? '' );
		if ( ! empty( $data['pillar_items'] ) && is_array( $data['pillar_items'] ) ) {
			foreach ( $data['pillar_items'] as $item ) {
				if ( is_array( $item ) ) {
					$out[] = $this->h3( $item['title'] ?? '' );
					$out[] = $this->p( $item['body'] ?? $item['text'] ?? '' );
				}
			}
		}

		$out[] = $this->h2( $data['work_heading'] ?? '' );
		$out[] = $this->items_list( $data['work_topics_items'] ?? array() );
		$out[] = $this->h3( $data['work_gains_heading'] ?? '' );
		$out[] = $this->items_list( $data['work_gains_items'] ?? array() );

		$out[] = $this->h2( $data['expect_heading_line1'] ?? '' );
		$out[] = $this->p( $data['expect_body'] ?? '' );
		$out[] = $this->p( $data['expect_quote'] ?? '' );
		$out[] = $this->link( $data['expect_button_url'] ?? '', $data['expect_button_text'] ?? '' );
		if ( ! empty( $data['expect_info_cards'] ) && is_array( $data['expect_info_cards'] ) ) {
			foreach ( $data['expect_info_cards'] as $card ) {
				if ( is_array( $card ) ) {
					$out[] = $this->h3( $card['title'] ?? '' );
					$out[] = $this->p( $card['body'] ?? '' );
				}
			}
		}

		$out[] = $this->h2( $data['faq_heading'] ?? '' );
		$out[] = $this->faq_html( $data['faq_items'] ?? array() );

		return $out;
	}


	/**
	 * Oasis page — _anna_content_oasis_page.
	 *
	 * @param array $data Saved meta.
	 * @return string[] HTML sections.
	 */
	private function build_oasis_page_html( $data ) {
		$out = array();

		$out[] = $this->h2( $data['hero_heading'] ?? '' );
		$out[] = $this->p( $data['hero_subheading'] ?? '' );
		$out[] = $this->p( $data['hero_body'] ?? '' );
		$out[] = $this->img( $data['hero_image_id'] ?? 0 );
		$out[] = $this->link( $data['hero_button_url'] ?? '', $data['hero_button_text'] ?? '' );

		$out[] = $this->h2( $data['what_heading'] ?? '' );
		$out[] = $this->p( $data['what_body'] ?? '' );
		$out[] = $this->link( $data['what_footer_url'] ?? '', $data['what_footer_line'] ?? '' );

		$out[] = $this->h2( $data['begun_heading'] ?? '' );
		$out[] = $this->p( $data['begun_subheading'] ?? '' );
		$out[] = $this->p( $data['begun_body'] ?? '' );
		$out[] = $this->img( $data['begun_image_id'] ?? 0 );
		$out[] = $this->p( $data['begun_quote'] ?? '' );
		$out[] = $this->p( $data['begun_closing'] ?? '' );
		$out[] = $this->h3( $data['begun_callout_label'] ?? '' );
		$out[] = $this->p( $data['begun_callout_body'] ?? '' );
		$out[] = $this->link( $data['begun_link_url'] ?? '', $data['begun_link_text'] ?? '' );

		$out[] = $this->h2( $data['inside_heading'] ?? '' );
		$out[] = $this->p( $data['inside_body'] ?? '' );
		$out[] = $this->p( $data['inside_highlight'] ?? '' );
		$out[] = $this->items_list( $data['inside_pill_items'] ?? array() );

		if ( ! empty( $data['inside_schedule_items'] ) && is_array( $data['inside_schedule_items'] ) ) {
			foreach ( $data['inside_schedule_items'] as $item ) {
				if ( is_array( $item ) ) {
					$out[] = $this->p( trim( ( $item['label'] ?? $item['title'] ?? '' ) . ' ' . ( $item['time'] ?? $item['body'] ?? '' ) ) );
				}
			}
		}

		$out[] = $this->h2( $data['how_heading'] ?? '' );
		$out[] = $this->p( $data['how_intro'] ?? '' );
		if ( ! empty( $data['how_card_items'] ) && is_array( $data['how_card_items'] ) ) {
			foreach ( $data['how_card_items'] as $item ) {
				if ( is_array( $item ) ) {
					$out[] = $this->h3( $item['title'] ?? $item['step'] ?? '' );
					$out[] = $this->p( $item['body'] ?? $item['text'] ?? '' );
				}
			}
		}
		$out[] = $this->p( $data['how_footer'] ?? '' );

		$out[] = $this->h2( $data['choose_heading'] ?? '' );
		$out[] = $this->p( $data['choose_intro'] ?? '' );
		if ( ! empty( $data['choose_plan_items'] ) && is_array( $data['choose_plan_items'] ) ) {
			foreach ( $data['choose_plan_items'] as $item ) {
				if ( is_array( $item ) ) {
					$out[] = $this->h3( $item['title'] ?? '' );
					$out[] = $this->p( $item['body'] ?? $item['price'] ?? '' );
					$out[] = $this->items_list( $item['features'] ?? array() );
				}
			}
		}
		$out[] = $this->p( $data['choose_footer'] ?? '' );

		$out[] = $this->h2( $data['ready_heading'] ?? '' );
		$out[] = $this->items_list( $data['ready_items'] ?? array() );

		$out[] = $this->h2( $data['waitlist_heading'] ?? '' );
		$out[] = $this->link( $data['waitlist_button_url'] ?? '', $data['waitlist_button_text'] ?? '' );

		$out[] = $this->h2( $data['faq_heading'] ?? '' );
		$out[] = $this->faq_html( $data['faq_items'] ?? array() );

		return $out;
	}

	/**
	 * Speaking page — _anna_content_speaking_page.
	 *
	 * @param array $data Saved meta.
	 * @return string[] HTML sections.
	 */
	private function build_speaking_page_html( $data ) {
		$out = array();

		$out[] = $this->h2( $data['hero_heading'] ?? '' );
		$out[] = $this->img( $data['hero_image_id'] ?? 0 );
		$out[] = $this->p( $data['hero_body'] ?? '' );
		$out[] = $this->link( $data['hero_button_url'] ?? '', $data['hero_button_text'] ?? '' );
		$out[] = $this->link( $data['hero_secondary_url'] ?? '', $data['hero_secondary_text'] ?? '' );

		if ( ! empty( $data['hero_stat_items'] ) && is_array( $data['hero_stat_items'] ) ) {
			foreach ( $data['hero_stat_items'] as $item ) {
				if ( is_array( $item ) ) {
					$out[] = $this->p( trim( ( $item['value'] ?? '' ) . ' ' . ( $item['label'] ?? '' ) ) );
				}
			}
		}

		$out[] = $this->h2( trim( ( $data['bring_heading_line1'] ?? '' ) . ' ' . ( $data['bring_heading_line2'] ?? '' ) ) );
		$out[] = $this->img( $data['bring_image_id'] ?? 0 );
		$out[] = $this->p( $data['bring_body'] ?? '' );
		$out[] = $this->p( $data['bring_quote'] ?? '' );
		$out[] = $this->link( $data['bring_button_url'] ?? '', $data['bring_button_text'] ?? '' );

		$out[] = $this->h2( $data['topics_heading'] ?? '' );
		$out[] = $this->p( $data['topics_body'] ?? '' );
		if ( ! empty( $data['topics_card_items'] ) && is_array( $data['topics_card_items'] ) ) {
			foreach ( $data['topics_card_items'] as $item ) {
				if ( is_array( $item ) ) {
					$out[] = $this->h3( $item['title'] ?? $item['text'] ?? '' );
					$out[] = $this->p( $item['body'] ?? '' );
				}
			}
		}

		$out[] = $this->h2( $data['formats_heading'] ?? '' );
		$out[] = $this->p( $data['formats_body'] ?? '' );
		if ( ! empty( $data['formats_card_items'] ) && is_array( $data['formats_card_items'] ) ) {
			foreach ( $data['formats_card_items'] as $item ) {
				if ( is_array( $item ) ) {
					$out[] = $this->h3( $item['title'] ?? '' );
					$out[] = $this->p( $item['body'] ?? '' );
				}
			}
		}
		$out[] = $this->items_list( $data['formats_audience_items'] ?? array() );

		$out[] = $this->h2( $data['takeaway_heading'] ?? '' );
		$out[] = $this->items_list( $data['takeaway_items'] ?? array() );

		$out[] = $this->h2( $data['cta_heading'] ?? '' );
		$out[] = $this->p( $data['cta_body'] ?? '' );
		$out[] = $this->link( $data['cta_button_url'] ?? '', $data['cta_button_text'] ?? '' );

		return $out;
	}

	/**
	 * Mental Health Support page — _anna_content_mhs_page.
	 *
	 * @param array $data Saved meta.
	 * @return string[] HTML sections.
	 */
	private function build_mhs_page_html( $data ) {
		$out = array();

		$out[] = $this->h2( $data['hero_heading'] ?? '' );
		$out[] = $this->img( $data['hero_image_id'] ?? 0 );

		$out[] = $this->h2( $data['opening_heading'] ?? '' );
		$out[] = $this->img( $data['opening_image_id'] ?? 0 );
		$out[] = $this->p( $data['opening_body'] ?? '' );

		$out[] = $this->h2( $data['programs_heading'] ?? '' );
		$out[] = $this->p( $data['programs_body'] ?? '' );

		$out[] = $this->h2( $data['inner_heading'] ?? '' );
		$out[] = $this->img( $data['inner_image_id'] ?? 0 );
		$out[] = $this->p( $data['inner_body'] ?? '' );

		$out[] = $this->h2( $data['work_heading'] ?? '' );
		$out[] = $this->p( $data['work_body'] ?? '' );

		$out[] = $this->h2( $data['practice_heading'] ?? '' );
		$out[] = $this->p( $data['practice_body'] ?? '' );
		$out[] = $this->link( $data['practice_link_url'] ?? '', $data['practice_link_text'] ?? '' );

		$out[] = $this->h2( $data['ready_heading'] ?? '' );
		$out[] = $this->p( $data['ready_subheading'] ?? '' );
		$out[] = $this->p( $data['ready_body'] ?? '' );
		$out[] = $this->link( $data['ready_button_primary_url'] ?? '', $data['ready_button_primary_text'] ?? '' );
		$out[] = $this->link( $data['ready_button_secondary_url'] ?? '', $data['ready_button_secondary_text'] ?? '' );
		$out[] = $this->link( $data['ready_button_tertiary_url'] ?? '', $data['ready_button_tertiary_text'] ?? '' );

		return $out;
	}

	/**
	 * MOVE page — _anna_content_move_page.
	 *
	 * @param array $data Saved meta.
	 * @return string[] HTML sections.
	 */
	private function build_move_page_html( $data ) {
		$out = array();

		$out[] = $this->h2( $data['hero_heading'] ?? '' );
		$out[] = $this->img( $data['hero_image_id'] ?? 0 );

		$out[] = $this->h2( $data['evolution_heading'] ?? '' );
		$out[] = $this->p( $data['evolution_body'] ?? '' );
		$out[] = $this->p( $data['evolution_callout'] ?? '' );

		$out[] = $this->h2( $data['was_heading'] ?? '' );
		$out[] = $this->p( $data['was_body'] ?? '' );

		$out[] = $this->h2( $data['said_heading'] ?? '' );
		if ( ! empty( $data['said_items'] ) && is_array( $data['said_items'] ) ) {
			foreach ( $data['said_items'] as $item ) {
				$quote = is_array( $item ) ? ( $item['quote'] ?? '' ) : (string) $item;
				$out[] = $this->p( $quote );
			}
		}

		$out[] = $this->h2( $data['reviews_heading'] ?? '' );
		$out[] = $this->p( $data['reviews_summary'] ?? '' );
		$out[] = $this->link( $data['reviews_cta_url'] ?? '', $data['reviews_cta_text'] ?? '' );
		if ( ! empty( $data['reviews_items'] ) && is_array( $data['reviews_items'] ) ) {
			foreach ( $data['reviews_items'] as $item ) {
				if ( is_array( $item ) ) {
					$out[] = $this->p( $item['body'] ?? $item['text'] ?? $item['quote'] ?? '' );
				}
			}
		}

		$out[] = $this->h2( $data['pillars_heading'] ?? '' );
		if ( ! empty( $data['pillar_items'] ) && is_array( $data['pillar_items'] ) ) {
			foreach ( $data['pillar_items'] as $item ) {
				if ( is_array( $item ) ) {
					$out[] = $this->h3( $item['title'] ?? $item['text'] ?? '' );
					$out[] = $this->p( $item['body'] ?? '' );
				}
			}
		}

		$out[] = $this->h2( $data['evolved_heading'] ?? '' );
		$out[] = $this->p( $data['evolved_body'] ?? '' );
		$out[] = $this->link( $data['evolved_button_primary_url'] ?? '', $data['evolved_button_primary_text'] ?? '' );
		$out[] = $this->link( $data['evolved_button_secondary_url'] ?? '', $data['evolved_button_secondary_text'] ?? '' );
		$out[] = $this->link( $data['evolved_button_tertiary_url'] ?? '', $data['evolved_button_tertiary_text'] ?? '' );

		return $out;
	}

	/**
	 * Reviews page — _anna_content_reviews_page.
	 *
	 * @param array $data Saved meta.
	 * @return string[] HTML sections.
	 */
	private function build_reviews_page_html( $data ) {
		$out = array();

		$out[] = $this->h2( $data['hero_heading'] ?? '' );
		$out[] = $this->img( $data['hero_image_id'] ?? 0 );
		$out[] = $this->p( $data['hero_rating_text'] ?? '' );
		$out[] = $this->link( $data['google_reviews_url'] ?? '', $data['google_reviews_text'] ?? '' );

		$out[] = $this->h2( $data['cta_heading'] ?? '' );
		$out[] = $this->p( $data['cta_body'] ?? '' );
		$out[] = $this->link( $data['cta_button_url'] ?? '', $data['cta_button_text'] ?? '' );

		return $out;
	}

	/**
	 * Contact page — _anna_content_contact_page.
	 *
	 * @param array $data Saved meta.
	 * @return string[] HTML sections.
	 */
	private function build_contact_page_html( $data ) {
		$out = array();

		$out[] = $this->h2( $data['hero_heading'] ?? '' );
		$out[] = $this->h2( $data['info_heading'] ?? '' );

		$out[] = $this->h2( $data['cta_card_heading'] ?? '' );
		$out[] = $this->p( $data['cta_card_body'] ?? '' );
		$out[] = $this->link( $data['cta_card_button_url'] ?? '', $data['cta_card_button_text'] ?? '' );

		$out[] = $this->h2( $data['form_heading'] ?? '' );
		$out[] = $this->p( $data['form_response_note'] ?? '' );

		return $out;
	}

	/**
	 * Blog page — _anna_content_blog_page.
	 *
	 * @param array $data Saved meta.
	 * @return string[] HTML sections.
	 */
	private function build_blog_page_html( $data ) {
		$out = array();

		$out[] = $this->h2( $data['hero_heading'] ?? '' );
		$out[] = $this->p( $data['hero_description'] ?? '' );
		$out[] = $this->h2( $data['section_heading'] ?? '' );
		$out[] = $this->p( $data['section_subtext'] ?? '' );

		return $out;
	}

	/**
	 * Scaffolded / flexible pages — _anna_content_{code}_page.
	 * Uses field type from config to decide element: textarea → <p>, text → <p>,
	 * url fields are paired with the preceding text field as a link.
	 *
	 * @param array $data    Saved meta.
	 * @param int   $post_id Post ID.
	 * @param array $config  Page config.
	 * @return string[] HTML sections.
	 */
	private function build_scaffolded_page_html( $data, $post_id, $config ) {
		$out = array();

		$sections = function_exists( 'anna_get_page_sections_for_post' )
			? anna_get_page_sections_for_post( $post_id )
			: ( $config['sections'] ?? array() );

		foreach ( $sections as $section ) {
			$label = $section['label'] ?? '';
			if ( $label ) {
				$out[] = $this->h2( $label );
			}

			$fields      = $section['fields'] ?? array();
			$field_keys  = array_keys( $fields );
			$pending_url = null;

			foreach ( $field_keys as $key ) {
				$field = $fields[ $key ];
				$type  = $field['type'] ?? 'text';
				$value = isset( $data[ $key ] ) ? (string) $data[ $key ] : '';

				if ( 'empty--' === trim( $value ) || '' === trim( $value ) ) {
					continue;
				}

				if ( 'media' === $type ) {
					continue;
				}

				if ( 'url' === $type ) {
					// Hold URL — pair with next text field or emit as bare link.
					$pending_url = $value;
					continue;
				}

				// text or textarea.
				if ( null !== $pending_url ) {
					$out[]       = $this->link( $pending_url, $value );
					$pending_url = null;
				} else {
					$out[] = $this->p( $value );
				}
			}

			// Emit any leftover URL with no text.
			if ( null !== $pending_url ) {
				$out[]       = $this->link( $pending_url, $pending_url );
				$pending_url = null;
			}
		}

		return $out;
	}


	// -------------------------------------------------------------------------
	// HTML primitive helpers
	// -------------------------------------------------------------------------

	/**
	 * Build an <img> tag from an attachment ID.
	 * Uses wp_get_attachment_image() so the alt text stored in the media
	 * library is automatically included — this is what Yoast reads for
	 * the "keyphrase in image alt" check.
	 * Returns '' if the ID is empty or the attachment doesn't exist.
	 *
	 * @param mixed $image_id Attachment ID (int or numeric string).
	 * @return string
	 */
	private function img( $image_id ) {
		$image_id = absint( $image_id );
		if ( ! $image_id ) {
			return '';
		}
		$html = wp_get_attachment_image( $image_id, 'large' );
		return $html ? $html : '';
	}

	/**
	 * Wrap a value in <h2>. Returns '' if value is empty or non-content.
	 *
	 * @param string $value Raw text.
	 * @return string
	 */
	private function h2( $value ) {
		$value = $this->clean_text( $value );
		return '' !== $value ? '<h2>' . esc_html( $value ) . '</h2>' : '';
	}

	/**
	 * Wrap a value in <h3>.
	 *
	 * @param string $value Raw text.
	 * @return string
	 */
	private function h3( $value ) {
		$value = $this->clean_text( $value );
		return '' !== $value ? '<h3>' . esc_html( $value ) . '</h3>' : '';
	}

	/**
	 * Wrap a value in <p>. Converts newlines to <br> within the paragraph.
	 *
	 * @param string $value Raw text.
	 * @return string
	 */
	private function p( $value ) {
		$value = $this->clean_text( $value );
		if ( '' === $value ) {
			return '';
		}
		// Preserve intentional line breaks inside body copy.
		$value = nl2br( esc_html( $value ) );
		return '<p>' . $value . '</p>';
	}

	/**
	 * Build a paragraph from multi-line text (e.g. recognition_items_text).
	 * Each line becomes a sentence in the paragraph.
	 *
	 * @param string $value Newline-separated text.
	 * @return string
	 */
	private function multiline_p( $value ) {
		$value = trim( (string) $value );
		if ( '' === $value || 'empty--' === $value ) {
			return '';
		}
		$lines = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $value ) ) );
		if ( empty( $lines ) ) {
			return '';
		}
		return '<p>' . implode( ' · ', array_map( 'esc_html', $lines ) ) . '</p>';
	}

	/**
	 * Build an <a href> tag from a URL and link text.
	 * Returns '' if either is empty or the URL is a non-content value.
	 *
	 * @param string $url  Link href.
	 * @param string $text Link text.
	 * @return string
	 */
	private function link( $url, $text ) {
		$url  = trim( (string) $url );
		$text = $this->clean_text( $text );

		if ( '' === $url || '' === $text ) {
			return '';
		}

		// Skip anchor-only or empty hrefs.
		if ( '#' === $url || 'empty--' === $url ) {
			return '';
		}

		return '<p><a href="' . esc_url( $url ) . '">' . esc_html( $text ) . '</a></p>';
	}

	/**
	 * Build a <ul> list from a simple repeater array.
	 * Items can be strings or arrays with 'text'/'label'/'value'/'title' keys.
	 *
	 * @param array $items Repeater rows.
	 * @return string
	 */
	private function items_list( $items ) {
		if ( empty( $items ) || ! is_array( $items ) ) {
			return '';
		}

		$lis = array();
		foreach ( $items as $item ) {
			if ( is_string( $item ) ) {
				$v = $item;
			} elseif ( is_array( $item ) ) {
				$v = (string) ( $item['text'] ?? $item['label'] ?? $item['value'] ?? $item['title'] ?? '' );
			} else {
				continue;
			}
			$v = $this->clean_text( $v );
			if ( '' !== $v ) {
				$lis[] = '<li>' . esc_html( $v ) . '</li>';
			}
		}

		return ! empty( $lis ) ? '<ul>' . implode( '', $lis ) . '</ul>' : '';
	}

	/**
	 * Build FAQ items as <h3> question + <p> answer pairs.
	 *
	 * @param array $items FAQ rows.
	 * @return string
	 */
	private function faq_html( $items ) {
		if ( empty( $items ) || ! is_array( $items ) ) {
			return '';
		}

		$out = array();
		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$out[] = $this->h3( $item['question'] ?? $item['title'] ?? '' );
			$out[] = $this->p( $item['answer'] ?? $item['body'] ?? '' );
		}

		return implode( "\n", array_filter( $out, 'strlen' ) );
	}

	/**
	 * Build an inline sentence from stat value/label pairs.
	 *
	 * @param array    $data Array containing stat keys.
	 * @param string[] $keys Keys to collect in order.
	 * @return string
	 */
	private function stats_inline( $data, $keys ) {
		$parts = array();
		foreach ( $keys as $key ) {
			$v = $this->clean_text( $data[ $key ] ?? '' );
			if ( '' !== $v ) {
				$parts[] = $v;
			}
		}
		return implode( ' · ', $parts );
	}

	/**
	 * Strip, trim, and reject non-content values from a string.
	 * Returns '' for empty strings, pure integers (attachment IDs),
	 * the empty-- sentinel, and shortcodes.
	 *
	 * @param string $value Raw value.
	 * @return string
	 */
	private function clean_text( $value ) {
		$value = trim( wp_strip_all_tags( (string) $value ) );

		if ( '' === $value )           { return ''; }
		if ( 'empty--' === $value )    { return ''; }
		if ( ctype_digit( $value ) )   { return ''; }
		if ( '[' === $value[0] )       { return ''; }

		return html_entity_decode( $value, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	}
}
