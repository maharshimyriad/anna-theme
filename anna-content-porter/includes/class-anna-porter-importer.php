<?php
/**
 * Anna Porter Importer
 *
 * Validates an Export_Package, re-creates image attachments, sanitises
 * incoming values, and writes the result into anna_theme_options.
 *
 * @package Anna_Content_Porter
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the two-step import process: preview (read-only validation) and
 * the full import (image re-creation + option write).
 */
class Anna_Porter_Importer {

	/**
	 * Warnings accumulated during image re-creation.
	 * Populated by recreate_images() so that import() can collect them.
	 *
	 * @var string[]
	 */
	private array $warnings = [];

	// -------------------------------------------------------------------------
	// Public API
	// -------------------------------------------------------------------------

	/**
	 * Validates the package and returns preview data for the confirmation UI.
	 * Does NOT write anything to the database.
	 *
	 * @param array $package  Decoded Export_Package (from json_decode $assoc=true).
	 * @return array{
	 *   exported_sections: string[],
	 *   source_site_url: string,
	 *   exported_at: string,
	 *   content_key_count: int
	 * }
	 * @throws InvalidArgumentException  If the package fails validation.
	 */
	public function preview( array $package ): array {
		// Validate plugin marker.
		if (
			! isset( $package['meta']['plugin'] ) ||
			$package['meta']['plugin'] !== 'anna-content-porter'
		) {
			throw new InvalidArgumentException(
				'Invalid package: not an Anna Content Porter export.'
			);
		}

		// Validate content array.
		if ( ! isset( $package['content'] ) || ! is_array( $package['content'] ) ) {
			throw new InvalidArgumentException(
				'Invalid package: missing content.'
			);
		}

		return [
			'exported_sections' => $package['meta']['exported_sections'] ?? [],
			'source_site_url'   => $package['meta']['source_site_url']   ?? '',
			'exported_at'       => $package['meta']['exported_at']       ?? '',
			'content_key_count' => count( $package['content'] ),
		];
	}

	/**
	 * Performs the full import: re-creates images, sanitises values, applies
	 * import mode, and writes the merged result to anna_theme_options.
	 *
	 * @param array  $package  Decoded Export_Package.
	 * @param string $mode     'overwrite' or 'skip'.
	 * @return array{
	 *   written: int,
	 *   skipped: int,
	 *   images_created: int,
	 *   warnings: string[]
	 * }
	 */
	public function import( array $package, string $mode ): array {
		// Reset warnings for this run.
		$this->warnings = [];

		// Fetch live options — used for skip-mode comparisons and the final merge.
		$live_options = get_option( 'anna_theme_options', [] );

		// Use the same live options array for Registry key checking.
		$all_options = $live_options;

		// Re-create images first so we have a complete $image_map before
		// iterating content keys.
		$image_map = $this->recreate_images( $package['images'] ?? [] );

		// Collect any warnings produced by recreate_images().
		$warnings = $this->warnings;

		$written = 0;
		$skipped = 0;
		$merged  = [];

		foreach ( $package['content'] as $key => $value ) {
			// 1. Skip internal WP keys.
			if ( str_starts_with( $key, '_' ) ) {
				continue;
			}

			// 2. Reject keys not registered in the Registry.
			$section = Anna_Porter_Registry::get_section_for_key( $key, $all_options );
			if ( null === $section ) {
				$warnings[] = "Rejected unknown key: {$key}";
				continue;
			}

			// 3. Resolve image string references.
			if ( is_string( $value ) && isset( $package['images'][ $value ] ) ) {
				// Skip mode: if live value is already a non-zero media ID, leave it.
				if ( 'skip' === $mode ) {
					$live_val = $live_options[ $key ] ?? 0;
					if ( is_int( $live_val ) && $live_val > 0 ) {
						$skipped++;
						continue;
					}
				}

				$new_id = $image_map[ $value ] ?? 0;
				$value  = $new_id; // 0 when image creation failed.
			} elseif (
				// 4. Handle raw int media refs that could not be exported as base64.
				is_int( $value ) &&
				$value > 0 &&
				isset( $package['images'][ (string) $value ] )
			) {
				$warnings[] = "Non-portable attachment ID {$value} for key {$key}";
				$value = 0;
			}

			// 5. Sanitise the value.
			$sanitised = $this->sanitise_value( $key, $value );

			// 6. Apply import mode — should we write this key?
			if ( ! $this->should_write( $key, $sanitised, $live_options, $mode ) ) {
				$skipped++;
				continue;
			}

			// 7. Stage for merge.
			$merged[ $key ] = $sanitised;
			$written++;
		}

		// Merge staged keys over the live options.
		$final = array_merge( $live_options, $merged );

		// Persist. update_option returns false both when the value didn't change
		// AND on genuine DB failure, so we only warn on actual divergence.
		$updated = update_option( 'anna_theme_options', $final );
		if ( false === $updated && $final !== $live_options ) {
			$warnings[] = 'update_option failed: anna_theme_options could not be saved.';
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
	 * Decodes base64 image payloads, uploads each file to the WP media library,
	 * and returns a map of exported string key → new local attachment ID.
	 *
	 * Failures are recorded in $this->warnings; processing continues on error.
	 *
	 * @param array $images  The 'images' object from the Export_Package.
	 * @return array<string, int>  e.g. ['42' => 187, '55' => 188]
	 */
	private function recreate_images( array $images ): array {
		$image_map = [];

		foreach ( $images as $string_key => $payload ) {
			// Decode base64 data (strict mode — false on invalid input).
			$decoded = base64_decode( $payload['base64_data'] ?? '', true );
			if ( false === $decoded ) {
				$this->warnings[] = "Base64 decode failed for image key {$string_key}";
				continue;
			}

			// Sanitise the original filename.
			$filename = sanitize_file_name( $payload['original_filename'] ?? 'import.jpg' );

			// Write decoded bytes to a temp file.
			$tmp = wp_tempnam( $filename );
			file_put_contents( $tmp, $decoded );

			// Determine the upload destination.
			$upload_dir = wp_upload_dir();
			$dest       = $upload_dir['path'] . '/' . wp_unique_filename( $upload_dir['path'], $filename );

			// Move temp file to the uploads directory.
			if ( ! rename( $tmp, $dest ) ) {
				// rename() failed — clean up and continue.
				@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				$this->warnings[] = "Could not move temp file to uploads for image key {$string_key}";
				continue;
			}

			// Detect MIME type from the destination filename.
			$filetype = wp_check_filetype( basename( $dest ) );

			// Build the attachment post array.
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

			// Generate and store attachment metadata (thumbnails, etc.).
			require_once ABSPATH . 'wp-admin/includes/image.php';
			$meta = wp_generate_attachment_metadata( $attach_id, $dest );
			wp_update_attachment_metadata( $attach_id, $meta );

			$image_map[ $string_key ] = $attach_id;
		}

		return $image_map;
	}

	/**
	 * Sanitises a single value using field-type rules inferred from the key name.
	 * Arrays are processed recursively.
	 *
	 * @param string $key    Option key (used to infer type hints).
	 * @param mixed  $value  Raw value from the package.
	 * @return mixed  Sanitised value.
	 */
	private function sanitise_value( string $key, $value ) {
		// Recurse into arrays (repeater fields).
		if ( is_array( $value ) ) {
			$sanitised = [];
			foreach ( $value as $sub_key => $sub_value ) {
				$sanitised[ $sub_key ] = $this->sanitise_value( (string) $sub_key, $sub_value );
			}
			return $sanitised;
		}

		// URL fields.
		if ( str_ends_with( $key, '_url' ) ) {
			return esc_url_raw( (string) $value );
		}

		// Media / integer ID fields.
		if ( str_ends_with( $key, '_id' ) ) {
			return absint( $value );
		}

		// Color fields (hex validation).
		if ( str_ends_with( $key, '_color' ) || str_starts_with( $key, 'color_' ) ) {
			return preg_match( '/^#[0-9a-fA-F]{3,8}$/', (string) $value )
				? (string) $value
				: '';
		}

		// Boolean toggle fields.
		if ( str_ends_with( $key, '_enabled' ) || str_ends_with( $key, '_toggle' ) ) {
			return (bool) (int) $value;
		}

		// Textarea / long-text fields.
		if (
			str_contains( $key, 'body' ) ||
			str_contains( $key, 'description' ) ||
			str_contains( $key, 'items_text' )
		) {
			return sanitize_textarea_field( (string) $value );
		}

		// Default: plain text scalar.
		return sanitize_text_field( (string) $value );
	}

	/**
	 * Determines whether a key should be written based on the import mode and
	 * the current live value.
	 *
	 * Overwrite mode: always write.
	 * Skip mode: only write when the live value is considered empty.
	 *
	 * @param string $key            Option key.
	 * @param mixed  $incoming       Sanitised incoming value (not used in skip logic).
	 * @param array  $live_options   Current anna_theme_options.
	 * @param string $mode           'overwrite' or 'skip'.
	 * @return bool  True if the key should be written.
	 */
	private function should_write( string $key, $incoming, array $live_options, string $mode ): bool {
		if ( 'overwrite' === $mode ) {
			return true;
		}

		// Skip mode: write only when the live value is empty.
		$live = $live_options[ $key ] ?? null;

		// Repeater / array field.
		if ( is_array( $live ) ) {
			return empty( $live );
		}

		// Media / integer field.
		if ( is_int( $live ) || ( is_string( $live ) && ctype_digit( $live ) ) ) {
			return 0 === absint( $live );
		}

		// Default scalar: empty string check.
		return '' === trim( (string) ( $live ?? '' ) );
	}
}
