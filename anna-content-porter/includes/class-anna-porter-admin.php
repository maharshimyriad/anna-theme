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
		$is_porter_page = (
			$hook === $this->page_hook
			|| ( isset( $_GET['page'] ) && 'anna-porter' === sanitize_key( wp_unslash( $_GET['page'] ) ) )
		);

		if ( ! $is_porter_page ) {
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
		<div class="wrap anna-porter-page">
			<?php $this->render_inline_styles(); ?>

			<div class="anna-porter-header">
				<h1><?php esc_html_e( 'Content Porter', 'anna-content-porter' ); ?></h1>
				<p><?php esc_html_e( 'Export and import Anna Baylis theme content between installations.', 'anna-content-porter' ); ?></p>
			</div>

			<?php
			$this->render_notices();
				$this->render_preview_panel();
				$this->render_export_panel();
				$this->render_import_panel();

				if ( isset( $_GET['porter_debug'] ) && current_user_can( 'manage_options' ) ) {
					$this->render_debug_panel();
				}
			?>

		</div>
		<?php
	}

	// ──────────────────────────────────────────────────────────────────────────
	// Render helpers
	// ──────────────────────────────────────────────────────────────────────────

	/**
	 * Prints critical fallback styles inline so the admin UI remains clean even
	 * when the external CSS file is cached, blocked, or not enqueued by the host.
	 */
	private function render_inline_styles(): void {
		?>
		<style>
			.anna-porter-page{max-width:1040px;margin-top:24px;color:#1d2327}.anna-porter-page *{box-sizing:border-box}.anna-porter-page .dashicons,.anna-porter-page .porter-checkbox,.anna-porter-page .porter-spinner{display:none!important}.anna-porter-header{margin:0 0 22px;padding-bottom:18px;border-bottom:1px solid #dcdcde}.anna-porter-header h1{margin:0 0 6px;font-size:24px;line-height:1.25;font-weight:600}.anna-porter-header p{max-width:720px;margin:0;color:#646970;font-size:14px;line-height:1.55}.anna-porter-box{max-width:940px;margin:0 0 20px;background:#fff;border:1px solid #dcdcde;border-radius:8px;box-shadow:0 1px 2px rgba(0,0,0,.04);overflow:hidden}.anna-porter-box-header{display:flex;align-items:center;gap:12px;padding:16px 20px;background:#fff;border-bottom:1px solid #edf0f2}.anna-porter-box-header h2{margin:0;font-size:16px;line-height:1.35;font-weight:600}.anna-porter-box-badge{margin-left:auto;padding:4px 10px;border:1px solid #dcdcde;border-radius:999px;background:#f6f7f7;color:#646970;font-size:12px}.anna-porter-box-body{padding:20px}.anna-porter-desc{margin:0 0 16px;color:#50575e;font-size:14px;line-height:1.6}.anna-porter-section-help{margin:0 0 12px;padding:10px 12px;border-radius:6px;background:#f6f7f7;color:#50575e;font-size:13px}.anna-porter-sections-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(185px,1fr));gap:10px;margin:0 0 16px}.anna-porter-section-label{position:relative;display:flex;align-items:center;min-height:42px;padding:10px 12px 10px 38px;border:1px solid #dcdcde;border-radius:6px;background:#fff;color:#1d2327;cursor:pointer;font-size:13px;line-height:1.3}.anna-porter-section-label:hover{border-color:#8c8f94;background:#f6f7f7}.anna-porter-section-label:has(input:checked),.anna-porter-section-label.is-checked{border-color:#2271b1;background:#f0f6fc;box-shadow:inset 0 0 0 1px #2271b1}.anna-porter-section-cb{position:absolute;left:12px;top:50%;width:16px;height:16px;margin:-8px 0 0}.anna-porter-btn-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap}.anna-porter-btn-row .button,#anna-porter-export-btn{min-height:36px;padding:3px 16px;font-weight:600}.anna-porter-upload-area{position:relative;display:block;width:100%;min-height:120px;margin:0 0 16px;padding:28px 20px;border:1.5px dashed #c3c4c7;border-radius:8px;background:#fbfbfc;text-align:center;cursor:pointer}.anna-porter-upload-area:hover,.anna-porter-upload-area.is-drag-over{border-color:#2271b1;background:#f0f6fc}.anna-porter-upload-area.has-file{border-style:solid;border-color:#00a32a;background:#f0fdf4}.anna-porter-upload-area input[type=file]{position:absolute;inset:0;width:100%;height:100%;opacity:0;cursor:pointer}.anna-porter-upload-title{margin:0 0 4px;font-size:14px;font-weight:600}.anna-porter-upload-hint{color:#646970;font-size:13px}.anna-porter-upload-filename{display:none;color:#008a20;font-size:14px;font-weight:600}.anna-porter-upload-area.has-file .anna-porter-upload-default{display:none}.anna-porter-upload-area.has-file .anna-porter-upload-filename{display:block}.anna-porter-meta-table,.anna-porter-debug-table{width:100%;border-collapse:collapse;margin:0 0 16px;border:1px solid #dcdcde;border-radius:6px;overflow:hidden;font-size:13px}.anna-porter-meta-table th,.anna-porter-meta-table td,.anna-porter-debug-table th,.anna-porter-debug-table td{padding:10px 12px;border-bottom:1px solid #edf0f2;text-align:left;vertical-align:top}.anna-porter-meta-table th,.anna-porter-debug-table th{width:180px;background:#f6f7f7;color:#50575e;font-weight:600}.anna-porter-mode-label{display:block;margin:0 0 8px;color:#50575e;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.03em}.anna-porter-mode-group{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:10px;margin:0 0 16px}.anna-porter-mode-option{cursor:pointer}.anna-porter-mode-option input[type=radio]{position:absolute;opacity:0}.anna-porter-mode-card{padding:13px 14px;border:1px solid #dcdcde;border-radius:6px;background:#fff}.anna-porter-mode-option input:checked+.anna-porter-mode-card{border-color:#2271b1;background:#f0f6fc;box-shadow:inset 0 0 0 1px #2271b1}.anna-porter-mode-card strong{display:block;margin-bottom:3px}.anna-porter-mode-card span{color:#646970;font-size:13px;line-height:1.45}@media(max-width:782px){.anna-porter-box-header{align-items:flex-start;flex-direction:column}.anna-porter-box-badge{margin-left:0}.anna-porter-sections-grid{grid-template-columns:1fr}}
		</style>
		<?php
	}

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
		$package = $this->load_import_package( $token );

		if ( null === $package ) {
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
							<td>
								<?php echo esc_html( $preview['content_key_count'] ); ?>
								<?php if ( ! empty( $preview['page_count'] ) ) : ?>
									&mdash;
									<?php echo esc_html( $preview['page_meta_count'] ); ?>
									<?php esc_html_e( 'live page fields across', 'anna-content-porter' ); ?>
									<?php echo esc_html( $preview['page_count'] ); ?>
									<?php esc_html_e( 'page(s)', 'anna-content-porter' ); ?>
									<?php if ( $preview['option_key_count'] > 0 ) : ?>
										+ <?php echo esc_html( $preview['option_key_count'] ); ?>
										<?php esc_html_e( 'global options', 'anna-content-porter' ); ?>
									<?php endif; ?>
								<?php endif; ?>
							</td>
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

					<p class="anna-porter-section-help">
						<?php esc_html_e( 'Choose “All Pages” for a full site export, or select specific sections below.', 'anna-content-porter' ); ?>
					</p>

					<div class="anna-porter-sections-grid">
						<?php foreach ( $sections as $id => $section ) : ?>
							<label class="anna-porter-section-label">
								<input
									type="checkbox"
									name="sections[]"
									value="<?php echo esc_attr( $id ); ?>"
									class="anna-porter-section-cb"
									<?php checked( 'all_pages', $id ); ?>
								>
								<span><?php echo esc_html( $section['label'] ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>

					<div class="anna-porter-btn-row">
						<button type="submit" id="anna-porter-export-btn" class="button button-primary">
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
							<?php esc_html_e( 'Upload & Preview', 'anna-content-porter' ); ?>
						</button>
					</div>
				</form>
			</div>
		</div>
		<?php
	}

	// -------------------------------------------------------------------------
	// Debug panel
	// -------------------------------------------------------------------------

	/**
	 * Debug panel — reads straight from the DB (no cache) to reveal exactly
	 * what is stored, which options contain "anna", and what each registry
	 * section would export. Remove this panel once the data source is confirmed.
	 */
	private function render_debug_panel(): void {
		global $wpdb;

		// ── 1. Direct DB read of anna_theme_options ────────────────────────────
		$raw_row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT option_value FROM {$wpdb->options} WHERE option_name = %s LIMIT 1",
				'anna_theme_options'
			)
		);

		$db_value    = $raw_row ? maybe_unserialize( $raw_row->option_value ) : null;
		$option_exists = ( null !== $raw_row );
		$is_array      = is_array( $db_value );
		$key_count     = $is_array ? count( $db_value ) : 0;

		// ── 2. All options whose name contains "anna" ──────────────────────────
		$anna_options = $wpdb->get_results(
			"SELECT option_name, LENGTH(option_value) AS val_len
			 FROM {$wpdb->options}
			 WHERE option_name LIKE '%anna%'
			 ORDER BY option_name"
		);

		// ── 3. Registry match preview (uses the direct DB value) ──────────────
		$sections        = Anna_Porter_Registry::get_sections();
		$options_for_reg = $is_array ? $db_value : [];
		$section_matches = [];
		foreach ( $sections as $sid => $sdata ) {
			$keys = Anna_Porter_Registry::get_keys_for_sections( [ $sid ], $options_for_reg );
			$section_matches[ $sid ] = [
				'label' => $sdata['label'],
				'keys'  => $keys,
			];
		}
		?>
		<details class="anna-porter-box anna-porter-debug-box" id="anna-porter-debug">
			<summary class="anna-porter-box-header">
				<h2><?php esc_html_e( 'Debug — Data Source Inspector', 'anna-content-porter' ); ?></h2>
				<span class="anna-porter-box-badge" style="margin-left:auto">
					<?php esc_html_e( 'Click to expand', 'anna-content-porter' ); ?>
				</span>
			</summary>
			<div class="anna-porter-box-body anna-porter-debug-body">

				<!-- ── Section A: anna_theme_options status ──────────────────── -->
				<h3 class="anna-porter-debug-heading">
					<?php esc_html_e( 'A — anna_theme_options (direct DB read, no cache)', 'anna-content-porter' ); ?>
				</h3>
				<?php if ( ! $option_exists ) : ?>
					<div class="anna-porter-debug-alert is-error">
						&#x274C; <?php esc_html_e( 'Row does NOT exist in wp_options. The theme is not saving to this option name.', 'anna-content-porter' ); ?>
					</div>
				<?php elseif ( ! $is_array || 0 === $key_count ) : ?>
					<div class="anna-porter-debug-alert is-warning">
						&#x26A0; <?php esc_html_e( 'Row exists but is empty or not an array. No exportable keys found.', 'anna-content-porter' ); ?>
					</div>
					<pre class="anna-porter-debug-pre"><?php echo esc_html( var_export( $db_value, true ) ); ?></pre>
				<?php else : ?>
					<div class="anna-porter-debug-alert is-ok">
						&#x2705;
						<?php
						echo esc_html(
							sprintf(
								/* translators: %d number of keys */
								__( 'Found %d top-level keys in anna_theme_options.', 'anna-content-porter' ),
								$key_count
							)
						);
						?>
					</div>
					<table class="anna-porter-debug-table">
						<thead><tr>
							<th><?php esc_html_e( 'Key', 'anna-content-porter' ); ?></th>
							<th><?php esc_html_e( 'Type', 'anna-content-porter' ); ?></th>
							<th><?php esc_html_e( 'Value (truncated)', 'anna-content-porter' ); ?></th>
						</tr></thead>
						<tbody>
							<?php foreach ( $db_value as $k => $v ) : ?>
								<tr>
									<td class="anna-porter-debug-key"><?php echo esc_html( $k ); ?></td>
									<td><?php echo esc_html( gettype( $v ) ); ?></td>
									<td class="anna-porter-debug-val"><?php
										if ( is_array( $v ) ) {
											echo esc_html( '[ ' . count( $v ) . ' items ]' );
										} else {
											$display = (string) $v;
											echo esc_html( strlen( $display ) > 120 ? substr( $display, 0, 120 ) . '…' : $display );
										}
									?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>

				<!-- Section B: all options whose name contains 'anna' -->
				<h3 class="anna-porter-debug-heading">
					<?php esc_html_e( 'B - All wp_options rows whose name contains anna', 'anna-content-porter' ); ?>
				</h3>
				<?php if ( empty( $anna_options ) ) : ?>
					<div class="anna-porter-debug-alert is-error">
						<?php esc_html_e( 'No options found. The theme may use a completely different prefix.', 'anna-content-porter' ); ?>
					</div>
				<?php else : ?>
					<table class="anna-porter-debug-table">
						<thead><tr>
							<th><?php esc_html_e( 'option_name', 'anna-content-porter' ); ?></th>
							<th><?php esc_html_e( 'Stored bytes', 'anna-content-porter' ); ?></th>
							<th><?php esc_html_e( 'Note', 'anna-content-porter' ); ?></th>
						</tr></thead>
						<tbody>
							<?php foreach ( $anna_options as $opt ) : ?>
								<tr>
									<td class="anna-porter-debug-key"><?php echo esc_html( $opt->option_name ); ?></td>
									<td><?php echo esc_html( number_format( (int) $opt->val_len ) ); ?></td>
									<td>
										<?php if ( $opt->option_name === 'anna_theme_options' ) : ?>
											<strong style="color:#2271b1"><?php esc_html_e( 'THIS is what the porter reads', 'anna-content-porter' ); ?></strong>
										<?php else : ?>
											<span style="color:#d63638"><?php esc_html_e( 'NOT read by porter', 'anna-content-porter' ); ?></span>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>

				<!-- Section C: registry match preview per section -->
				<h3 class="anna-porter-debug-heading">
					<?php esc_html_e( 'C - Registry match preview (keys the porter would export per section)', 'anna-content-porter' ); ?>
				</h3>
				<table class="anna-porter-debug-table">
					<thead><tr>
						<th><?php esc_html_e( 'Section', 'anna-content-porter' ); ?></th>
						<th><?php esc_html_e( 'Keys matched', 'anna-content-porter' ); ?></th>
						<th><?php esc_html_e( 'Key names', 'anna-content-porter' ); ?></th>
					</tr></thead>
					<tbody>
						<?php foreach ( $section_matches as $sid => $info ) : ?>
							<tr>
								<td class="anna-porter-debug-key"><?php echo esc_html( $info['label'] . ' (' . $sid . ')' ); ?></td>
								<td>
									<?php if ( empty( $info['keys'] ) ) : ?>
										<span style="color:#d63638">0</span>
									<?php else : ?>
										<strong style="color:#00a32a"><?php echo esc_html( count( $info['keys'] ) ); ?></strong>
									<?php endif; ?>
								</td>
								<td class="anna-porter-debug-val"><?php echo esc_html( implode( ', ', $info['keys'] ) ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<!-- Section D: post meta entries whose keys match anna_theme_options keys -->
				<h3 class="anna-porter-debug-heading">
					<?php esc_html_e( 'D - Post meta rows sharing the same keys as anna_theme_options (the live source)', 'anna-content-porter' ); ?>
				</h3>
				<?php
				// Build the list of known keys from the DB value we already have.
				$known_keys = $is_array ? array_keys( $db_value ) : [];

				if ( empty( $known_keys ) ) :
				?>
					<div class="anna-porter-debug-alert is-warning">
						<?php esc_html_e( 'No keys in anna_theme_options to compare against.', 'anna-content-porter' ); ?>
					</div>
				<?php else :
					// Build a safe IN() placeholder list.
					$placeholders = implode( ', ', array_fill( 0, count( $known_keys ), '%s' ) );

					// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
					$postmeta_rows = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT pm.post_id, p.post_title, p.post_type, p.post_status,
							        pm.meta_key,
							        LEFT(pm.meta_value, 120) AS meta_val_preview
							 FROM {$wpdb->postmeta} pm
							 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
							 WHERE pm.meta_key IN ($placeholders)
							   AND p.post_status NOT IN ('trash','auto-draft')
							 ORDER BY p.post_title, pm.meta_key
							 LIMIT 300",
							...$known_keys
						)
					);
					// phpcs:enable

					if ( empty( $postmeta_rows ) ) :
				?>
					<div class="anna-porter-debug-alert is-ok">
						<?php esc_html_e( 'No post meta rows found for these keys. The theme reads only from anna_theme_options — option name is correct, data may simply be stale there.', 'anna-content-porter' ); ?>
					</div>
				<?php else : ?>
					<div class="anna-porter-debug-alert is-error">
						<?php
						printf(
							/* translators: %d count */
							esc_html__( 'Found %d post meta row(s) using the same keys. The LIVE data lives here, NOT in anna_theme_options. The porter must read post meta instead.', 'anna-content-porter' ),
							count( $postmeta_rows )
						);
						?>
					</div>
					<table class="anna-porter-debug-table">
						<thead><tr>
							<th><?php esc_html_e( 'post_id', 'anna-content-porter' ); ?></th>
							<th><?php esc_html_e( 'Title', 'anna-content-porter' ); ?></th>
							<th><?php esc_html_e( 'Type / Status', 'anna-content-porter' ); ?></th>
							<th><?php esc_html_e( 'meta_key', 'anna-content-porter' ); ?></th>
							<th><?php esc_html_e( 'meta_value (preview)', 'anna-content-porter' ); ?></th>
						</tr></thead>
						<tbody>
							<?php foreach ( $postmeta_rows as $row ) : ?>
								<?php
								// Highlight rows whose meta_value differs from the option value.
								$opt_val    = (string) ( $db_value[ $row->meta_key ] ?? '' );
								$meta_val   = (string) $row->meta_val_preview;
								$is_diff    = ( trim( $opt_val ) !== trim( $meta_val ) );
								?>
								<tr style="<?php echo $is_diff ? 'background:#fff8f0;' : ''; ?>">
									<td><?php echo esc_html( $row->post_id ); ?></td>
									<td><?php echo esc_html( $row->post_title ); ?></td>
									<td><?php echo esc_html( $row->post_type . ' / ' . $row->post_status ); ?></td>
									<td class="anna-porter-debug-key"><?php echo esc_html( $row->meta_key ); ?></td>
									<td class="anna-porter-debug-val">
										<?php echo esc_html( $meta_val ); ?>
										<?php if ( $is_diff ) : ?>
											<br><em style="color:#d63638;font-size:11px">
												<?php esc_html_e( 'DIFFERS from anna_theme_options', 'anna-content-porter' ); ?>
											</em>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
				<?php endif; ?>

				<!-- Section E: broad LIKE search across postmeta using every registry prefix -->
				<h3 class="anna-porter-debug-heading">
					<?php esc_html_e( 'E - Broad post meta search using all registry prefixes (catches any key variant)', 'anna-content-porter' ); ?>
				</h3>
				<?php
				$all_prefixes = [];
				foreach ( Anna_Porter_Registry::get_sections() as $sec ) {
					foreach ( $sec['prefixes'] as $pfx ) {
						$all_prefixes[] = $pfx;
					}
				}
				$all_prefixes = array_values( array_unique( $all_prefixes ) );

				if ( ! empty( $all_prefixes ) ) :
					$like_where = implode( ' OR ', array_fill( 0, count( $all_prefixes ), 'pm.meta_key LIKE %s' ) );
					$like_vals  = array_map( function( $p ) { return $p . '%'; }, $all_prefixes );

					// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
					$broad_meta = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT pm.post_id, p.post_title, p.post_type, p.post_status,
							        pm.meta_key, LEFT(pm.meta_value, 120) AS val_preview
							 FROM {$wpdb->postmeta} pm
							 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
							 WHERE ($like_where)
							   AND p.post_status NOT IN ('trash','auto-draft')
							 ORDER BY p.post_title, pm.meta_key
							 LIMIT 300",
							...$like_vals
						)
					);
					// phpcs:enable

					if ( empty( $broad_meta ) ) :
				?>
						<div class="anna-porter-debug-alert is-ok">
							<?php esc_html_e( 'No post meta found for any registry prefix. Data is NOT in post meta at all.', 'anna-content-porter' ); ?>
						</div>
				<?php else : ?>
						<div class="anna-porter-debug-alert is-warning">
							<?php
							printf(
								esc_html__( 'Found %d post meta row(s) matching registry prefixes (possibly private _keys or variants).', 'anna-content-porter' ),
								count( $broad_meta )
							);
							?>
						</div>
						<table class="anna-porter-debug-table">
							<thead><tr>
								<th><?php esc_html_e( 'post_id', 'anna-content-porter' ); ?></th>
								<th><?php esc_html_e( 'Title', 'anna-content-porter' ); ?></th>
								<th><?php esc_html_e( 'Type', 'anna-content-porter' ); ?></th>
								<th><?php esc_html_e( 'meta_key', 'anna-content-porter' ); ?></th>
								<th><?php esc_html_e( 'meta_value', 'anna-content-porter' ); ?></th>
							</tr></thead>
							<tbody>
								<?php foreach ( $broad_meta as $brow ) : ?>
									<tr>
										<td><?php echo esc_html( $brow->post_id ); ?></td>
										<td><?php echo esc_html( $brow->post_title ); ?></td>
										<td><?php echo esc_html( $brow->post_type . ' / ' . $brow->post_status ); ?></td>
										<td class="anna-porter-debug-key"><?php echo esc_html( $brow->meta_key ); ?></td>
										<td class="anna-porter-debug-val"><?php echo esc_html( $brow->val_preview ); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
				<?php endif; ?>
				<?php endif; ?>

				<!-- Section F: staleness check — when was anna_theme_options last touched -->
				<h3 class="anna-porter-debug-heading">
					<?php esc_html_e( 'F - Staleness: compare anna_theme_options to recently-edited pages', 'anna-content-porter' ); ?>
				</h3>
				<?php
				// Snapshot the serialized byte length of the option as a rough change indicator.
				$opt_byte_len = $raw_row ? strlen( $raw_row->option_value ) : 0;

				// Grab pages modified in the last 30 days.
				$recent_pages = $wpdb->get_results(
					"SELECT ID, post_title, post_modified, post_status
					 FROM {$wpdb->posts}
					 WHERE post_type IN ('page','post')
					   AND post_status NOT IN ('trash','auto-draft')
					 ORDER BY post_modified DESC
					 LIMIT 10"
				);
				?>
				<table class="anna-porter-debug-table">
					<thead><tr>
						<th><?php esc_html_e( 'Item', 'anna-content-porter' ); ?></th>
						<th><?php esc_html_e( 'Detail', 'anna-content-porter' ); ?></th>
					</tr></thead>
					<tbody>
						<tr>
							<td class="anna-porter-debug-key"><?php esc_html_e( 'anna_theme_options serialised size', 'anna-content-porter' ); ?></td>
							<td><?php echo esc_html( number_format( $opt_byte_len ) . ' bytes' ); ?></td>
						</tr>
						<tr>
							<td class="anna-porter-debug-key"><?php esc_html_e( 'Current server time (UTC)', 'anna-content-porter' ); ?></td>
							<td><?php echo esc_html( gmdate( 'Y-m-d H:i:s' ) ); ?></td>
						</tr>
					</tbody>
				</table>
				<?php if ( ! empty( $recent_pages ) ) : ?>
					<p style="font-size:12px;color:var(--porter-muted);margin:6px 0 10px">
						<?php esc_html_e( '10 most recently edited pages/posts (if any of these are newer than when you last saved your hero/section fields, the option is stale):', 'anna-content-porter' ); ?>
					</p>
					<table class="anna-porter-debug-table">
						<thead><tr>
							<th><?php esc_html_e( 'ID', 'anna-content-porter' ); ?></th>
							<th><?php esc_html_e( 'Title', 'anna-content-porter' ); ?></th>
							<th><?php esc_html_e( 'Status', 'anna-content-porter' ); ?></th>
							<th><?php esc_html_e( 'Last modified', 'anna-content-porter' ); ?></th>
						</tr></thead>
						<tbody>
							<?php foreach ( $recent_pages as $pg ) : ?>
								<tr>
									<td><?php echo esc_html( $pg->ID ); ?></td>
									<td><?php echo esc_html( $pg->post_title ); ?></td>
									<td><?php echo esc_html( $pg->post_status ); ?></td>
									<td><?php echo esc_html( $pg->post_modified ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>

				<!-- Section G: action hint -->
				<h3 class="anna-porter-debug-heading">
					<?php esc_html_e( 'G - What to do next', 'anna-content-porter' ); ?>
				</h3>
				<div class="anna-porter-debug-alert is-warning">
					<strong><?php esc_html_e( 'Diagnosis: the porter is reading the right option, but anna_theme_options is stale.', 'anna-content-porter' ); ?></strong><br><br>
					<?php esc_html_e( 'The hero/section edit panel on your page editor saves to anna_theme_options via its own save action (usually a separate Save button or an AJAX call). Clicking the WP Update button on the page does NOT automatically flush those fields into anna_theme_options.', 'anna-content-porter' ); ?><br><br>
					<?php esc_html_e( 'Steps to confirm:', 'anna-content-porter' ); ?>
					<ol style="margin:8px 0 0 18px;font-size:12px">
						<li><?php esc_html_e( 'Open the page with the hero panel. Edit any field. Look for a dedicated Save/Update button INSIDE that panel (not the top WP Update button) and click it. Then reload this debug panel and check if anna_theme_options updated.', 'anna-content-porter' ); ?></li>
						<li><?php esc_html_e( 'If no separate Save button exists, open your browser DevTools (F12 > Network) while clicking WP Update. Check whether an AJAX POST to admin-ajax.php or admin-post.php is made with action containing anna or theme_options. If that call returns an error, the save is silently failing.', 'anna-content-porter' ); ?></li>
						<li><?php esc_html_e( 'If you do find a save_post hook that updates anna_theme_options, check whether a nonce field named after the meta box is present in the form and passing verification.', 'anna-content-porter' ); ?></li>
					</ol>
				</div>

			</div>
		</details>
		<?php
	}

	// -------------------------------------------------------------------------
	// Form handlers
	// -------------------------------------------------------------------------

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

		// ── Store package in a temporary uploads file and redirect ─────────────
		// Large exports with embedded base64 images can exceed transient / object
		// cache limits, causing "session expired" between preview and confirm.
		$token  = substr( md5( uniqid( 'anna_porter_', true ) ), 0, 16 );
		$stored = $this->store_import_package( $token, $package );

		if ( ! $stored ) {
			$err = rawurlencode( 'Could not store the uploaded import package. Please check uploads folder permissions.' );
			wp_redirect( add_query_arg( [ 'page' => 'anna-porter', 'porter_error' => $err ], admin_url( 'admin.php' ) ) );
			exit;
		}

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
		$package = $this->load_import_package( $token );

		if ( null === $package ) {
			$err = rawurlencode( 'Session expired. Please upload the file again.' );
			wp_redirect( add_query_arg( [ 'page' => 'anna-porter', 'porter_error' => $err ], admin_url( 'admin.php' ) ) );
			exit;
		}

		// Consume the stored package immediately so it cannot be replayed.
		$this->delete_import_package( $token );

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
	 * Returns the temporary import package directory, creating it if necessary.
	 *
	 * @return string|null
	 */
	private function import_package_dir(): ?string {
		$upload = wp_upload_dir();

		if ( ! empty( $upload['error'] ) || empty( $upload['basedir'] ) ) {
			return null;
		}

		$dir = trailingslashit( $upload['basedir'] ) . 'anna-content-porter';

		if ( ! wp_mkdir_p( $dir ) ) {
			return null;
		}

		// Prevent casual directory browsing on hosts that serve indexes.
		$index = trailingslashit( $dir ) . 'index.html';
		if ( ! file_exists( $index ) ) {
			file_put_contents( $index, '' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		}

		return $dir;
	}

	/**
	 * Returns a token-scoped package file path.
	 *
	 * @param string $token Import token.
	 * @return string|null
	 */
	private function import_package_path( string $token ): ?string {
		$token = sanitize_key( $token );

		if ( '' === $token ) {
			return null;
		}

		$dir = $this->import_package_dir();
		if ( null === $dir ) {
			return null;
		}

		return trailingslashit( $dir ) . 'package-' . $token . '.json';
	}

	/**
	 * Stores an import package on disk to avoid transient/object-cache size limits.
	 *
	 * @param string $token   Import token.
	 * @param array  $package Decoded JSON package.
	 * @return bool
	 */
	private function store_import_package( string $token, array $package ): bool {
		$path = $this->import_package_path( $token );

		if ( null === $path ) {
			return false;
		}

		$json = wp_json_encode( $package );
		if ( false === $json ) {
			return false;
		}

		return false !== file_put_contents( $path, $json, LOCK_EX ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	}

	/**
	 * Loads a stored import package from disk.
	 *
	 * @param string $token Import token.
	 * @return array|null
	 */
	private function load_import_package( string $token ): ?array {
		$path = $this->import_package_path( $token );

		if ( null === $path || ! is_readable( $path ) ) {
			return null;
		}

		// Expire files older than 30 minutes.
		$mtime = filemtime( $path );
		if ( false !== $mtime && ( time() - $mtime ) > ( 30 * MINUTE_IN_SECONDS ) ) {
			$this->delete_import_package( $token );
			return null;
		}

		$raw = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( false === $raw || '' === $raw ) {
			return null;
		}

		$package = json_decode( $raw, true );
		return is_array( $package ) ? $package : null;
	}

	/**
	 * Deletes a stored import package.
	 *
	 * @param string $token Import token.
	 * @return void
	 */
	private function delete_import_package( string $token ): void {
		$path = $this->import_package_path( $token );

		if ( null !== $path && file_exists( $path ) ) {
			@unlink( $path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		}
	}

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
