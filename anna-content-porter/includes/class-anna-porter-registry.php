<?php
/**
 * Anna Porter Registry
 *
 * Single source of truth for which anna_theme_options keys belong to which
 * page section, and which post-meta keys store the live content for each section.
 *
 * @package Anna_Content_Porter
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Maps page sections to their option key prefixes, exact key names, and
 * the post-meta keys that store the live content on specific pages.
 */
class Anna_Porter_Registry {

	/**
	 * Returns the complete section map.
	 *
	 * Each entry shape:
	 *   'label'          => string     Human-readable name shown in the UI.
	 *   'prefixes'       => string[]   Option key prefixes (legacy / brand / footer).
	 *   'exact'          => string[]   Exact option key names.
	 *   'post_meta_page' => string     (optional) '__front__' or a page slug.
	 *   'post_meta_keys' => string[]   (optional) _anna_content_* meta keys on that page.
	 *
	 * @return array<string, array>
	 */
	public static function get_sections(): array {
		$sections = [

			// ── Home page ──────────────────────────────────────────────────────
			'home' => [
				'label'          => 'Home',
				'prefixes'       => [
					'hero_', 'intro_', 'recognition_', 'services_', 'about_', 'testimonials_', 'cta_',
				],
				'exact'          => [],
				'post_meta_page' => '__front__',
				'post_meta_keys' => [
					'_anna_content_home_page',
				],
			],

			// ── Everything stored as live page content ─────────────────────────
			'all_pages' => [
				'label'          => 'All Pages',
				'prefixes'       => [],
				'exact'          => [],
				'post_meta_page' => '__all__',
				'post_meta_keys' => [ '_anna_content_%' ],
			],

			// ── Standalone page sections ───────────────────────────────────────
			'about_pg' => [
				'label'          => 'About Page',
				'prefixes'       => [ 'about_pg_' ],
				'exact'          => [],
				'post_meta_page' => 'about',
				'post_meta_keys' => [ '_anna_content_about_pg' ],
			],

			'coaching_pg' => [
				'label'          => 'Coaching Page',
				'prefixes'       => [ 'coaching_pg_' ],
				'exact'          => [],
				'post_meta_page' => 'coaching',
				'post_meta_keys' => [ '_anna_content_coaching_pg' ],
			],

			'oasis_pg' => [
				'label'          => 'Oasis Page',
				'prefixes'       => [ 'oasis_pg_' ],
				'exact'          => [],
				'post_meta_page' => 'oasis',
				'post_meta_keys' => [ '_anna_content_oasis_pg' ],
			],

			'speaking_pg' => [
				'label'          => 'Speaking Page',
				'prefixes'       => [ 'speaking_pg_' ],
				'exact'          => [],
				'post_meta_page' => 'speaking',
				'post_meta_keys' => [ '_anna_content_speaking_pg' ],
			],

			'mhs_pg' => [
				'label'          => 'Mental Health Support Page',
				'prefixes'       => [ 'mhs_pg_' ],
				'exact'          => [],
				'post_meta_page' => 'mhs',
				'post_meta_keys' => [ '_anna_content_mhs_pg' ],
			],

			'move_pg' => [
				'label'          => 'Move Page',
				'prefixes'       => [ 'move_pg_' ],
				'exact'          => [],
				'post_meta_page' => 'move',
				'post_meta_keys' => [ '_anna_content_move_pg' ],
			],

			'reviews_pg' => [
				'label'          => 'Reviews Page',
				'prefixes'       => [ 'reviews_pg_' ],
				'exact'          => [],
				'post_meta_page' => 'reviews',
				'post_meta_keys' => [ '_anna_content_reviews_pg' ],
			],

			'contact_pg' => [
				'label'          => 'Contact Page',
				'prefixes'       => [ 'contact_pg_' ],
				'exact'          => [],
				'post_meta_page' => 'contact',
				'post_meta_keys' => [ '_anna_content_contact_pg' ],
			],

			// ── Global brand — still lives in anna_theme_options ───────────────
			'brand' => [
				'label'    => 'Global Brand',
				'prefixes' => [
					'color_', 'font_', 'container_', 'header_',
				],
				'exact'    => [
					'site_logo_id',
					'footer_logo_id',
					'border_radius_btn',
					'section_padding_md',
					'discovery_call_url',
				],
			],

			// ── Footer & Social — still lives in anna_theme_options ────────────
			'footer_social' => [
				'label'    => 'Footer & Social',
				'prefixes' => [
					'footer_', 'social_', 'contact_', 'newsletter_', 'copyright_',
				],
				'exact'    => [
					'privacy_url',
					'terms_url',
				],
			],
		];

		// ── Dynamic scaffolded pages ───────────────────────────────────────────
		if ( function_exists( 'anna_get_scaffolded_pages' ) ) {
			foreach ( anna_get_scaffolded_pages() as $page ) {
				$code   = $page['code']          ?? '';
				$prefix = $page['option_prefix'] ?? '';
				$title  = $page['title']         ?? $code;

				if ( ! $code || ! $prefix ) {
					continue;
				}

				if ( ! isset( $sections[ $code ] ) ) {
					$sections[ $code ] = [
						'label'          => $title,
						'prefixes'       => [ $prefix ],
						'exact'          => [],
						'post_meta_page' => str_replace( '_pg', '', $code ),
						'post_meta_keys' => [ '_anna_content_' . rtrim( $code, '_' ) ],
					];
				}
			}
		}

		return $sections;
	}

	/**
	 * Given a list of section IDs and the full live anna_theme_options array,
	 * returns all matching option keys (used for brand/footer sections that
	 * still live in anna_theme_options).
	 *
	 * @param string[] $section_ids  Keys from get_sections() to include.
	 * @param array    $all_options  Full anna_theme_options associative array.
	 * @return string[]  Flat, deduplicated list of matching option key names.
	 */
	public static function get_keys_for_sections( array $section_ids, array $all_options ): array {
		$sections = static::get_sections();

		$requested = [];
		foreach ( $section_ids as $id ) {
			if ( isset( $sections[ $id ] ) ) {
				$requested[ $id ] = $sections[ $id ];
			}
		}

		if ( empty( $requested ) ) {
			return [];
		}

		$matched = [];

		foreach ( array_keys( $all_options ) as $key ) {
			$assigned_section = null;
			$best_prefix_len  = 0;
			$exact_match      = false;

			foreach ( $requested as $section_id => $section ) {
				if ( in_array( $key, $section['exact'], true ) ) {
					$assigned_section = $section_id;
					$exact_match      = true;
					break;
				}

				foreach ( $section['prefixes'] as $prefix ) {
					if ( str_starts_with( $key, $prefix ) ) {
						$prefix_len = strlen( $prefix );
						if ( $prefix_len > $best_prefix_len ) {
							$best_prefix_len  = $prefix_len;
							$assigned_section = $section_id;
						}
					}
				}
			}

			if ( null !== $assigned_section ) {
				$matched[] = $key;
			}
		}

		return array_values( array_unique( $matched ) );
	}

	/**
	 * Returns the section ID that owns the given option key, or null if none.
	 *
	 * @param string $key         The option key to look up.
	 * @param array  $all_options Full anna_theme_options array.
	 * @return string|null
	 */
	public static function get_section_for_key( string $key, array $all_options ): ?string {
		$sections        = static::get_sections();
		$assigned        = null;
		$best_prefix_len = 0;

		foreach ( $sections as $section_id => $section ) {
			if ( in_array( $key, $section['exact'], true ) ) {
				return $section_id;
			}

			foreach ( $section['prefixes'] as $prefix ) {
				if ( str_starts_with( $key, $prefix ) ) {
					$prefix_len = strlen( $prefix );
					if ( $prefix_len > $best_prefix_len ) {
						$best_prefix_len = $prefix_len;
						$assigned        = $section_id;
					}
				}
			}
		}

		return $assigned;
	}
}
