<?php
/**
 * Anna Porter Importer
 *
 * Validates an Export_Package, re-creates image attachments, and writes the
 * content back to the correct destination:
 *   - 'pages'   → _anna_content_* post meta on pages found by slug
 *   - 'content' → anna_theme_options (brand, footer, legacy)
 *
 * @package Anna_Content_Porter
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the two-step import process: preview (read-only) and full import.
 */
class Anna_Porter_Importer {

	/**
	 * @var string[]
	 */
	private array $warnings = [];

	// -------------------------------------------------------------------------
	// Public API
	// -------------------------------------------------------------------------

	/**
	 * Validates the package and returns preview data. Does not write anything.
	 *
	 * @param array $package  Decoded Export_Package.
	 * @return array
	 * @throws InvalidArgumentException  If the package fails validation.
	 */
	public function preview( array $package ): array {
		if (
			! isset( $package['meta']['plugin'] ) ||
			$package['meta']['plugin'] !== 'anna-content-porter'
		) {
			throw new InvalidArgumentException(
				'Invalid package: not an Anna Content Porter export.'
			);
		}

		if (
			! isset( $package['content'] ) ||
			! is_array( $package['content'] )
		) {
			// v2 packages may have an empty content array — check pages instead.
			if ( empty( $package['pages'] ) ) {
				throw new InvalidArgumentException(
					'Invalid package: missing both content and pages.'
				);
			}
		}

		$page_meta_count = 0;
		foreach ( ( $package['pages'] ?? [] ) as $meta_data ) {
			if ( is_array( $meta_data ) ) {
				$page_meta_count += count( $meta_data );
			}
		}

		$option_key_count = count( $package['content'] ?? [] );

		return [
			'exported_sections' => $package['meta']['exported_sections'] ?? [],
			'source_site_url'   => $package['meta']['source_site_url']   ?? '',
			'exported_at'       => $package['meta']['exported_at']       ?? '',
			'content_key_count' => $option_key_count + $page_meta_count,
			'option_key_count'  => $option_key_count,
			'page_meta_count'   => $page_meta_count,
			'page_count'        => count( $package['pages'] ?? [] ),
		];
	}

	/**
	 * Performs the full import.
	 *
	 * @param array  $package  Decoded Export_Package.
	 * @param string $mode     'overwrite' or 'skip'.
	 * @return array{ written: int, skipped: int, images_created: int, warnings: string[] }
	 */
	public function import( array $package, string $mode ): array {
		$this->warnings = [];

		$image_map = $this->recreate_images( $package['images'] ?? [] );
		$warnings  = $this->warnings;

		$written = 0;
		$skipped = 0;

		// ── Part 1: Post meta (pages — v2 format) ──────────────────────────────
		foreach ( ( $package['pages'] ?? [] ) as $page_key => $meta_data ) {
			if ( ! is_array( $meta_data ) ) {
				continue;
			}

			$page_id = $this->resolve_import_page( $page_key );

			if ( null === $page_id ) {
				$warnings[] = sprintf(
					'Could not find target page for "%s" — skipping all %d meta key(s) for this page.',
					$page_key,
					count( $meta_data )
				);
				$skipped += count( $meta_data );
				continue;
			}

			foreach ( $meta_data as $meta_key => $value ) {
				// Resolve image string references inside the value recursively.
				$resolved = $this->resolve_images_in_value( $value, $image_map );

				if ( 'skip' === $mode ) {
					$existing = get_post_meta( $page_id, $meta_key, true );
					if ( ! empty( $existing ) ) {
						$skipped++;
						continue;
					}
				}

				update_post_meta( $page_id, $meta_key, $resolved );
				$written++;
			}
		}

		// ── Part 2: anna_theme_options (brand, footer, legacy v1 content) ──────
		$live_options = get_option( 'anna_theme_options', [] );
		$merged       = [];

		foreach ( ( $package['content'] ?? [] ) as $key => $value ) {
			if ( str_starts_with( $key, '_' ) ) {
				continue;
			}

			$section = Anna_Porter_Registry::get_section_for_key( $key, $live_options );
			if ( null === $section ) {
				$warnings[] = "Rejected unknown key: {$key}";
				continue;
			}

			// Resolve image references.
			if ( is_string( $value ) && isset( $package['images'][ $value ] ) ) {
				if ( 'skip' === $mode ) {
					$live_val = $live_options[ $key ] ?? 0;
					if ( is_int( $live_val ) && $live_val > 0 ) {
						$skipped++;
						continue;
					}
				}
				$value = $image_map[ $value ] ?? 0;
			} elseif (
				is_int( $value ) &&
				$value > 0 &&
				isset( $package['images'][ (string) $value ] )
			) {
				$warnings[] = "Non-portable attachment ID {$value} for key {$key}";
				$value = 0;
			}

			$sanitised = $this->sanitise_value( $key, $value );

			if ( ! $this->should_write( $key, $sanitised, $live_options, $mode ) ) {
				$skipped++;
				continue;
			}

			$merged[ $key ] = $sanitised;
			$written++;
		}

		if ( ! empty( $merged ) ) {
			$final   = array_merge( $live_options, $merged );
			$updated = update_option( 'anna_theme_options', $final );
			if ( false === $updated && $final !== $live_options ) {
				$warnings[] = 'update_option failed: anna_theme_options could not be saved.';
			}
		}

		return [
			'written'        => $written,
			'skipped'        => $skipped,
			'images_created' => count( $image_map ),
			'warnings'       => $warnings,
		];
	}

	// -------------------------------------------------------------------------
	// Private helpers
	// -------------------------------------------------------------------------

	/**
	 * Resolves a page_key from the export package to a local post ID.
	 *
	 * @param string $page_key  '__front__' or a page slug.
	 * @return int|null
	 */
	private function resolve_import_page( string $page_key ): ?int {
		if ( '__front__' === $page_key ) {
			$id = (int) get_option( 'page_on_front' );
			return $id > 0 ? $id : null;
		}

		$post = get_page_by_path( $page_key );
		return $post ? $post->ID : null;
	}

	/**
	 * Recursively walks a value and replaces any string image references
	 * (e.g. "98") with the new local attachment ID from $image_map.
	 *
	 * @param mixed                $value
	 * @param array<string, int>   $image_map
	 * @return mixed
	 */
	private function resolve_images_in_value( $value, array $image_map ) {
		if ( is_array( $value ) ) {
			$result = [];
			foreach ( $value as $k => $v ) {
				$result[ $k ] = $this->resolve_images_in_value( $v, $image_map );
			}
			return $result;
		}

		if ( is_string( $value ) && isset( $image_map[ $value ] ) ) {
			return $image_map[ $value ];
		}

		return $value;
	}

	/**
	 * Decodes base64 image payloads, uploads each to the WP media library,
	 * and returns a map of exported string key to new local attachment ID.
	 *
	 * @param array $images  The 'images' object from the Export_Package.
	 * @return array<string, int>
	 */
	private function recreate_images( array $images ): array {
		$image_map = [];

		foreach ( $images as $string_key => $payload ) {
			$decoded = base64_decode( $payload['base64_data'] ?? '', true );
			if ( false === $decoded ) {
				$this->warnings[] = "Base64 decode failed for image key {$string_key}";
				continue;
			}

			$filename   = sanitize_file_name( $payload['original_filename'] ?? 'import.jpg' );
			$tmp        = wp_tempnam( $filename );
			file_put_contents( $tmp, $decoded ); // phpcs:ignore WordPress.WP.AlternativeFunctions

			$upload_dir = wp_upload_dir();
			$dest       = $upload_dir['path'] . '/' . wp_unique_filename( $upload_dir['path'], $filename );

			if ( ! rename( $tmp, $dest ) ) {
				@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				$this->warnings[] = "Could not move temp file to uploads for image key {$string_key}";
				continue;
			}

			$filetype   = wp_check_filetype( basename( $dest ) );
			$attachment = [
				'post_mime_type' => $filetype['type'] ?: ( $payload['mime_type'] ?? 'image/jpeg' ),
				'post_title'     => pathinfo( $filename, PATHINFO_FILENAME ),
				'post_status'    => 'inherit',
			];

			$attach_id = wp_insert_attachment( $attachment, $dest );

			if ( is_wp_error( $attach_id ) ) {
				@unlink( $dest ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				$this->warnings[] = "Failed to create attachment for image key {$string_key}: " .
					$attach_id->get_error_message();
				continue;
			}

			require_once ABSPATH . 'wp-admin/includes/image.php';
			$meta = wp_generate_attachment_metadata( $attach_id, $dest );
			wp_update_attachment_metadata( $attach_id, $meta );

			$image_map[ $string_key ] = $attach_id;
		}

		return $image_map;
	}

	/**
	 * Sanitises a single option value (used for anna_theme_options keys).
	 *
	 * @param string $key
	 * @param mixed  $value
	 * @return mixed
	 */
	private function sanitise_value( string $key, $value ) {
		if ( is_array( $value ) ) {
			$sanitised = [];
			foreach ( $value as $sub_key => $sub_value ) {
				$sanitised[ $sub_key ] = $this->sanitise_value( (string) $sub_key, $sub_value );
			}
			return $sanitised;
		}

		if ( str_ends_with( $key, '_url' ) ) {
			return esc_url_raw( (string) $value );
		}

		if ( str_ends_with( $key, '_id' ) ) {
			return absint( $value );
		}

		if ( str_ends_with( $key, '_color' ) || str_starts_with( $key, 'color_' ) ) {
			return preg_match( '/^#[0-9a-fA-F]{3,8}$/', (string) $value )
				? (string) $value
				: '';
		}

		if ( str_ends_with( $key, '_enabled' ) || str_ends_with( $key, '_toggle' ) ) {
			return (bool) (int) $value;
		}

		if (
			str_contains( $key, 'body' ) ||
			str_contains( $key, 'description' ) ||
			str_contains( $key, 'items_text' )
		) {
			return wp_kses_post( (string) $value );
		}

		return wp_kses_post( (string) $value );
	}

	/**
	 * Determines whether a key should be written (used for anna_theme_options).
	 *
	 * @param string $key
	 * @param mixed  $incoming
	 * @param array  $live_options
	 * @param string $mode
	 * @return bool
	 */
	private function should_write( string $key, $incoming, array $live_options, string $mode ): bool {
		if ( 'overwrite' === $mode ) {
			return true;
		}

		$live = $live_options[ $key ] ?? null;

		if ( is_array( $live ) ) {
			return empty( $live );
		}

		if ( is_int( $live ) || ( is_string( $live ) && ctype_digit( $live ) ) ) {
			return 0 === absint( $live );
		}

		return '' === trim( (string) ( $live ?? '' ) );
	}
}
