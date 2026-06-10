<?php
/**
 * Anna Porter Exporter
 *
 * Reads live content from _anna_content_* post meta on specific pages
 * (for page/section content) and from anna_theme_options (for global brand /
 * footer data). Resolves attachment IDs to base64 payloads and streams the
 * result as a JSON download.
 *
 * @package Anna_Content_Porter
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds and streams an Export_Package JSON download.
 */
class Anna_Porter_Exporter {

	// -------------------------------------------------------------------------
	// Public API
	// -------------------------------------------------------------------------

	/**
	 * Entry point called from the admin-post handler.
	 *
	 * Builds the package, validates it is non-empty, sends download headers,
	 * echoes JSON, and exits.
	 *
	 * @param string[] $section_ids Section IDs selected by the admin user.
	 * @return void  Never returns.
	 */
	public function export( array $section_ids ): void {
		@ini_set( 'memory_limit', '256M' ); // phpcs:ignore WordPress.PHP.IniSet.Risky

		$package = $this->build_package( $section_ids );

		if ( empty( $package['content'] ) && empty( $package['pages'] ) ) {
			wp_die( 'No content found for the selected sections.' );
		}

		$filename = 'anna-content-porter-' . gmdate( 'Y-m-d' ) . '.json';

		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Cache-Control: no-cache, must-revalidate' );

		echo json_encode( $package, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode

		exit;
	}

	/**
	 * Assembles the Export_Package without sending headers.
	 *
	 * For sections with post_meta_page/post_meta_keys defined in the registry,
	 * reads from _anna_content_* post meta on the relevant page.
	 * For sections without (brand, footer_social), reads from anna_theme_options.
	 *
	 * @param string[] $section_ids Section IDs to include.
	 * @return array
	 */
	public function build_package( array $section_ids ): array {
		// Bust the object cache so we always get fresh DB data.
		wp_cache_delete( 'anna_theme_options', 'options' );
		$all_options = get_option( 'anna_theme_options', [] );

		$sections = Anna_Porter_Registry::get_sections();

		$option_content = []; // anna_theme_options keys (brand, footer_social)
		$page_content   = []; // _anna_content_* post meta keyed by page slug
		$images         = [];
		$warnings       = [];

		foreach ( $section_ids as $section_id ) {
			if ( ! isset( $sections[ $section_id ] ) ) {
				continue;
			}
			$section = $sections[ $section_id ];

			if ( ! empty( $section['post_meta_page'] ) && '__all__' === $section['post_meta_page'] ) {
				// ── Export every live page-content meta row across the site ───
				foreach ( $this->get_all_page_content_meta_rows() as $row ) {
					$page_key = $this->page_key_for_post( (int) $row->post_id );
					$value    = maybe_unserialize( $row->meta_value );

					if ( is_array( $value ) ) {
						$processed = $this->process_repeater( $value, $images, $warnings );
					} else {
						$processed = $value;
					}

					$page_content[ $page_key ][ $row->meta_key ] = $processed;

					// Keep the legacy flat mirror only for the front page. Multiple
					// pages can have fields with the same names, so `pages` is the
					// authoritative multi-page export format.
					if ( '__front__' === $page_key ) {
						foreach ( $this->flatten_post_meta_content( $row->meta_key, $processed ) as $flat_key => $flat_value ) {
							$option_content[ $flat_key ] = $flat_value;
						}
					}
				}
			} elseif ( ! empty( $section['post_meta_page'] ) ) {
				// ── Live post-meta source for one page ─────────────────────────
				$page_id = $this->resolve_page_id(
					$section['post_meta_page'],
					$section['post_meta_keys'] ?? []
				);

				if ( null === $page_id ) {
					$warnings[] = sprintf(
						'Section "%s": could not find page "%s". Skipping.',
						$section_id,
						$section['post_meta_page']
					);
					continue;
				}

				$page_slug = $this->page_key_for_post( $page_id );
				$rows      = $this->get_page_content_meta_rows( $page_id );

				if ( empty( $rows ) ) {
					$warnings[] = sprintf(
						'Section "%s": page %d has no _anna_content_* meta rows.',
						$section_id,
						$page_id
					);
					continue;
				}

				foreach ( $rows as $row ) {
					$value = maybe_unserialize( $row->meta_value );

					if ( is_array( $value ) ) {
						$processed = $this->process_repeater( $value, $images, $warnings );
					} else {
						$processed = $value;
					}

					$page_content[ $page_slug ][ $row->meta_key ] = $processed;

					// Only mirror the front page into legacy flat content, because
					// multiple inner pages can share field names.
					if ( '__front__' === $page_slug ) {
						foreach ( $this->flatten_post_meta_content( $row->meta_key, $processed ) as $flat_key => $flat_value ) {
							$option_content[ $flat_key ] = $flat_value;
						}
					}
				}
			} else {
				// ── anna_theme_options fallback (brand, footer_social) ─────────
				$matched_keys = Anna_Porter_Registry::get_keys_for_sections(
					[ $section_id ],
					$all_options
				);

				foreach ( $matched_keys as $key ) {
					$value = $all_options[ $key ] ?? null;

					if ( is_array( $value ) ) {
						$option_content[ $key ] = $this->process_repeater( $value, $images, $warnings );
					} elseif ( is_int( $value ) && $value > 0 ) {
						$payload = $this->resolve_image( $value );
						if ( null !== $payload ) {
							$images[ (string) $value ]  = $payload;
							$option_content[ $key ]     = (string) $value;
						} else {
							$option_content[ $key ] = $value;
							$warnings[]             = sprintf(
								'Could not read file for attachment ID %d (key: %s)',
								$value,
								$key
							);
						}
					} else {
						$option_content[ $key ] = $value;
					}
				}
			}
		}

		return [
			'meta'            => [
				'plugin'            => 'anna-content-porter',
				'format_version'    => 2,
				'theme_version'     => $this->get_theme_version(),
				'exported_at'       => gmdate( 'c' ),
				'source_site_url'   => get_home_url(),
				'exported_sections' => $this->get_section_labels( $section_ids ),
			],
			'content'         => $option_content,
			'pages'           => $page_content,
			'images'          => $images,
			'export_warnings' => $warnings,
		];
	}

	// -------------------------------------------------------------------------
	// Private helpers
	// -------------------------------------------------------------------------

	/**
	 * Returns every _anna_content_* meta row for one page.
	 *
	 * @param int $page_id Page/post ID.
	 * @return array<int, object>
	 */
	private function get_page_content_meta_rows( int $page_id ): array {
		global $wpdb;

		$like = $wpdb->esc_like( '_anna_content_' ) . '%';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT meta_key, meta_value
				 FROM {$wpdb->postmeta}
				 WHERE post_id = %d
				   AND meta_key LIKE %s
				 ORDER BY meta_key ASC",
				$page_id,
				$like
			)
		);

		return is_array( $rows ) ? $rows : [];
	}

	/**
	 * Returns every page/post meta row that stores live Anna content.
	 *
	 * @return array<int, object>
	 */
	private function get_all_page_content_meta_rows(): array {
		global $wpdb;

		$like = $wpdb->esc_like( '_anna_content_' ) . '%';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT pm.post_id, pm.meta_key, pm.meta_value
				 FROM {$wpdb->postmeta} pm
				 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				 WHERE pm.meta_key LIKE %s
				   AND p.post_type = 'page'
				   AND p.post_status NOT IN ('trash','auto-draft')
				 ORDER BY p.menu_order ASC, p.post_title ASC, pm.meta_key ASC",
				$like
			)
		);

		return is_array( $rows ) ? $rows : [];
	}

	/**
	 * Returns the package page key for a post ID.
	 *
	 * @param int $post_id Page ID.
	 * @return string
	 */
	private function page_key_for_post( int $post_id ): string {
		$front_id = (int) get_option( 'page_on_front' );
		if ( $front_id > 0 && $post_id === $front_id ) {
			return '__front__';
		}

		$post = get_post( $post_id );
		if ( $post && ! empty( $post->post_name ) ) {
			return $post->post_name;
		}

		return 'page-' . $post_id;
	}

	/**
	 * Resolves a page reference to a post ID.
	 *
	 * Priority:
	 *   1. '__front__' → get_option('page_on_front')
	 *   2. Slug match via get_page_by_path()
	 *   3. Auto-discover: first published page with any of $meta_keys
	 *
	 * @param string   $page_ref   '__front__' or a page slug.
	 * @param string[] $meta_keys  _anna_content_* keys to look for as fallback.
	 * @return int|null
	 */
	private function resolve_page_id( string $page_ref, array $meta_keys ): ?int {
		if ( '__front__' === $page_ref ) {
			$id = (int) get_option( 'page_on_front' );
			return $id > 0 ? $id : null;
		}

		$post = get_page_by_path( $page_ref );
		if ( $post ) {
			return $post->ID;
		}

		// Auto-discover: any published page that already has one of the meta keys.
		global $wpdb;
		foreach ( $meta_keys as $meta_key ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			$post_id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT pm.post_id
					 FROM {$wpdb->postmeta} pm
					 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
					 WHERE pm.meta_key = %s
					   AND p.post_status = 'publish'
					   AND p.post_type  = 'page'
					 LIMIT 1",
					$meta_key
				)
			);

			if ( $post_id ) {
				return (int) $post_id;
			}
		}

		return null;
	}

	/**
	 * Flattens a _anna_content_* post-meta array into legacy anna_theme_options
	 * style keys for export compatibility and easier JSON inspection.
	 *
	 * @param string $meta_key  The _anna_content_* meta key.
	 * @param mixed  $value     Processed meta value.
	 * @return array<string,mixed>
	 */
	private function flatten_post_meta_content( string $meta_key, $value ): array {
		if ( ! is_array( $value ) ) {
			return [];
		}

		$prefix_map = [
			// New single-row home page format — sections are nested arrays, not flat keys.
			// We skip flattening for this key; the nested structure is already canonical.
			'_anna_content_home_page'    => null,
			'_anna_content_about_pg'     => 'about_pg_',
			'_anna_content_coaching_pg'  => 'coaching_pg_',
			'_anna_content_oasis_pg'     => 'oasis_pg_',
			'_anna_content_speaking_pg'  => 'speaking_pg_',
			'_anna_content_mhs_pg'       => 'mhs_pg_',
			'_anna_content_move_pg'      => 'move_pg_',
			'_anna_content_reviews_pg'   => 'reviews_pg_',
			'_anna_content_contact_pg'   => 'contact_pg_',
		];

		// null prefix means the value is a nested structure — do not flatten.
		if ( array_key_exists( $meta_key, $prefix_map ) && null === $prefix_map[ $meta_key ] ) {
			return [];
		}

		$prefix = $prefix_map[ $meta_key ] ?? '';
		$flat   = [];

		foreach ( $value as $field_key => $field_value ) {
			$field_key = (string) $field_key;

			// Intro meta already stores many fully-prefixed keys such as
			// intro_body and recognition_items_text. Keep those as-is.
			if (
				'' === $prefix
				|| str_starts_with( $field_key, $prefix )
				|| str_starts_with( $field_key, 'intro_' )
				|| str_starts_with( $field_key, 'recognition_' )
			) {
				$flat[ $field_key ] = $field_value;
			} else {
				$flat[ $prefix . $field_key ] = $field_value;
			}
		}

		return $flat;
	}

	/**
	 * Resolves a single attachment ID to an Image_Payload array.
	 *
	 * @param int $attachment_id  WordPress attachment post ID.
	 * @return array|null  Null when the file is unreadable.
	 */
	private function resolve_image( int $attachment_id ): ?array {
		$path = get_attached_file( $attachment_id );

		if ( ! $path || ! is_readable( $path ) ) {
			return null;
		}

		$mime = get_post_mime_type( $attachment_id ) ?: 'application/octet-stream';
		$src  = wp_get_attachment_url( $attachment_id ) ?: '';
		$b64  = base64_encode( file_get_contents( $path ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode,WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		return [
			'original_filename' => basename( $path ),
			'mime_type'         => $mime,
			'source_url'        => $src,
			'base64_data'       => $b64,
		];
	}

	/**
	 * Recursively walks an array and resolves any sub-fields whose key ends
	 * with `_id` and whose value is an int > 0 as attachment IDs.
	 *
	 * @param array               $items
	 * @param array<string,array> &$images
	 * @param string[]            &$warnings
	 * @return array
	 */
	private function process_repeater( array $items, array &$images, array &$warnings ): array {
		$processed = [];

		foreach ( $items as $row_key => $row_value ) {
			if ( is_array( $row_value ) ) {
				$processed_row = [];

				foreach ( $row_value as $sub_key => $sub_value ) {
					if ( is_array( $sub_value ) ) {
						$processed_row[ $sub_key ] = $this->process_repeater( $sub_value, $images, $warnings );
					} elseif (
						is_string( $sub_key )
						&& str_ends_with( $sub_key, '_id' )
						&& is_int( $sub_value )
						&& $sub_value > 0
					) {
						$payload = $this->resolve_image( $sub_value );
						if ( null !== $payload ) {
							$images[ (string) $sub_value ] = $payload;
							$processed_row[ $sub_key ]     = (string) $sub_value;
						} else {
							$processed_row[ $sub_key ] = $sub_value;
							$warnings[]                = sprintf(
								'Could not read file for attachment ID %d (sub-key: %s)',
								$sub_value,
								$sub_key
							);
						}
					} else {
						$processed_row[ $sub_key ] = $sub_value;
					}
				}

				$processed[ $row_key ] = $processed_row;
			} elseif (
				is_string( $row_key )
				&& str_ends_with( $row_key, '_id' )
				&& is_int( $row_value )
				&& $row_value > 0
			) {
				$payload = $this->resolve_image( $row_value );
				if ( null !== $payload ) {
					$images[ (string) $row_value ] = $payload;
					$processed[ $row_key ]         = (string) $row_value;
				} else {
					$processed[ $row_key ] = $row_value;
					$warnings[]            = sprintf(
						'Could not read file for attachment ID %d (key: %s)',
						$row_value,
						$row_key
					);
				}
			} else {
				$processed[ $row_key ] = $row_value;
			}
		}

		return $processed;
	}

	/**
	 * Returns the Version string from the active theme's style.css header.
	 *
	 * @return string
	 */
	private function get_theme_version(): string {
		return wp_get_theme()->get( 'Version' ) ?: '';
	}

	/**
	 * Returns human-readable section labels for the given IDs.
	 *
	 * @param string[] $section_ids
	 * @return string[]
	 */
	private function get_section_labels( array $section_ids ): array {
		$sections = Anna_Porter_Registry::get_sections();
		$labels   = [];

		foreach ( $section_ids as $id ) {
			if ( isset( $sections[ $id ]['label'] ) ) {
				$labels[] = $sections[ $id ]['label'];
			}
		}

		return $labels;
	}
}
