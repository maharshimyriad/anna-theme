<?php
/**
 * Admin Settings - Pages Registration
 *
 * Registers admin menu pages and renders tab-based settings UI.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add top-level admin menu.
 */
function anna_add_admin_menu() {
	add_menu_page(
		__( 'Anna Baylis Theme', 'anna-baylis' ),
		__( 'Anna Theme', 'anna-baylis' ),
		'manage_options',
		'anna-theme-settings',
		'anna_render_settings_page',
		'dashicons-admin-customizer',
		3
	);
}
add_action( 'admin_menu', 'anna_add_admin_menu' );

/**
 * Define settings tabs.
 *
 * @return array
 */
function anna_get_settings_tabs() {
	$tabs = array(
		'brand'      => __( 'Brand', 'anna-baylis' ),
		'typography' => __( 'Typography', 'anna-baylis' ),
		'layout'     => __( 'Layout', 'anna-baylis' ),
		'header'     => __( 'Header', 'anna-baylis' ),
		// 'hero'       => __( 'Hero', 'anna-baylis' ),
		// 'sections'   => __( 'Sections', 'anna-baylis' ),
		// 'content'    => __( 'Content', 'anna-baylis' ),
		// 'about_page'    => __( 'About Page', 'anna-baylis' ),
		// 'coaching_page' => __( 'Coaching Page', 'anna-baylis' ),
		// 'oasis_page'    => __( 'Oasis Page', 'anna-baylis' ),
		// 'speaking_page' => __( 'Speaking Page', 'anna-baylis' ),
		// 'mhs_page'      => __( 'Mental Health Page', 'anna-baylis' ),
		// 'move_page'     => __( 'MOVE Page', 'anna-baylis' ),
		// 'cta'        => __( 'CTA', 'anna-baylis' ),
		'footer'     => __( 'Footer', 'anna-baylis' ),
		'social'     => __( 'Social', 'anna-baylis' ),
		'animations' => __( 'Animations', 'anna-baylis' ),
		'seo'        => __( 'SEO', 'anna-baylis' ),
	);

	return apply_filters( 'anna_settings_tabs', $tabs );
}

/**
 * Render the main settings page.
 */
function anna_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$tabs       = anna_get_settings_tabs();
	$active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'brand';
	$social     = anna_get_social_links();
	$toast_type = '';
	$toast_text = '';

	if ( isset( $_GET['settings-updated'] ) && 'true' === sanitize_text_field( wp_unslash( $_GET['settings-updated'] ) ) ) {
		$toast_type = 'success';
		$toast_text = __( 'Theme settings saved successfully.', 'anna-baylis' );
	}

	$settings_messages = get_settings_errors( 'anna_theme_options' );
	if ( ! empty( $settings_messages ) ) {
		$last_message = end( $settings_messages );
		if ( ! empty( $last_message['message'] ) ) {
			$toast_type = 'error' === $last_message['type'] ? 'error' : 'success';
			$toast_text = wp_strip_all_tags( $last_message['message'] );
		}
	}
	?>
	<div class="wrap anna-admin-wrap">
		<?php if ( $toast_text ) : ?>
			<div class="anna-admin-toast anna-admin-toast--<?php echo esc_attr( $toast_type ); ?>" data-anna-toast="true" role="status" aria-live="polite">
				<?php echo esc_html( $toast_text ); ?>
			</div>
		<?php endif; ?>
		<h1 class="anna-admin-title">
			<span class="anna-admin-logo">*</span>
			<?php esc_html_e( 'Anna Baylis - Theme Settings', 'anna-baylis' ); ?>
		</h1>

		<p class="description" style="margin-top:0.5rem;padding:0.75rem 1rem;background:#f0f6fc;border-left:4px solid #72aee6;border-radius:2px;max-width:700px;">
			<strong><?php esc_html_e( 'Tip:', 'anna-baylis' ); ?></strong>
			<?php esc_html_e( 'Type', 'anna-baylis' ); ?>
			<code>empty--</code>
			<?php esc_html_e( 'into any text field to intentionally leave it blank on the frontend (hides the default content).', 'anna-baylis' ); ?>
		</p>

		<nav class="nav-tab-wrapper anna-admin-tabs" aria-label="<?php esc_attr_e( 'Settings tabs', 'anna-baylis' ); ?>">
			<?php foreach ( $tabs as $slug => $label ) : ?>
				<a href="?page=anna-theme-settings&tab=<?php echo esc_attr( $slug ); ?>" class="nav-tab <?php echo $active_tab === $slug ? 'nav-tab-active' : ''; ?>" aria-current="<?php echo $active_tab === $slug ? 'page' : 'false'; ?>">
					<?php echo esc_html( $label ); ?>
				</a>
			<?php endforeach; ?>
		</nav>

		<form method="post" action="options.php" class="anna-admin-form">
			<?php settings_fields( 'anna_theme_options_group' ); ?>
			<input type="hidden" name="anna_theme_options[_anna_active_tab]" value="<?php echo esc_attr( $active_tab ); ?>">

			<table class="form-table anna-admin-table">
				<?php if ( 'brand' === $active_tab ) : ?>
					<?php anna_field_heading( __( 'Brand Identity', 'anna-baylis' ) ); ?>
					<?php anna_field_media( 'site_logo_id', __( 'Site Logo', 'anna-baylis' ), __( 'Used across the header, mobile menu, and footer. Falls back to the WordPress custom logo if left empty.', 'anna-baylis' ) ); ?>
					<?php anna_field_heading( __( 'Brand Colors', 'anna-baylis' ) ); ?>
					<?php anna_field_color( 'color_primary', __( 'Primary Deep Green', 'anna-baylis' ), '#007063' ); ?>
					<?php anna_field_color( 'color_accent', __( 'Accent Green', 'anna-baylis' ), '#4CA591' ); ?>
					<?php anna_field_color( 'color_bg_soft', __( 'Soft Background', 'anna-baylis' ), '#F2F6F2' ); ?>
					<?php anna_field_color( 'color_text', __( 'Body Text Color', 'anna-baylis' ), '#1A2B25' ); ?>
					<?php anna_field_color( 'color_heading', __( 'Heading Color', 'anna-baylis' ), '#0F1F1B' ); ?>

				<?php elseif ( 'typography' === $active_tab ) : ?>
					<?php anna_field_heading( __( 'Typography', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'font_heading', __( 'Heading Font Family', 'anna-baylis' ), '', 'text', "'Lexend', sans-serif" ); ?>
					<?php anna_field_text( 'font_body', __( 'Body Font Family', 'anna-baylis' ), '', 'text', "'Mulish', sans-serif" ); ?>
					<?php anna_field_text( 'font_size_base', __( 'Base Font Size', 'anna-baylis' ), __( 'CSS value, e.g. 1rem', 'anna-baylis' ) ); ?>
					<?php anna_field_select( 'font_weight_heading', __( 'Heading Weight', 'anna-baylis' ), array( '300' => 'Light', '400' => 'Regular', '500' => 'Medium', '600' => 'Semi-Bold', '700' => 'Bold' ) ); ?>
					<?php anna_field_select( 'font_weight_body', __( 'Body Weight', 'anna-baylis' ), array( '300' => 'Light', '400' => 'Regular', '500' => 'Medium', '600' => 'Semi-Bold' ) ); ?>

				<?php elseif ( 'layout' === $active_tab ) : ?>
					<?php anna_field_heading( __( 'Layout & Spacing', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'container_max', __( 'Max Container Width', 'anna-baylis' ), '', 'text', '1320px' ); ?>
					<?php anna_field_text( 'container_wide', __( 'Wide Container Width', 'anna-baylis' ), '', 'text', '1440px' ); ?>
					<?php anna_field_text( 'section_padding_md', __( 'Default Section Padding', 'anna-baylis' ), __( 'CSS clamp or fixed value', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'border_radius_btn', __( 'Button Border Radius', 'anna-baylis' ), '', 'text', '9999px' ); ?>

				<?php elseif ( 'header' === $active_tab ) : ?>
					<?php anna_field_heading( __( 'Header', 'anna-baylis' ) ); ?>
					<p class="description"><?php esc_html_e( 'The site header always uses a solid background.', 'anna-baylis' ); ?></p>
					<?php anna_field_text( 'header_cta_text', __( 'CTA Button Text', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'header_cta_url', __( 'CTA Button URL', 'anna-baylis' ), '', 'url' ); ?>

				<?php elseif ( 'hero' === $active_tab ) : ?>
					<?php anna_field_heading( __( 'Hero Section', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'hero_eyebrow', __( 'Eyebrow Text', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'hero_heading', __( 'Main Heading', 'anna-baylis' ), __( 'HTML allowed: use <br> for line breaks', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'hero_description', __( 'Description', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'hero_trust_text', __( 'Trust Text', 'anna-baylis' ) ); ?>
					<?php anna_field_media( 'hero_image_id', __( 'Hero Image', 'anna-baylis' ), __( 'Main hero background image.', 'anna-baylis' ) ); ?>
					<?php anna_field_heading( __( 'Statistics Cards', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'stat_1_value', __( 'Stat 1 Value', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'stat_1_label', __( 'Stat 1 Label', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'stat_2_value', __( 'Stat 2 Value', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'stat_2_label', __( 'Stat 2 Label', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'stat_3_value', __( 'Stat 3 Value', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'stat_3_label', __( 'Stat 3 Label', 'anna-baylis' ) ); ?>

				<?php elseif ( 'sections' === $active_tab ) : ?>
					<?php anna_field_heading( __( 'Section Visibility', 'anna-baylis' ), __( 'Toggle homepage sections on or off.', 'anna-baylis' ) ); ?>
					<?php anna_field_toggle( 'section_hero_enabled', __( 'Hero Section', 'anna-baylis' ) ); ?>
					<?php anna_field_toggle( 'section_intro_enabled', __( 'Intro / Approach Section', 'anna-baylis' ) ); ?>
					<?php anna_field_toggle( 'section_recognition_enabled', __( 'Recognition Section', 'anna-baylis' ) ); ?>
					<?php anna_field_toggle( 'section_services_enabled', __( 'Services Section', 'anna-baylis' ) ); ?>
					<?php anna_field_toggle( 'section_about_enabled', __( 'About Section', 'anna-baylis' ) ); ?>
					<?php anna_field_toggle( 'section_testimonials_enabled', __( 'Testimonials Section', 'anna-baylis' ) ); ?>
					<?php anna_field_toggle( 'section_cta_enabled', __( 'CTA Section', 'anna-baylis' ) ); ?>

				<?php elseif ( 'content' === $active_tab ) : ?>
					<?php anna_field_heading( __( 'Intro / Approach Section', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'intro_eyebrow', __( 'Eyebrow', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'intro_heading', __( 'Heading', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'intro_body', __( 'Body Copy', 'anna-baylis' ), __( 'HTML allowed: paragraphs with <p> tags', 'anna-baylis' ), 6 ); ?>
					<?php anna_field_textarea( 'intro_quote', __( 'Pull Quote', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'intro_quote_cite', __( 'Quote Citation', 'anna-baylis' ) ); ?>
					<?php anna_field_media( 'intro_image_id', __( 'Intro Section Image', 'anna-baylis' ), __( 'Optional image for the intro section.', 'anna-baylis' ) ); ?>

					<?php anna_field_heading( __( 'Recognition Section', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'recognition_eyebrow', __( 'Eyebrow', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'recognition_heading', __( 'Heading', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'recognition_description', __( 'Description', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'recognition_items_text', __( 'Recognition Items', 'anna-baylis' ), __( 'One list item per line.', 'anna-baylis' ), 8 ); ?>
					<?php anna_field_media( 'recognition_image_id', __( 'Recognition Section Image', 'anna-baylis' ), __( 'Optional background or accent image.', 'anna-baylis' ) ); ?>

					<?php anna_field_heading( __( 'Services Section', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'services_eyebrow', __( 'Eyebrow', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'services_heading', __( 'Heading', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'services_description', __( 'Description', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'services_cta_text', __( 'CTA Button Text', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'services_cta_url', __( 'CTA Button URL', 'anna-baylis' ), '', 'url' ); ?>

					<?php anna_field_heading( __( 'About Section', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'about_eyebrow', __( 'Eyebrow', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'about_heading', __( 'Heading', 'anna-baylis' ), __( 'HTML allowed.', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'about_body', __( 'Body Text', 'anna-baylis' ), __( 'HTML allowed: paragraphs with <p> tags', 'anna-baylis' ), 6 ); ?>
					<?php anna_field_media( 'about_image_id', __( 'About Portrait Image', 'anna-baylis' ), __( 'Portrait photo shown in the About section.', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'about_badge_number', __( 'Experience Badge Number', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'about_badge_text', __( 'Experience Badge Text', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'about_quote', __( 'Pull Quote', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'about_expertise_text', __( 'Expertise Tags', 'anna-baylis' ), __( 'One tag per line.', 'anna-baylis' ), 8 ); ?>
					<?php anna_field_text( 'about_cta_text', __( 'CTA Button Text', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'about_cta_url', __( 'CTA Button URL', 'anna-baylis' ), '', 'url' ); ?>

					<?php anna_field_heading( __( 'Testimonials Section', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'testimonials_eyebrow', __( 'Eyebrow', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'testimonials_heading', __( 'Heading', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'testimonials_summary', __( 'Summary Line', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'testimonials_cta_text', __( 'Reviews Link Text', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'testimonials_cta_url', __( 'Reviews Link URL', 'anna-baylis' ), '', 'url' ); ?>

				<?php elseif ( 'about_page' === $active_tab ) : ?>
					<?php anna_field_heading( __( 'About Page Hero', 'anna-baylis' ), __( 'Content for the About page template. Defaults match the design; edit here without touching code.', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'about_pg_hero_eyebrow', __( 'Eyebrow', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'about_pg_hero_heading', __( 'Heading', 'anna-baylis' ), __( 'Use line breaks for the hero layout.', 'anna-baylis' ), 4 ); ?>
					<?php anna_field_text( 'about_pg_hero_subheading', __( 'Subheading (optional)', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'about_pg_hero_description', __( 'Description (optional)', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'about_pg_hero_tags_text', __( 'Hero Tags (pills)', 'anna-baylis' ), __( 'One tag per line.', 'anna-baylis' ), 6 ); ?>
					<?php anna_field_media( 'about_pg_hero_image_id', __( 'Hero Background Image', 'anna-baylis' ) ); ?>

					<?php anna_field_heading( __( 'My Story', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'about_pg_story_eyebrow', __( 'Eyebrow', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'about_pg_story_heading', __( 'Heading', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'about_pg_story_body', __( 'Body Copy', 'anna-baylis' ), __( 'One paragraph per blank line.', 'anna-baylis' ), 8 ); ?>
					<?php anna_field_media( 'about_pg_story_image_id', __( 'Portrait Image', 'anna-baylis' ) ); ?>

					<?php anna_field_heading( __( 'My Rock Bottom', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'about_pg_rock_heading', __( 'Heading', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'about_pg_rock_left_body', __( 'Left Column', 'anna-baylis' ), __( 'One paragraph per blank line.', 'anna-baylis' ), 8 ); ?>
					<?php anna_field_textarea( 'about_pg_rock_right_body', __( 'Right Column', 'anna-baylis' ), __( 'One paragraph per blank line.', 'anna-baylis' ), 8 ); ?>

					<?php anna_field_heading( __( 'How I Became a Coach', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'about_pg_coach_eyebrow', __( 'Eyebrow', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'about_pg_coach_title', __( 'Heading', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'about_pg_coach_body', __( 'Body Copy', 'anna-baylis' ), __( 'One paragraph per blank line.', 'anna-baylis' ), 8 ); ?>
					<?php anna_field_text( 'about_pg_coach_button_text', __( 'Button Text', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'about_pg_coach_button_url', __( 'Button URL', 'anna-baylis' ), '', 'url' ); ?>
					<?php anna_field_media( 'about_pg_coach_image_id', __( 'Right Image', 'anna-baylis' ) ); ?>

					<?php anna_field_heading( __( 'How I Work', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'about_pg_work_eyebrow', __( 'Eyebrow', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'about_pg_work_heading', __( 'Heading', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'about_pg_work_body', __( 'Left Column Copy', 'anna-baylis' ), __( 'One paragraph per blank line.', 'anna-baylis' ), 10 ); ?>
					<?php anna_field_heading( __( 'How I Work Cards', 'anna-baylis' ), __( 'These appear as stacked cards in the right column.', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'about_pg_work_card_1_title', __( 'Card 1 Title', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'about_pg_work_card_1_body', __( 'Card 1 Body', 'anna-baylis' ), '', 3 ); ?>
					<?php anna_field_text( 'about_pg_work_card_2_title', __( 'Card 2 Title', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'about_pg_work_card_2_body', __( 'Card 2 Body', 'anna-baylis' ), '', 3 ); ?>
					<?php anna_field_text( 'about_pg_work_card_3_title', __( 'Card 3 Title', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'about_pg_work_card_3_body', __( 'Card 3 Body', 'anna-baylis' ), '', 3 ); ?>
					<?php anna_field_text( 'about_pg_work_card_4_title', __( 'Card 4 Title', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'about_pg_work_card_4_body', __( 'Card 4 Body', 'anna-baylis' ), '', 3 ); ?>

					<?php anna_field_heading( __( 'What People Say', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'about_pg_people_eyebrow', __( 'Eyebrow', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'about_pg_people_heading', __( 'Heading', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'about_pg_people_body', __( 'Intro', 'anna-baylis' ), '', 4 ); ?>

					<?php
					$people_items = anna_get_option( 'about_pg_people_items', array() );
					if ( ( ! is_array( $people_items ) || empty( $people_items ) ) && function_exists( 'anna_get_about_people_items_from_options' ) ) {
						$people_items = anna_get_about_people_items_from_options();
					}
					$people_items = is_array( $people_items ) ? $people_items : array();
					$people_count = count( $people_items );
					?>
					<tr>
						<th scope="row"><?php esc_html_e( 'Cards', 'anna-baylis' ); ?></th>
						<td>
							<p class="description"><?php esc_html_e( 'Optional logo image, or initials shown in the green circle when no logo is set.', 'anna-baylis' ); ?></p>
							<div class="anna-repeater-collapse">
								<button type="button" class="anna-repeater-collapse__toggle" aria-expanded="false">
									<span class="anna-repeater-collapse__arrow" aria-hidden="true">▶</span>
									<span class="anna-repeater-collapse__label">
										<?php
										echo esc_html(
											sprintf(
												/* translators: %d: number of qualification cards */
												__( 'Show all cards (%d)', 'anna-baylis' ),
												$people_count
											)
										);
										?>
									</span>
								</button>
								<div class="anna-repeater-collapse__panel is-collapsed" data-anna-repeater-collapse-panel="true">
							<div class="anna-admin-repeater" data-anna-repeater="about-people">
								<div class="anna-admin-repeater__rows" data-anna-repeater-rows="true">
									<?php foreach ( $people_items as $index => $item ) : ?>
										<?php
										$logo_id     = absint( $item['logo_id'] ?? 0 );
										$initials    = (string) ( $item['initials'] ?? '' );
										$title       = (string) ( $item['title'] ?? '' );
										$desc        = (string) ( $item['org'] ?? $item['description'] ?? '' );
										$preview_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'thumbnail' ) : '';
										?>
										<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
											<div class="anna-admin-repeater__row-fields">
												<div class="anna-admin-repeater__field">
													<label class="anna-admin-repeater__label"><?php esc_html_e( 'Logo', 'anna-baylis' ); ?></label>
													<input type="hidden" id="anna-about-people-<?php echo esc_attr( $index ); ?>-logo" name="anna_theme_options[about_pg_people_items][<?php echo esc_attr( $index ); ?>][logo_id]" value="<?php echo esc_attr( $logo_id ); ?>">
													<div class="anna-media-preview" id="anna-about-people-<?php echo esc_attr( $index ); ?>-logo-preview">
														<?php if ( $preview_url ) : ?>
															<img src="<?php echo esc_url( $preview_url ); ?>" alt="" style="max-width:150px;height:auto;border-radius:8px;">
														<?php endif; ?>
													</div>
													<button type="button" class="button anna-media-upload-btn" data-target="anna-about-people-<?php echo esc_attr( $index ); ?>-logo" data-preview="anna-about-people-<?php echo esc_attr( $index ); ?>-logo-preview"><?php esc_html_e( 'Select Image', 'anna-baylis' ); ?></button>
													<button type="button" class="button anna-media-remove-btn" data-target="anna-about-people-<?php echo esc_attr( $index ); ?>-logo" data-preview="anna-about-people-<?php echo esc_attr( $index ); ?>-logo-preview" <?php echo ! $logo_id ? 'style="display:none;"' : ''; ?>><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button>
												</div>

												<div class="anna-admin-repeater__field">
													<label class="anna-admin-repeater__label"><?php esc_html_e( 'Initials', 'anna-baylis' ); ?></label>
													<input type="text" name="anna_theme_options[about_pg_people_items][<?php echo esc_attr( $index ); ?>][initials]" value="<?php echo esc_attr( $initials ); ?>" class="small-text" placeholder="HM">
												</div>

												<div class="anna-admin-repeater__field">
													<label class="anna-admin-repeater__label"><?php esc_html_e( 'Title', 'anna-baylis' ); ?></label>
													<input type="text" name="anna_theme_options[about_pg_people_items][<?php echo esc_attr( $index ); ?>][title]" value="<?php echo esc_attr( $title ); ?>" class="regular-text">
												</div>

												<div class="anna-admin-repeater__field">
													<label class="anna-admin-repeater__label"><?php esc_html_e( 'Description', 'anna-baylis' ); ?></label>
													<textarea name="anna_theme_options[about_pg_people_items][<?php echo esc_attr( $index ); ?>][description]" rows="2" class="large-text"><?php echo esc_textarea( $desc ); ?></textarea>
												</div>
											</div>

											<div class="anna-admin-repeater__row-actions">
												<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button>
											</div>
										</div>
									<?php endforeach; ?>
								</div>

								<button type="button" class="button" data-anna-repeater-add="true"><?php esc_html_e( 'Add Card', 'anna-baylis' ); ?></button>

								<template data-anna-repeater-template="true">
									<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
										<div class="anna-admin-repeater__row-fields">
											<div class="anna-admin-repeater__field">
												<label class="anna-admin-repeater__label"><?php esc_html_e( 'Logo', 'anna-baylis' ); ?></label>
												<input type="hidden" id="anna-about-people-__INDEX__-logo" name="anna_theme_options[about_pg_people_items][__INDEX__][logo_id]" value="">
												<div class="anna-media-preview" id="anna-about-people-__INDEX__-logo-preview"></div>
												<button type="button" class="button anna-media-upload-btn" data-target="anna-about-people-__INDEX__-logo" data-preview="anna-about-people-__INDEX__-logo-preview"><?php esc_html_e( 'Select Image', 'anna-baylis' ); ?></button>
												<button type="button" class="button anna-media-remove-btn" data-target="anna-about-people-__INDEX__-logo" data-preview="anna-about-people-__INDEX__-logo-preview" style="display:none;"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button>
											</div>

											<div class="anna-admin-repeater__field">
												<label class="anna-admin-repeater__label"><?php esc_html_e( 'Initials', 'anna-baylis' ); ?></label>
												<input type="text" name="anna_theme_options[about_pg_people_items][__INDEX__][initials]" value="" class="small-text" placeholder="HM">
											</div>

											<div class="anna-admin-repeater__field">
												<label class="anna-admin-repeater__label"><?php esc_html_e( 'Title', 'anna-baylis' ); ?></label>
												<input type="text" name="anna_theme_options[about_pg_people_items][__INDEX__][title]" value="" class="regular-text">
											</div>

											<div class="anna-admin-repeater__field">
												<label class="anna-admin-repeater__label"><?php esc_html_e( 'Description', 'anna-baylis' ); ?></label>
												<textarea name="anna_theme_options[about_pg_people_items][__INDEX__][description]" rows="2" class="large-text"></textarea>
											</div>
										</div>

										<div class="anna-admin-repeater__row-actions">
											<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button>
										</div>
									</div>
								</template>
							</div>
								</div>
							</div>
						</td>
					</tr>

					<?php anna_field_heading( __( 'I would love to connect', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'about_pg_connect_eyebrow', __( 'Intro line', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'about_pg_connect_heading', __( 'Heading', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'about_pg_connect_button_text', __( 'Button Text', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'about_pg_connect_button_url', __( 'Button URL', 'anna-baylis' ), '', 'url' ); ?>

				<?php elseif ( 'coaching_page' === $active_tab ) : ?>
					<?php anna_render_coaching_page_settings_fields(); ?>
				<?php elseif ( 'oasis_page' === $active_tab ) : ?>
					<?php anna_render_oasis_page_settings_fields(); ?>
				<?php elseif ( 'speaking_page' === $active_tab ) : ?>
					<?php anna_render_speaking_page_settings_fields(); ?>
				<?php elseif ( 'mhs_page' === $active_tab ) : ?>
					<?php anna_render_mhs_page_settings_fields(); ?>
				<?php elseif ( 'move_page' === $active_tab ) : ?>
					<?php anna_render_move_page_settings_fields(); ?>

				<?php elseif ( anna_is_scaffold_settings_tab( $active_tab ) ) : ?>
					<?php anna_render_scaffold_settings_tab( $active_tab ); ?>

				<?php elseif ( 'cta' === $active_tab ) : ?>
					<?php anna_field_heading( __( 'Final CTA Section', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'cta_eyebrow', __( 'Eyebrow', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'cta_heading', __( 'Heading', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'cta_description', __( 'Description', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'cta_trust', __( 'Trust Text', 'anna-baylis' ) ); ?>
					<?php anna_field_media( 'cta_image_id', __( 'CTA Background Image', 'anna-baylis' ), __( 'Optional background image for the CTA section.', 'anna-baylis' ) ); ?>
					<?php anna_field_heading( __( 'Primary CTA Button', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'cta_primary_text', __( 'Button Text', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'cta_primary_url', __( 'Button URL', 'anna-baylis' ), '', 'url' ); ?>
					<?php anna_field_heading( __( 'Secondary CTA Button', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'cta_secondary_text', __( 'Button Text', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'cta_secondary_url', __( 'Button URL', 'anna-baylis' ), '', 'url' ); ?>

				<?php elseif ( 'footer' === $active_tab ) : ?>
					<?php anna_field_heading( __( 'Footer Logo', 'anna-baylis' ) ); ?>
					<?php anna_field_media( 'footer_logo_id', __( 'Footer Logo', 'anna-baylis' ), __( 'Logo shown in the footer. Falls back to the site logo if left empty.', 'anna-baylis' ) ); ?>
					<?php anna_field_heading( __( 'Footer Content', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'footer_description', __( 'Brand Description', 'anna-baylis' ), '', 5 ); ?>
					<?php anna_field_text( 'contact_email', __( 'Email', 'anna-baylis' ), '', 'email' ); ?>
					<?php anna_field_text( 'contact_phone', __( 'Phone', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'contact_address', __( 'Address', 'anna-baylis' ), __( 'Multiple lines allowed.', 'anna-baylis' ), 4 ); ?>
					<?php anna_field_text( 'contact_hours', __( 'Business Hours', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'newsletter_heading', __( 'Newsletter Heading', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'newsletter_text', __( 'Newsletter Description', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'newsletter_name_placeholder', __( 'Newsletter Name Placeholder', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'newsletter_email_placeholder', __( 'Newsletter Email Placeholder', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'newsletter_button_text', __( 'Newsletter Button Text', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'copyright_text', __( 'Copyright Text', 'anna-baylis' ), __( 'Leave blank for auto year/name', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'privacy_url', __( 'Privacy Policy URL', 'anna-baylis' ), '', 'url' ); ?>
					<?php anna_field_text( 'terms_url', __( 'Terms of Service URL', 'anna-baylis' ), '', 'url' ); ?>

				<?php elseif ( 'social' === $active_tab ) : ?>
					<?php anna_field_heading( __( 'Social Media Links', 'anna-baylis' ), __( 'Enter the full URL for each profile.', 'anna-baylis' ) ); ?>
					<?php foreach ( $social as $platform => $url ) : ?>
						<tr>
							<th scope="row"><label for="anna-social-<?php echo esc_attr( $platform ); ?>"><?php echo esc_html( ucfirst( $platform ) ); ?></label></th>
							<td><input type="url" id="anna-social-<?php echo esc_attr( $platform ); ?>" name="anna_theme_options[social_links][<?php echo esc_attr( $platform ); ?>]" value="<?php echo esc_url( $url ); ?>" class="regular-text" placeholder="https://"></td>
						</tr>
					<?php endforeach; ?>

				<?php elseif ( 'animations' === $active_tab ) : ?>
					<?php anna_field_heading( __( 'Animation Settings', 'anna-baylis' ) ); ?>
					<?php anna_field_toggle( 'animations_enabled', __( 'Enable Animations', 'anna-baylis' ), __( 'GSAP animations and scroll effects', 'anna-baylis' ) ); ?>
					<?php anna_field_select( 'animation_speed', __( 'Animation Speed', 'anna-baylis' ), array( 'fast' => 'Fast', 'normal' => 'Normal', 'slow' => 'Slow', 'very-slow' => 'Very Slow' ) ); ?>

				<?php elseif ( 'seo' === $active_tab ) : ?>
					<?php anna_field_heading( __( 'SEO Defaults', 'anna-baylis' ) ); ?>
					<?php anna_field_text( 'seo_default_title_suffix', __( 'Title Suffix', 'anna-baylis' ) ); ?>
					<?php anna_field_textarea( 'seo_default_description', __( 'Default Meta Description', 'anna-baylis' ) ); ?>
					<?php anna_field_media( 'seo_og_image_id', __( 'Default OG Image', 'anna-baylis' ), __( 'Used when pages do not have a featured image.', 'anna-baylis' ) ); ?>
				<?php endif; ?>
			</table>

			<?php submit_button( __( 'Save Settings', 'anna-baylis' ) ); ?>
		</form>
	</div>
	<?php
}
