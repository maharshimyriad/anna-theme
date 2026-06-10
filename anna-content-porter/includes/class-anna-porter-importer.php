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

		// Content-only import: do NOT recreate or overwrite images. Recreating
		// base64 images and thumbnails is slow and can cause 504 timeouts on hosts.
		$image_map = [];
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
				$existing = get_post_meta( $page_id, $meta_key, true );

				if ( 'skip' === $mode && ! empty( $existing ) ) {
					$skipped++;
					continue;
				}

				// Content-only import: merge incoming text/content fields over the
				// existing meta array while preserving image/media ID fields.
				$resolved = $this->merge_without_image_fields( $value, $existing );

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

			// Content-only import: never overwrite media/image option fields.
			if ( $this->is_image_field_key( $key ) ) {
				$skipped++;
				continue;
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
			'images_created' => 0,
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
	 * Returns true when a field key represents an image/media attachment ID.
	 * These fields are preserved during imports to avoid overwriting local media.
	 *
	 * @param string|int $key Field key.
	 * @return bool
	 */
	private function is_image_field_key( $key ): bool {
		$key = (string) $key;

		return (
			'image_id' === $key
			|| 'logo_id' === $key
			|| str_ends_with( $key, '_image_id' )
			|| str_ends_with( $key, '_logo_id' )
			|| str_ends_with( $key, '_media_id' )
			|| str_ends_with( $key, '_attachment_id' )
			|| str_ends_with( $key, '_id' )
		);
	}

	/**
	 * Merges incoming content over existing content while preserving image fields.
	 *
	 * If an incoming array contains image_id / *_id fields, the existing local
	 * value is kept. This makes imports content-only and prevents 504 timeouts
	 * caused by image recreation and thumbnail generation.
	 *
	 * @param mixed $incoming Incoming package value.
	 * @param mixed $existing Existing local value.
	 * @return mixed
	 */
	private function merge_without_image_fields( $incoming, $existing ) {
		if ( ! is_array( $incoming ) ) {
			return $incoming;
		}

		$result = is_array( $existing ) ? $existing : [];

		foreach ( $incoming as $key => $value ) {
			if ( $this->is_image_field_key( $key ) ) {
				continue;
			}

			if ( is_array( $value ) ) {
				$result[ $key ] = $this->merge_without_image_fields(
					$value,
					$result[ $key ] ?? []
				);
			} else {
				$result[ $key ] = $value;
			}
		}

		return $result;
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
