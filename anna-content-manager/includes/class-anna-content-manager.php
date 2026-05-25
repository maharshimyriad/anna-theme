<?php
/**
 * Anna Content Manager plugin bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Anna_Content_Manager {
	/**
	 * Singleton instance.
	 *
	 * @var Anna_Content_Manager|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Anna_Content_Manager
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Activate plugin and seed hero data from legacy theme options.
	 */
	public static function activate() {
		$front_page_id = (int) get_option( 'page_on_front' );
		if ( ! $front_page_id ) {
			return;
		}

		$existing = get_post_meta( $front_page_id, '_anna_content_hero', true );
		if ( is_array( $existing ) && ! empty( array_filter( $existing ) ) ) {
			return;
		}

		$theme_options = get_option( 'anna_theme_options', array() );
		if ( ! is_array( $theme_options ) ) {
			$theme_options = array();
		}

		$hero_data = array(
			'eyebrow'            => $theme_options['hero_eyebrow'] ?? '',
			'heading'            => isset( $theme_options['hero_heading'] ) ? wp_strip_all_tags( str_replace( '<br>', "\n", (string) $theme_options['hero_heading'] ) ) : '',
			'description'        => $theme_options['hero_description'] ?? '',
			'trust_text'         => $theme_options['hero_trust_text'] ?? '',
			'image_id'           => absint( $theme_options['hero_image_id'] ?? 0 ),
			'primary_button_text'=> $theme_options['cta_primary_text'] ?? '',
			'primary_button_url' => $theme_options['cta_primary_url'] ?? '',
			'secondary_button_text' => $theme_options['cta_secondary_text'] ?? '',
			'secondary_button_url'  => $theme_options['cta_secondary_url'] ?? '',
			'stat_1_value'       => $theme_options['stat_1_value'] ?? '',
			'stat_1_label'       => $theme_options['stat_1_label'] ?? '',
			'stat_2_value'       => $theme_options['stat_2_value'] ?? '',
			'stat_2_label'       => $theme_options['stat_2_label'] ?? '',
			'stat_3_value'       => $theme_options['stat_3_value'] ?? '',
			'stat_3_label'       => $theme_options['stat_3_label'] ?? '',
		);

		update_post_meta( $front_page_id, '_anna_content_hero', $hero_data );
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'add_meta_boxes_page', array( $this, 'register_meta_boxes' ) );
		add_action( 'save_post_page', array( $this, 'save_page_content' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	/**
	 * Register page-level content meta boxes.
	 */
	public function register_meta_boxes() {
		add_meta_box(
			'anna_content_hero',
			__( 'Anna Hero Section Content', 'anna-baylis' ),
			array( $this, 'render_hero_meta_box' ),
			'page',
			'normal',
			'high'
		);
	}

	/**
	 * Enqueue admin assets for media selection.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_admin_assets( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen || 'page' !== $screen->post_type ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_script(
			'anna-content-manager-admin',
			ANNA_CONTENT_MANAGER_URL . 'assets/js/admin-page-content.js',
			array( 'jquery' ),
			ANNA_CONTENT_MANAGER_VERSION,
			true
		);
	}

	/**
	 * Render hero meta box fields.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_hero_meta_box( $post ) {
		wp_nonce_field( 'anna_content_save_page', 'anna_content_page_nonce' );

		$data = $this->get_page_section( $post->ID, 'hero' );
		$data = wp_parse_args(
			$data,
			array(
				'eyebrow'               => '',
				'heading'               => '',
				'description'           => '',
				'trust_text'            => '',
				'image_id'              => 0,
				'primary_button_text'   => '',
				'primary_button_url'    => '',
				'secondary_button_text' => '',
				'secondary_button_url'  => '',
				'stat_1_value'          => '',
				'stat_1_label'          => '',
				'stat_2_value'          => '',
				'stat_2_label'          => '',
				'stat_3_value'          => '',
				'stat_3_label'          => '',
			)
		);

		$image_url = ! empty( $data['image_id'] ) ? wp_get_attachment_image_url( absint( $data['image_id'] ), 'medium' ) : '';
		?>
		<p><?php esc_html_e( 'Use this panel to manage hero content for this page without touching the theme layout. The homepage hero reads these values first.', 'anna-baylis' ); ?></p>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="anna-content-hero-eyebrow"><?php esc_html_e( 'Eyebrow', 'anna-baylis' ); ?></label></th>
				<td><input type="text" id="anna-content-hero-eyebrow" name="anna_content_hero[eyebrow]" value="<?php echo esc_attr( $data['eyebrow'] ); ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><label for="anna-content-hero-heading"><?php esc_html_e( 'Heading', 'anna-baylis' ); ?></label></th>
				<td>
					<textarea id="anna-content-hero-heading" name="anna_content_hero[heading]" rows="3" class="large-text"><?php echo esc_textarea( $data['heading'] ); ?></textarea>
					<p class="description"><?php esc_html_e( 'Use new lines where the design should break the headline.', 'anna-baylis' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="anna-content-hero-description"><?php esc_html_e( 'Description', 'anna-baylis' ); ?></label></th>
				<td><textarea id="anna-content-hero-description" name="anna_content_hero[description]" rows="4" class="large-text"><?php echo esc_textarea( $data['description'] ); ?></textarea></td>
			</tr>
			<tr>
				<th scope="row"><label for="anna-content-hero-trust"><?php esc_html_e( 'Trust Text', 'anna-baylis' ); ?></label></th>
				<td><input type="text" id="anna-content-hero-trust" name="anna_content_hero[trust_text]" value="<?php echo esc_attr( $data['trust_text'] ); ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Background Image', 'anna-baylis' ); ?></th>
				<td>
					<input type="hidden" id="anna-content-hero-image-id" name="anna_content_hero[image_id]" value="<?php echo esc_attr( $data['image_id'] ); ?>">
					<div id="anna-content-hero-image-preview" style="margin-bottom:10px;">
						<?php if ( $image_url ) : ?>
							<img src="<?php echo esc_url( $image_url ); ?>" alt="" style="max-width:240px;height:auto;border-radius:10px;">
						<?php endif; ?>
					</div>
					<button type="button" class="button anna-content-media-select" data-target="anna-content-hero-image-id" data-preview="anna-content-hero-image-preview"><?php esc_html_e( 'Select Image', 'anna-baylis' ); ?></button>
					<button type="button" class="button anna-content-media-remove" data-target="anna-content-hero-image-id" data-preview="anna-content-hero-image-preview"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button>
				</td>
			</tr>
		</table>

		<h4><?php esc_html_e( 'Primary Button', 'anna-baylis' ); ?></h4>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="anna-content-hero-primary-text"><?php esc_html_e( 'Text', 'anna-baylis' ); ?></label></th>
				<td><input type="text" id="anna-content-hero-primary-text" name="anna_content_hero[primary_button_text]" value="<?php echo esc_attr( $data['primary_button_text'] ); ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><label for="anna-content-hero-primary-url"><?php esc_html_e( 'URL', 'anna-baylis' ); ?></label></th>
				<td><input type="url" id="anna-content-hero-primary-url" name="anna_content_hero[primary_button_url]" value="<?php echo esc_attr( $data['primary_button_url'] ); ?>" class="regular-text"></td>
			</tr>
		</table>

		<h4><?php esc_html_e( 'Secondary Button', 'anna-baylis' ); ?></h4>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="anna-content-hero-secondary-text"><?php esc_html_e( 'Text', 'anna-baylis' ); ?></label></th>
				<td><input type="text" id="anna-content-hero-secondary-text" name="anna_content_hero[secondary_button_text]" value="<?php echo esc_attr( $data['secondary_button_text'] ); ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><label for="anna-content-hero-secondary-url"><?php esc_html_e( 'URL', 'anna-baylis' ); ?></label></th>
				<td><input type="url" id="anna-content-hero-secondary-url" name="anna_content_hero[secondary_button_url]" value="<?php echo esc_attr( $data['secondary_button_url'] ); ?>" class="regular-text"></td>
			</tr>
		</table>

		<h4><?php esc_html_e( 'Stats', 'anna-baylis' ); ?></h4>
		<table class="form-table">
			<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
				<tr>
					<th scope="row"><?php echo esc_html( sprintf( __( 'Stat %d', 'anna-baylis' ), $i ) ); ?></th>
					<td>
						<input type="text" name="anna_content_hero[stat_<?php echo esc_attr( $i ); ?>_value]" value="<?php echo esc_attr( $data[ 'stat_' . $i . '_value' ] ); ?>" class="small-text" placeholder="<?php esc_attr_e( 'Value', 'anna-baylis' ); ?>">
						<input type="text" name="anna_content_hero[stat_<?php echo esc_attr( $i ); ?>_label]" value="<?php echo esc_attr( $data[ 'stat_' . $i . '_label' ] ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Label', 'anna-baylis' ); ?>">
					</td>
				</tr>
			<?php endfor; ?>
		</table>
		<?php
	}

	/**
	 * Save page content meta.
	 *
	 * @param int $post_id Current post ID.
	 */
	public function save_page_content( $post_id ) {
		if ( ! isset( $_POST['anna_content_page_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['anna_content_page_nonce'] ) ), 'anna_content_save_page' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['anna_content_hero'] ) && is_array( $_POST['anna_content_hero'] ) ) {
			$hero = wp_unslash( $_POST['anna_content_hero'] );
			$data = array(
				'eyebrow'               => sanitize_text_field( $hero['eyebrow'] ?? '' ),
				'heading'               => sanitize_textarea_field( $hero['heading'] ?? '' ),
				'description'           => sanitize_textarea_field( $hero['description'] ?? '' ),
				'trust_text'            => sanitize_text_field( $hero['trust_text'] ?? '' ),
				'image_id'              => absint( $hero['image_id'] ?? 0 ),
				'primary_button_text'   => sanitize_text_field( $hero['primary_button_text'] ?? '' ),
				'primary_button_url'    => esc_url_raw( $hero['primary_button_url'] ?? '' ),
				'secondary_button_text' => sanitize_text_field( $hero['secondary_button_text'] ?? '' ),
				'secondary_button_url'  => esc_url_raw( $hero['secondary_button_url'] ?? '' ),
				'stat_1_value'          => sanitize_text_field( $hero['stat_1_value'] ?? '' ),
				'stat_1_label'          => sanitize_text_field( $hero['stat_1_label'] ?? '' ),
				'stat_2_value'          => sanitize_text_field( $hero['stat_2_value'] ?? '' ),
				'stat_2_label'          => sanitize_text_field( $hero['stat_2_label'] ?? '' ),
				'stat_3_value'          => sanitize_text_field( $hero['stat_3_value'] ?? '' ),
				'stat_3_label'          => sanitize_text_field( $hero['stat_3_label'] ?? '' ),
			);

			update_post_meta( $post_id, '_anna_content_hero', $data );
		}
	}

	/**
	 * Get page section meta.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $section Section key.
	 * @return array
	 */
	public function get_page_section( $post_id, $section ) {
		$post_id = absint( $post_id );
		if ( ! $post_id ) {
			return array();
		}

		$data = get_post_meta( $post_id, '_anna_content_' . sanitize_key( $section ), true );
		return is_array( $data ) ? $data : array();
	}
}

/**
 * Public helper for theme templates.
 *
 * @param int    $post_id Post ID.
 * @param string $section Section key.
 * @return array
 */
function anna_content_get_page_section( $post_id, $section ) {
	return Anna_Content_Manager::instance()->get_page_section( $post_id, $section );
}
