<?php
/**
 * Anna Porter Admin
 *
 * Registers the "Content Porter" submenu page under the Anna theme settings
 * parent menu, enqueues page-specific assets, renders the export/import UI,
 * and dispatches all form submissions via admin-post.php hooks.
 *
 * @package Anna_Content_Porter
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles all admin-facing concerns for the Anna Content Porter plugin:
 * menu registration, asset enqueuing, page rendering, and form dispatch.
 */
class Anna_Porter_Admin {

	/**
	 * The page hook suffix returned by add_submenu_page().
	 * Used to scope asset enqueuing to this page only.
	 *
	 * @var string
	 */
	private string $page_hook = '';

	// ──────────────────────────────────────────────────────────────────────────
	// Bootstrap
	// ──────────────────────────────────────────────────────────────────────────

	/**
	 * Registers all WordPress hooks required by this class.
	 * Called once from the main plugin bootstrap file.
	 */
	public function init(): void {
		add_action( 'admin_menu',                            [ $this, 'register_menu'         ], 20 );
		add_action( 'admin_enqueue_scripts',                 [ $this, 'enqueue_assets'        ] );
		add_action( 'admin_post_anna_porter_export',         [ $this, 'handle_export'         ] );
		add_action( 'admin_post_anna_porter_import_preview', [ $this, 'handle_import_preview' ] );
		add_action( 'admin_post_anna_porter_import_confirm', [ $this, 'handle_import_confirm' ] );
	}

	// ──────────────────────────────────────────────────────────────────────────
	// Menu registration
	// ──────────────────────────────────────────────────────────────────────────

	/**
	 * Registers the "Content Porter" submenu under the Anna theme settings parent.
	 */
	public function register_menu(): void {
		$this->page_hook = add_submenu_page(
			'anna-theme-settings',
			__( 'Content Porter', 'anna-content-porter' ),
			__( 'Content Porter', 'anna-content-porter' ),
			'manage_options',
			'anna-porter',
			[ $this, 'render_page' ]
		);
	}

	// ──────────────────────────────────────────────────────────────────────────
	// Asset enqueuing
	// ──────────────────────────────────────────────────────────────────────────

	/**
	 * Enqueues the porter admin stylesheet and script, but only on the porter
	 * page itself (matched by $hook against the stored page hook suffix).
	 *
	 * @param string $hook Current admin page hook suffix.
	 */
	public function enqueue_assets( string $hook ): void {
		if ( $hook !== $this->page_hook ) {
			return;
		}

		$css_path = ANNA_PORTER_DIR . 'assets/css/admin.css';
		$js_path  = ANNA_PORTER_DIR . 'assets/js/admin.js';

		if ( file_exists( $css_path ) ) {
			wp_enqueue_style(
				'anna-porter-admin',
				ANNA_PORTER_URL . 'assets/css/admin.css',
				[],
				filemtime( $css_path )
			);
		}

		if ( file_exists( $js_path ) ) {
			wp_enqueue_script(
				'anna-porter-admin',
				ANNA_PORTER_URL . 'assets/js/admin.js',
				[],
				filemtime( $js_path ),
				true
			);
		}
	}

	// ──────────────────────────────────────────────────────────────────────────
	// Page rendering
	// ──────────────────────────────────────────────────────────────────────────

	/**
	 * Renders the full admin page.
	 */
	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Insufficient permissions.', 'anna-content-porter' ) );
		}
		?>
		<div class="wrap">

			<!-- ── Page header ───────────────────────────────────────────────── -->
			<div class="anna-porter-header">
				<div class="anna-porter-header-icon">
					<span class="dashicons dashicons-migrate"></span>
				</div>
				<div class="anna-porter-header-text">
					<h1><?php esc_html_e( 'Content Porter', 'anna-content-porter' ); ?></h1>
					<p><?php esc_html_e( 'Export and import Anna Baylis theme content between installations.', 'anna-content-porter' ); ?></p>
				</div>
			</div>

			<?php
			$this->render_notices();
			$this->render_preview_panel();
			$this->render_export_panel();
			$this->render_import_panel();
			?>

		</div>
		<?php
	}

	// ──────────────────────────────────────────────────────────────────────────
	// Render helpers
	// ──────────────────────────────────────────────────────────────────────────

	/**
	 * Renders error and success/warning notices from GET params.
	 */
	private function render_notices(): void {

		// ── Error notice ───────────────────────────────────────────────────────
		if ( isset( $_GET['porter_error'] ) ) {
			?>
			<div class="notice notice-error is-dismissible">
				<p><?php echo esc_html( sanitize_text_field( wp_unslash( $_GET['porter_error'] ) ) ); ?></p>
			</div>
			<?php
		}

		// ── Import result notice ───────────────────────────────────────────────
		if ( ! isset( $_GET['porter_done'] ) ) {
			return;
		}

		$written    = absint( $_GET['written']   ?? 0 );
		$skipped    = absint( $_GET['skipped']   ?? 0 );
		$images     = absint( $_GET['images']    ?? 0 );
		$warn_token = sanitize_key( $_GET['warn_token'] ?? '' );

		$import_warnings = [];
		if ( $warn_token ) {
			$stored = get_transient( "anna_porter_warn_{$warn_token}" );
			if ( is_array( $stored ) ) {
				$import_warnings = $stored;
				delete_transient( "anna_porter_warn_{$warn_token}" );
			}
		}

		$summary = sprintf(
			/* translators: 1: keys written 2: keys skipped 3: images created */
			__( 'Import complete — %1$d keys written, %2$d skipped, %3$d images created.', 'anna-content-porter' ),
			$written, $skipped, $images
		);

		if ( 0 === $written && 0 === $skipped ) {
			?>
			<div class="notice notice-error is-dismissible">
				<p>
					<?php echo esc_html( $summary ); ?>
					<?php esc_html_e( 'No content was imported — all keys were rejected. Check the warnings below.', 'anna-content-porter' ); ?>
				</p>
			</div>
			<?php
		} elseif ( empty( $import_warnings ) ) {
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php echo esc_html( $summary ); ?></p>
			</div>
			<?php
		} else {
			?>
			<div class="notice notice-warning is-dismissible">
				<p>
					<?php echo esc_html( $summary ); ?>
					<?php echo esc_html( sprintf( __( '(%d warning(s) — see below)', 'anna-content-porter' ), count( $import_warnings ) ) ); ?>
				</p>
			</div>
			<?php
		}

		if ( ! empty( $import_warnings ) ) {
			?>
			<div class="anna-porter-box anna-porter-warnings-box">
				<div class="anna-porter-box-header">
					<span class="dashicons dashicons-warning"></span>
					<h2><?php esc_html_e( 'Import Warnings', 'anna-content-porter' ); ?></h2>
				</div>
				<div class="anna-porter-box-body">
					<ul>
						<?php foreach ( $import_warnings as $w ) : ?>
							<li><?php echo esc_html( $w ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Renders the import preview panel when a valid porter_preview token is present.
	 */
	private function render_preview_panel(): void {
		if ( ! isset( $_GET['porter_preview'], $_GET['porter_token'] ) ) {
			return;
		}

		$token   = sanitize_key( $_GET['porter_token'] );
		$package = get_transient( "anna_porter_pkg_{$token}" );

		if ( false === $package ) {
			?>
			<div class="notice notice-error is-dismissible">
				<p><?php esc_html_e( 'Preview session expired. Please upload the file again.', 'anna-content-porter' ); ?></p>
			</div>
			<?php
			return;
		}

		$preview = null;
		try {
			$preview = ( new Anna_Porter_Importer() )->preview( $package );
		} catch ( InvalidArgumentException $e ) {
			?>
			<div class="notice notice-error is-dismissible">
				<p><?php echo esc_html( $e->getMessage() ); ?></p>
			</div>
			<?php
			return;
		}
		?>
		<div class="anna-porter-box anna-porter-preview-box">
			<div class="anna-porter-box-header">
				<span class="dashicons dashicons-visibility"></span>
				<h2><?php esc_html_e( 'Import Preview', 'anna-content-porter' ); ?></h2>
			</div>
			<div class="anna-porter-box-body">

				<table class="anna-porter-meta-table">
					<tbody>
						<tr>
							<th><?php esc_html_e( 'Source site', 'anna-content-porter' ); ?></th>
							<td><?php echo esc_html( $preview['source_site_url'] ); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Exported at', 'anna-content-porter' ); ?></th>
							<td><?php echo esc_html( $preview['exported_at'] ); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Sections', 'anna-content-porter' ); ?></th>
							<td><?php echo esc_html( implode( ', ', $preview['exported_sections'] ) ); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Content keys', 'anna-content-porter' ); ?></th>
							<td><?php echo esc_html( $preview['content_key_count'] ); ?></td>
						</tr>
					</tbody>
				</table>

				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php wp_nonce_field( 'anna_porter_import_confirm', 'anna_porter_nonce' ); ?>
					<input type="hidden" name="action"       value="anna_porter_import_confirm">
					<input type="hidden" name="porter_token" value="<?php echo esc_attr( $token ); ?>">

					<span class="anna-porter-mode-label"><?php esc_html_e( 'Import Mode', 'anna-content-porter' ); ?></span>
					<div class="anna-porter-mode-group">
						<label class="anna-porter-mode-option">
							<input type="radio" name="import_mode" value="overwrite" checked>
							<div class="anna-porter-mode-card">
								<strong><?php esc_html_e( 'Overwrite', 'anna-content-porter' ); ?></strong>
								<span><?php esc_html_e( 'Replace all existing values with the imported ones', 'anna-content-porter' ); ?></span>
							</div>
						</label>
						<label class="anna-porter-mode-option">
							<input type="radio" name="import_mode" value="skip">
							<div class="anna-porter-mode-card">
								<strong><?php esc_html_e( 'Skip Existing', 'anna-content-porter' ); ?></strong>
								<span><?php esc_html_e( 'Only fill in empty or missing fields, leave existing values alone', 'anna-content-porter' ); ?></span>
							</div>
						</label>
					</div>

					<div class="anna-porter-btn-row">
						<button type="submit" class="button button-primary">
							<span class="dashicons dashicons-yes-alt"></span>
							<?php esc_html_e( 'Confirm Import', 'anna-content-porter' ); ?>
						</button>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=anna-porter' ) ); ?>" class="button">
							<?php esc_html_e( 'Cancel', 'anna-content-porter' ); ?>
						</a>
					</div>
				</form>

			</div>
		</div>
		<?php
	}

	/**
	 * Renders the Export panel.
	 */
	private function render_export_panel(): void {
		$sections      = Anna_Porter_Registry::get_sections();
		$section_count = count( $sections );
		?>
		<div class="anna-porter-box">
			<div class="anna-porter-box-header">
				<span class="dashicons dashicons-download"></span>
				<h2><?php esc_html_e( 'Export Content', 'anna-content-porter' ); ?></h2>
				<span class="anna-porter-box-badge">
					<?php
					echo esc_html(
						sprintf(
							/* translators: %d number of sections */
							_n( '%d section available', '%d sections available', $section_count, 'anna-content-porter' ),
							$section_count
						)
					);
					?>
				</span>
			</div>
			<div class="anna-porter-box-body">
				<p class="anna-porter-desc">
					<?php esc_html_e( 'Select the page sections to include in the export. A portable JSON file will be downloaded with the latest saved content.', 'anna-content-porter' ); ?>
				</p>

				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="anna-porter-export-form">
					<?php wp_nonce_field( 'anna_porter_export', 'anna_porter_nonce' ); ?>
					<input type="hidden" name="action" value="anna_porter_export">

					<div class="anna-porter-grid-toolbar">
						<span class="anna-porter-grid-label"><?php esc_html_e( 'Page Sections', 'anna-content-porter' ); ?></span>
						<a href="#" id="anna-porter-select-all"
							data-label-select="<?php esc_attr_e( 'Select All', 'anna-content-porter' ); ?>"
							data-label-deselect="<?php esc_attr_e( 'Deselect All', 'anna-content-porter' ); ?>">
							<?php esc_html_e( 'Select All', 'anna-content-porter' ); ?>
						</a>
					</div>

					<div class="anna-porter-sections-grid">
						<?php foreach ( $sections as $id => $section ) : ?>
							<label class="anna-porter-section-label">
								<span class="porter-checkbox">
									<span class="dashicons dashicons-yes"></span>
								</span>
								<input
									type="checkbox"
									name="sections[]"
									value="<?php echo esc_attr( $id ); ?>"
									class="anna-porter-section-cb"
								>
								<?php echo esc_html( $section['label'] ); ?>
							</label>
						<?php endforeach; ?>
					</div>

					<div class="anna-porter-selection-bar" id="anna-porter-sel-bar">
						<span>
							<span id="anna-porter-selected-count" class="anna-porter-sel-count">0</span>
							<span class="anna-porter-sel-total">
								<?php
								echo esc_html(
									sprintf(
										/* translators: %d total number of sections */
										__( ' of %d sections selected', 'anna-content-porter' ),
										$section_count
									)
								);
								?>
							</span>
						</span>
						<span
							id="anna-porter-sel-hint"
							class="anna-porter-sel-hint"
							data-hint-pending="<?php esc_attr_e( 'Select at least one section to export', 'anna-content-porter' ); ?>"
							data-hint-ready="<?php esc_attr_e( 'Ready to export', 'anna-content-porter' ); ?>"
						>
							<?php esc_html_e( 'Select at least one section to export', 'anna-content-porter' ); ?>
						</span>
					</div>

					<div class="anna-porter-btn-row">
						<button type="submit" id="anna-porter-export-btn" class="button button-primary" disabled>
							<span class="porter-spinner"></span>
							<span class="dashicons dashicons-download porter-btn-icon"></span>
							<?php esc_html_e( 'Export Selected Sections', 'anna-content-porter' ); ?>
						</button>
					</div>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Renders the Import panel.
	 */
	private function render_import_panel(): void {
		?>
		<div class="anna-porter-box">
			<div class="anna-porter-box-header">
				<span class="dashicons dashicons-upload"></span>
				<h2><?php esc_html_e( 'Import Content', 'anna-content-porter' ); ?></h2>
			</div>
			<div class="anna-porter-box-body">
				<p class="anna-porter-desc">
					<?php esc_html_e( 'Upload a Content Porter JSON file to preview its contents before committing the import.', 'anna-content-porter' ); ?>
				</p>

				<form
					method="post"
					action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
					enctype="multipart/form-data"
					id="anna-porter-import-form"
				>
					<?php wp_nonce_field( 'anna_porter_import_preview', 'anna_porter_nonce' ); ?>
					<input type="hidden" name="action" value="anna_porter_import_preview">

					<div class="anna-porter-upload-area" id="anna-porter-upload-area">
						<input
							type="file"
							name="import_file"
							accept=".json"
							required
							id="anna-porter-file-input"
						>
						<div class="anna-porter-upload-default">
							<span class="dashicons dashicons-media-code anna-porter-upload-icon"></span>
							<div class="anna-porter-upload-title">
								<?php esc_html_e( 'Choose a file or drag it here', 'anna-content-porter' ); ?>
							</div>
							<div class="anna-porter-upload-hint">
								<?php esc_html_e( 'Only .json files exported by Anna Content Porter are accepted', 'anna-content-porter' ); ?>
							</div>
						</div>
						<div class="anna-porter-upload-filename" id="anna-porter-upload-filename"></div>
					</div>

					<div class="anna-porter-btn-row">
						<button type="submit" class="button button-primary">
							<span class="dashicons dashicons-visibility"></span>
							<?php esc_html_e( 'Upload & Preview', 'anna-content-porter' ); ?>
						</button>
					</div>
				</form>
			</div>
		</div>
		<?php
	}

	// ──────────────────────────────────────────────────────────────────────────
	// Form handlers
	// ──────────────────────────────────────────────────────────────────────────

	/**
	 * Handles the export POST action.
	 *
	 * Validates nonce and capabilities, then delegates to Anna_Porter_Exporter
	 * which sends download headers and exits. This method never returns on success.
	 */
	public function handle_export(): void {
		check_admin_referer( 'anna_porter_export', 'anna_porter_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Insufficient permissions.', 'anna-content-porter' ) );
		}

		$section_ids = array_filter( array_map( 'sanitize_key', (array) ( $_POST['sections'] ?? [] ) ) );

		if ( empty( $section_ids ) ) {
			wp_die( __( 'No sections selected.', 'anna-content-porter' ) );
		}

		// Increase memory limit to accommodate large image bundles.
		@ini_set( 'memory_limit', '256M' ); // phpcs:ignore WordPress.PHP.IniSet.Risky

		( new Anna_Porter_Exporter() )->export( $section_ids );
	}

	/**
	 * Handles the import preview POST action.
	 *
	 * Validates nonce, capabilities, and the uploaded file; decodes the JSON
	 * package; stores it in a short-lived transient; then redirects the browser
	 * back to the porter admin page in preview mode.
	 */
	public function handle_import_preview(): void {
		check_admin_referer( 'anna_porter_import_preview', 'anna_porter_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Insufficient permissions.', 'anna-content-porter' ) );
		}

		// ── File upload validation ─────────────────────────────────────────────
		$file = $_FILES['import_file'] ?? null;

		if ( ! $file || $file['error'] !== UPLOAD_ERR_OK ) {
			$msg = $file ? $this->upload_error_message( (int) $file['error'] ) : 'No file uploaded.';
			wp_redirect( add_query_arg( [ 'page' => 'anna-porter', 'porter_error' => rawurlencode( $msg ) ], admin_url( 'admin.php' ) ) );
			exit;
		}

		// ── MIME validation ────────────────────────────────────────────────────
		$mime = $file['type'] ?? '';
		if ( ! in_array( $mime, [ 'application/json', 'text/plain', 'application/octet-stream' ], true ) ) {
			$err = rawurlencode( 'Invalid file type. Please upload a .json file.' );
			wp_redirect( add_query_arg( [ 'page' => 'anna-porter', 'porter_error' => $err ], admin_url( 'admin.php' ) ) );
			exit;
		}

		// ── Read & decode ──────────────────────────────────────────────────────
		$raw     = file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		$package = json_decode( $raw, true );

		if ( null === $package || JSON_ERROR_NONE !== json_last_error() ) {
			$err = rawurlencode( 'Invalid JSON file: ' . json_last_error_msg() );
			wp_redirect( add_query_arg( [ 'page' => 'anna-porter', 'porter_error' => $err ], admin_url( 'admin.php' ) ) );
			exit;
		}

		// ── Plugin identity validation ─────────────────────────────────────────
		if ( ( $package['meta']['plugin'] ?? '' ) !== 'anna-content-porter' ) {
			$err = rawurlencode( 'This file was not produced by the Anna Content Porter plugin.' );
			wp_redirect( add_query_arg( [ 'page' => 'anna-porter', 'porter_error' => $err ], admin_url( 'admin.php' ) ) );
			exit;
		}

		// ── Store package in transient & redirect ──────────────────────────────
		$token = substr( md5( uniqid( 'anna_porter_', true ) ), 0, 16 );
		set_transient( "anna_porter_pkg_{$token}", $package, 30 * MINUTE_IN_SECONDS );

		wp_redirect( add_query_arg( [
			'page'           => 'anna-porter',
			'porter_preview' => '1',
			'porter_token'   => $token,
		], admin_url( 'admin.php' ) ) );
		exit;
	}

	/**
	 * Handles the import confirm POST action.
	 *
	 * Validates nonce and capabilities, retrieves the stored package from its
	 * transient (consuming it), runs the importer, then redirects with a result
	 * summary in the query string.
	 */
	public function handle_import_confirm(): void {
		check_admin_referer( 'anna_porter_import_confirm', 'anna_porter_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Insufficient permissions.', 'anna-content-porter' ) );
		}

		$token   = sanitize_key( $_POST['porter_token'] ?? '' );
		$package = get_transient( "anna_porter_pkg_{$token}" );

		if ( false === $package ) {
			$err = rawurlencode( 'Session expired. Please upload the file again.' );
			wp_redirect( add_query_arg( [ 'page' => 'anna-porter', 'porter_error' => $err ], admin_url( 'admin.php' ) ) );
			exit;
		}

		// Consume the transient immediately so it cannot be replayed.
		delete_transient( "anna_porter_pkg_{$token}" );

		$mode   = ( 'skip' === ( $_POST['import_mode'] ?? '' ) ) ? 'skip' : 'overwrite';
		$result = ( new Anna_Porter_Importer() )->import( $package, $mode );

		// Store warning messages in a short-lived transient so the result page
		// can display them without bloating the redirect URL.
		$warn_token = '';
		if ( ! empty( $result['warnings'] ) ) {
			$warn_token = substr( md5( uniqid( 'apw_', true ) ), 0, 16 );
			set_transient( "anna_porter_warn_{$warn_token}", $result['warnings'], 5 * MINUTE_IN_SECONDS );
		}

		wp_redirect( add_query_arg( [
			'page'        => 'anna-porter',
			'porter_done' => '1',
			'written'     => $result['written'],
			'skipped'     => $result['skipped'],
			'images'      => $result['images_created'],
			'warn_token'  => $warn_token,
		], admin_url( 'admin.php' ) ) );
		exit;
	}

	// ──────────────────────────────────────────────────────────────────────────
	// Private helpers
	// ──────────────────────────────────────────────────────────────────────────

	/**
	 * Returns a human-readable message for a PHP file-upload error code.
	 *
	 * @param int $code One of the UPLOAD_ERR_* constants.
	 * @return string
	 */
	private function upload_error_message( int $code ): string {
		switch ( $code ) {
			case UPLOAD_ERR_INI_SIZE:
				return 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
			case UPLOAD_ERR_FORM_SIZE:
				return 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.';
			case UPLOAD_ERR_PARTIAL:
				return 'The file was only partially uploaded. Please try again.';
			case UPLOAD_ERR_NO_FILE:
				return 'No file was uploaded.';
			case UPLOAD_ERR_NO_TMP_DIR:
				return 'The server is missing a temporary folder for file uploads.';
			case UPLOAD_ERR_CANT_WRITE:
				return 'Failed to write the uploaded file to disk.';
			case UPLOAD_ERR_EXTENSION:
				return 'A PHP extension stopped the file upload.';
			default:
				return 'Unknown upload error.';
		}
	}
}
