<?php
/**
 * Anna Porter Exporter
 *
 * Pulls the relevant keys from anna_theme_options, resolves any Media_Field
 * attachment IDs to base64 Image_Payload arrays, assembles the Export_Package
 * JSON structure, and streams it as a browser file-download.
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
	 * Main entry point called from the admin-post handler.
	 *
	 * Resolves the matched keys, assembles the Export_Package, sends the
	 * appropriate download headers, echoes JSON, and exits.
	 *
	 * @param string[] $section_ids Section IDs selected by the admin user.
	 * @return void  Never returns — calls exit after streaming the download.
	 */
	public function export( array $section_ids ): void {
		$all_options = get_option( 'anna_theme_options', [] );
		$matched     = Anna_Porter_Registry::get_keys_for_sections( $section_ids, $all_options );

		if ( empty( $matched ) ) {
			wp_die( 'No content found for the selected sections.' );
		}

		// Allow larger exports to complete without hitting the default PHP
		// memory ceiling.  The @ suppressor is intentional — ini_set may be
		// disabled on some hosts and the export should still proceed.
		@ini_set( 'memory_limit', '256M' ); // phpcs:ignore WordPress.PHP.IniSet.Risky

		$package = $this->build_package( $section_ids );

		$filename = 'anna-content-porter-' . gmdate( 'Y-m-d' ) . '.json';

		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Cache-Control: no-cache, must-revalidate' );

		echo json_encode( $package, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode

		exit;
	}

	/**
	 * Assembles the Export_Package array without sending any headers.
	 *
	 * Separated from export() so it can be called independently (e.g. in
	 * automated tests) without triggering a download.
	 *
	 * @param string[] $section_ids Section IDs to include in the package.
	 * @return array{
	 *   meta: array{
	 *     plugin: string,
	 *     theme_version: string,
	 *     exported_at: string,
	 *     source_site_url: string,
	 *     exported_sections: string[]
	 *   },
	 *   content: array<string, mixed>,
	 *   images: array<string, array{
	 *     original_filename: string,
	 *     mime_type: string,
	 *     source_url: string,
	 *     base64_data: string
	 *   }>,
	 *   export_warnings: string[]
	 * }
	 */
	public function build_package( array $section_ids ): array {
		$all_options  = get_option( 'anna_theme_options', [] );
		$matched_keys = Anna_Porter_Registry::get_keys_for_sections( $section_ids, $all_options );

		$content  = [];
		$images   = [];
		$warnings = [];

		foreach ( $matched_keys as $key ) {
			$value = $all_options[ $key ] ?? null;

			if ( is_array( $value ) ) {
				// ── Repeater_Field ─────────────────────────────────────────────
				$content[ $key ] = $this->process_repeater( $value, $images, $warnings );

			} elseif ( is_int( $value ) && $value > 0 ) {
				// ── Media_Field ────────────────────────────────────────────────
				$payload = $this->resolve_image( $value );

				if ( null !== $payload ) {
					$images[ (string) $value ] = $payload;
					$content[ $key ]           = (string) $value; // string reference
				} else {
					$content[ $key ] = $value; // keep raw int
					$warnings[]      = sprintf(
						'Could not read file for attachment ID %d (key: %s)',
						$value,
						$key
					);
				}
			} else {
				// ── Scalar_Field ───────────────────────────────────────────────
				$content[ $key ] = $value;
			}
		}

		return [
			'meta'            => [
				'plugin'            => 'anna-content-porter',
				'theme_version'     => $this->get_theme_version(),
				'exported_at'       => gmdate( 'c' ),
				'source_site_url'   => get_home_url(),
				'exported_sections' => $this->get_section_labels( $section_ids ),
			],
			'content'         => $content,
			'images'          => $images,
			'export_warnings' => $warnings,
		];
	}

	// -------------------------------------------------------------------------
	// Private helpers
	// -------------------------------------------------------------------------

	/**
	 * Resolves a single attachment ID to an Image_Payload array.
	 *
	 * Reads the physical file from disk, base64-encodes its contents, and
	 * bundles the result with metadata needed to recreate the attachment on
	 * the destination site.
	 *
	 * Returns null when the attachment file cannot be found or read (e.g. the
	 * file has been deleted from the server while the database record remains).
	 *
	 * @param int $attachment_id  WordPress attachment post ID.
	 * @return array{
	 *   original_filename: string,
	 *   mime_type: string,
	 *   source_url: string,
	 *   base64_data: string
	 * }|null  Null when the file is unreadable.
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
	 * Recursively walks a Repeater_Field array and resolves any sub-fields
	 * that look like attachment IDs (key ends with `_id`, value is int > 0).
	 *
	 * Sub-values that are themselves arrays are recursed into, allowing
	 * arbitrarily nested repeaters.
	 *
	 * @param array              $items     The repeater rows to process.
	 * @param array<string,array> &$images   Image payload map, passed by reference
	 *                                        so resolved payloads are collected into
	 *                                        the package-level images object.
	 * @param string[]           &$warnings  Warning messages, passed by reference.
	 * @return array  The processed repeater array.
	 */
	private function process_repeater( array $items, array &$images, array &$warnings ): array {
		$processed = [];

		foreach ( $items as $row_key => $row_value ) {
			if ( is_array( $row_value ) ) {
				// ── Nested row / sub-repeater ──────────────────────────────────
				$processed_row = [];

				foreach ( $row_value as $sub_key => $sub_value ) {
					if ( is_array( $sub_value ) ) {
						// Sub-field is itself an array — recurse one level deeper.
						$processed_row[ $sub_key ] = $this->process_repeater( $sub_value, $images, $warnings );

					} elseif (
						is_string( $sub_key )
						&& str_ends_with( $sub_key, '_id' )
						&& is_int( $sub_value )
						&& $sub_value > 0
					) {
						// Sub-field looks like a Media_Field attachment ID.
						$payload = $this->resolve_image( $sub_value );

						if ( null !== $payload ) {
							$images[ (string) $sub_value ] = $payload;
							$processed_row[ $sub_key ]     = (string) $sub_value;
						} else {
							$processed_row[ $sub_key ] = $sub_value;
							$warnings[]                = sprintf(
								'Could not read file for attachment ID %d (repeater sub-key: %s)',
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
				// ── Top-level row that is itself a Media_Field ─────────────────
				$payload = $this->resolve_image( $row_value );

				if ( null !== $payload ) {
					$images[ (string) $row_value ] = $payload;
					$processed[ $row_key ]         = (string) $row_value;
				} else {
					$processed[ $row_key ] = $row_value;
					$warnings[]            = sprintf(
						'Could not read file for attachment ID %d (repeater key: %s)',
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
	 * Falls back to an empty string if the header is missing or the theme
	 * object is unavailable (e.g. during CLI-driven unit tests).
	 *
	 * @return string  Theme version, e.g. "1.4.2", or "" if unavailable.
	 */
	private function get_theme_version(): string {
		return wp_get_theme()->get( 'Version' ) ?: '';
	}

	/**
	 * Returns an array of human-readable section labels for the given IDs.
	 *
	 * IDs that do not exist in the registry are silently skipped so the
	 * package meta remains clean even if the caller passes stale section IDs.
	 *
	 * @param string[] $section_ids  Section IDs to look up.
	 * @return string[]  Ordered list of label strings.
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
