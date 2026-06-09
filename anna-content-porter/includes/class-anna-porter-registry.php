<?php
/**
 * Anna Porter Registry
 *
 * Single source of truth for which anna_theme_options keys belong to which
 * page section. Used by both the exporter and the importer.
 *
 * @package Anna_Content_Porter
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Maps page sections to their option key prefixes and exact key names.
 */
class Anna_Porter_Registry {

	/**
	 * Returns the complete section map, merged with any dynamically registered
	 * scaffolded pages from anna_get_scaffolded_pages().
	 *
	 * Each entry has the shape:
	 *   'label'    => string     Human-readable name shown in the UI.
	 *   'prefixes' => string[]   Key prefixes to match (e.g. 'hero_').
	 *   'exact'    => string[]   Exact key names (e.g. 'site_logo_id').
	 *
	 * @return array<string, array{label: string, prefixes: string[], exact: string[]}>
	 */
	public static function get_sections(): array {
		$sections = [

			// ── Home page ──────────────────────────────────────────────────────
			'home' => [
				'label'    => 'Home',
				'prefixes' => [
					'hero_',         // Hero section fields and stats
					'intro_',        // Intro / approach section
					'recognition_',  // Recognition list section
					'services_',     // Services section
					'about_',        // Home page "about" teaser (NOT the About page)
					'testimonials_', // Testimonials section
					'cta_',          // Final CTA section
				],
				'exact'    => [],
			],

			// ── Standalone page sections ───────────────────────────────────────
			'about_pg' => [
				'label'    => 'About Page',
				'prefixes' => [ 'about_pg_' ],
				'exact'    => [],
			],

			'coaching_pg' => [
				'label'    => 'Coaching Page',
				'prefixes' => [ 'coaching_pg_' ],
				'exact'    => [],
			],

			'oasis_pg' => [
				'label'    => 'Oasis Page',
				'prefixes' => [ 'oasis_pg_' ],
				'exact'    => [],
			],

			'speaking_pg' => [
				'label'    => 'Speaking Page',
				'prefixes' => [ 'speaking_pg_' ],
				'exact'    => [],
			],

			'mhs_pg' => [
				'label'    => 'Mental Health Support Page',
				'prefixes' => [ 'mhs_pg_' ],
				'exact'    => [],
			],

			'move_pg' => [
				'label'    => 'Move Page',
				'prefixes' => [ 'move_pg_' ],
				'exact'    => [],
			],

			'reviews_pg' => [
				'label'    => 'Reviews Page',
				'prefixes' => [ 'reviews_pg_' ],
				'exact'    => [],
			],

			'contact_pg' => [
				'label'    => 'Contact Page',
				'prefixes' => [ 'contact_pg_' ],
				'exact'    => [],
			],

			// ── Global brand ───────────────────────────────────────────────────
			// footer_logo_id is listed as an exact key here so it goes to brand
			// exports rather than the footer_social section's footer_ prefix.
			'brand' => [
				'label'    => 'Global Brand',
				'prefixes' => [
					'color_',     // color_primary, color_accent, …
					'font_',      // font_heading, font_body, font_size_base, …
					'container_', // container_max, container_wide
					'header_',    // header_style, header_cta_text, header_cta_url
				],
				'exact'    => [
					'site_logo_id',
					'footer_logo_id',
					'border_radius_btn',
					'section_padding_md',
					'discovery_call_url',
				],
			],

			// ── Footer & Social ────────────────────────────────────────────────
			// contact_* here covers global footer contact details (email, phone,
			// address). Contact *page* fields use prefix contact_pg_ (separate section).
			'footer_social' => [
				'label'    => 'Footer & Social',
				'prefixes' => [
					'footer_',     // footer_description, etc. (footer_logo_id overridden above)
					'social_',     // social_links array
					'contact_',    // contact_email, contact_phone, contact_address, contact_hours
					'newsletter_', // newsletter_heading, newsletter_text, …
					'copyright_',  // copyright_text
				],
				'exact'    => [
					'privacy_url',
					'terms_url',
				],
			],
		];

		// ── Dynamic scaffolded pages ───────────────────────────────────────────
		// Guard with function_exists so the registry works even when the
		// page-scaffolder plugin is inactive.
		if ( function_exists( 'anna_get_scaffolded_pages' ) ) {
			foreach ( anna_get_scaffolded_pages() as $page ) {
				$code   = $page['code']          ?? '';
				$prefix = $page['option_prefix'] ?? '';
				$title  = $page['title']         ?? $code;

				if ( ! $code || ! $prefix ) {
					continue;
				}

				// Only add the scaffolded section if it has not already been
				// defined in the static map above.
				if ( ! isset( $sections[ $code ] ) ) {
					$sections[ $code ] = [
						'label'    => $title,
						'prefixes' => [ $prefix ],
						'exact'    => [],
					];
				}
			}
		}

		return $sections;
	}

	/**
	 * Given a list of section IDs and the full live anna_theme_options array,
	 * returns all matching option keys using the following priority order:
	 *
	 *   1. Exact-key match (highest priority — immediate win).
	 *   2. Longest-prefix-wins for prefix matches across the requested sections.
	 *   3. Each key is assigned to exactly one section (no duplicates).
	 *
	 * Keys whose first character is `_` are NOT filtered out here; the
	 * exporter/importer decides what to include.
	 *
	 * @param string[] $section_ids  Keys from get_sections() to include.
	 * @param array    $all_options  Full anna_theme_options associative array.
	 * @return string[]  Flat, deduplicated list of matching option key names.
	 */
	public static function get_keys_for_sections( array $section_ids, array $all_options ): array {
		$sections = static::get_sections();

		// Build a filtered map containing only the requested sections.
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
				// ── 1. Exact-key match (trumps all prefix matches) ─────────────
				if ( in_array( $key, $section['exact'], true ) ) {
					$assigned_section = $section_id;
					$exact_match      = true;
					break; // Exact match wins immediately; stop checking sections.
				}

				// ── 2. Prefix match (longest prefix wins) ──────────────────────
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

		// array_values + array_unique guarantees a clean, zero-indexed list
		// with no duplicates (duplicates cannot arise given the algorithm, but
		// this is a defensive measure).
		return array_values( array_unique( $matched ) );
	}

	/**
	 * Returns the section ID that owns the given key, or null if no registered
	 * section claims it.
	 *
	 * Uses the same exact-match → longest-prefix-wins logic as
	 * get_keys_for_sections(), but searches across ALL registered sections
	 * rather than a restricted subset.
	 *
	 * @param string $key         The option key to look up.
	 * @param array  $all_options Full anna_theme_options array (used only to
	 *                            confirm the key actually exists; the lookup
	 *                            itself is registry-driven).
	 * @return string|null  The section ID, or null if unregistered.
	 */
	public static function get_section_for_key( string $key, array $all_options ): ?string {
		$sections        = static::get_sections();
		$assigned        = null;
		$best_prefix_len = 0;

		foreach ( $sections as $section_id => $section ) {
			// ── 1. Exact-key match ─────────────────────────────────────────────
			if ( in_array( $key, $section['exact'], true ) ) {
				return $section_id; // Exact match wins immediately.
			}

			// ── 2. Prefix match (longest prefix wins) ──────────────────────────
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
