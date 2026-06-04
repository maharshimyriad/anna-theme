<?php
/**
 * Admin UI for Anna Page Scaffolder.
 *
 * @package Anna_Page_Scaffolder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Scaffolder admin screens.
 */
final class Anna_Page_Scaffolder_Admin {

	/**
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * @return self
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ), 99 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_media' ) );
	}

	/**
	 * Register submenu under Anna Theme.
	 */
	public function register_menu() {
		add_submenu_page(
			'anna-theme-settings',
			__( 'Page Scaffolder', 'anna-baylis' ),
			__( 'Page Scaffolder', 'anna-baylis' ),
			'manage_options',
			'anna-page-scaffolder',
			array( $this, 'render_page' )
		);
	}

	/**
	 * @param string $hook Hook suffix.
	 */
	public function enqueue_media( $hook ) {
		if ( 'anna-theme-settings_page_anna-page-scaffolder' !== $hook && 'post.php' !== $hook && 'post-new.php' !== $hook ) {
			return;
		}

		wp_enqueue_media();
	}

	/**
	 * Render scaffolder admin page.
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$notice = '';
		$files  = array();

		if ( isset( $_POST['anna_scaffold_generate'] ) && check_admin_referer( 'anna_scaffold_generate' ) ) {
			$slug  = isset( $_POST['page_slug'] ) ? sanitize_title( wp_unslash( $_POST['page_slug'] ) ) : '';
			$title = isset( $_POST['page_title'] ) ? sanitize_text_field( wp_unslash( $_POST['page_title'] ) ) : '';
			$code  = isset( $_POST['page_code'] ) ? sanitize_key( wp_unslash( $_POST['page_code'] ) ) : '';

			if ( ! $code && $slug ) {
				$code = str_replace( '-', '_', $slug );
			}

			$generator = new Anna_Page_Scaffold_Generator();
			$result    = $generator->generate( $slug, $title, $code );

			if ( $result['success'] ) {
				$notice = '<div class="notice notice-success"><p>' . esc_html( $result['message'] ) . '</p></div>';
				if ( ! empty( $result['files'] ) ) {
					$files = $result['files'];
					$notice .= '<ul style="margin-left:1.5em;list-style:disc;">';
					foreach ( $files as $file ) {
						$notice .= '<li><code>' . esc_html( $file ) . '</code></li>';
					}
					$notice .= '</ul><p>' . esc_html__( 'Visit Settings → your new tab, or edit the WordPress page to customize content.', 'anna-baylis' ) . '</p>';
				}
			} else {
				$notice = '<div class="notice notice-error"><p>' . esc_html( $result['message'] ) . '</p></div>';
			}
		}

		$pages         = anna_get_scaffolded_pages();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Anna Page Scaffolder', 'anna-baylis' ); ?></h1>
			<p><?php esc_html_e( 'Generate a full page structure for the Anna Baylis theme: template, section partials, CSS, theme settings tab, and page editor fields.', 'anna-baylis' ); ?></p>

			<?php echo $notice; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

			<form method="post" style="max-width:640px;margin-top:1.5rem;">
				<?php wp_nonce_field( 'anna_scaffold_generate' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="page_slug"><?php esc_html_e( 'Page slug', 'anna-baylis' ); ?></label></th>
						<td>
							<input type="text" name="page_slug" id="page_slug" class="regular-text" required placeholder="contact" value="">
							<p class="description"><?php esc_html_e( 'URL path, e.g. contact → /contact/', 'anna-baylis' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="page_title"><?php esc_html_e( 'Page title', 'anna-baylis' ); ?></label></th>
						<td>
							<input type="text" name="page_title" id="page_title" class="regular-text" required placeholder="Contact" value="">
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="page_code"><?php esc_html_e( 'Code prefix', 'anna-baylis' ); ?></label></th>
						<td>
							<input type="text" name="page_code" id="page_code" class="regular-text" placeholder="contact" pattern="[a-z][a-z0-9_]*">
							<p class="description"><?php esc_html_e( 'Used in PHP function names (auto-filled from slug if empty).', 'anna-baylis' ); ?></p>
						</td>
					</tr>
				</table>
				<?php submit_button( __( 'Generate Page Structure', 'anna-baylis' ), 'primary', 'anna_scaffold_generate' ); ?>
			</form>

			<?php if ( ! empty( $pages ) ) : ?>
				<h2 style="margin-top:2.5rem;"><?php esc_html_e( 'Scaffolded pages', 'anna-baylis' ); ?></h2>
				<table class="widefat striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Title', 'anna-baylis' ); ?></th>
							<th><?php esc_html_e( 'Slug', 'anna-baylis' ); ?></th>
							<th><?php esc_html_e( 'Code', 'anna-baylis' ); ?></th>
							<th><?php esc_html_e( 'Template', 'anna-baylis' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $pages as $page ) : ?>
							<tr>
								<td><?php echo esc_html( $page['title'] ?? '' ); ?></td>
								<td><code>/<?php echo esc_html( $page['slug'] ?? '' ); ?>/</code></td>
								<td><code><?php echo esc_html( $page['code'] ?? '' ); ?></code></td>
								<td><code><?php echo esc_html( $page['template_file'] ?? '' ); ?></code></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
		<script>
		(function () {
			const slug = document.getElementById('page_slug');
			const code = document.getElementById('page_code');
			if (!slug || !code) return;
			slug.addEventListener('change', function () {
				if (code.value) return;
				code.value = slug.value.replace(/-/g, '_').replace(/[^a-z0-9_]/g, '');
			});
		})();
		</script>
		<?php
	}
}
