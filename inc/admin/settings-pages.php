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
 * Apply the homepage design preset to saved theme settings.
 */
function anna_apply_homepage_preset_action() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to do that.', 'anna-baylis' ) );
	}

	check_admin_referer( 'anna_apply_homepage_preset' );

	$defaults = anna_get_default_options();
	$existing = get_option( 'anna_theme_options', array() );
	$existing = is_array( $existing ) ? $existing : array();

	$preset_keys = array(
		'header_cta_text', 'header_cta_url',
		'hero_eyebrow', 'hero_heading', 'hero_description', 'hero_trust_text',
		'stat_1_value', 'stat_1_label', 'stat_2_value', 'stat_2_label', 'stat_3_value', 'stat_3_label',
		'intro_eyebrow', 'intro_heading', 'intro_body', 'intro_quote', 'intro_quote_cite',
		'recognition_eyebrow', 'recognition_heading', 'recognition_description', 'recognition_items_text',
		'services_eyebrow', 'services_heading', 'services_description', 'services_cta_text', 'services_cta_url',
		'about_eyebrow', 'about_heading', 'about_body', 'about_quote', 'about_badge_number', 'about_badge_text', 'about_expertise_text', 'about_cta_text', 'about_cta_url',
		'testimonials_eyebrow', 'testimonials_heading', 'testimonials_summary', 'testimonials_cta_text', 'testimonials_cta_url',
		'cta_eyebrow', 'cta_heading', 'cta_description', 'cta_trust', 'cta_primary_text', 'cta_primary_url', 'cta_secondary_text', 'cta_secondary_url',
		'footer_description', 'contact_email', 'contact_phone', 'contact_address', 'contact_hours',
		'newsletter_heading', 'newsletter_text', 'newsletter_name_placeholder', 'newsletter_email_placeholder', 'newsletter_button_text', 'copyright_text',
	);

	foreach ( $preset_keys as $key ) {
		if ( array_key_exists( $key, $defaults ) ) {
			$existing[ $key ] = $defaults[ $key ];
		}
	}

	update_option( 'anna_theme_options', $existing );

	wp_safe_redirect(
		add_query_arg(
			array(
				'page'          => 'anna-theme-settings',
				'tab'           => 'content',
				'anna_preset'   => 'applied',
			),
			admin_url( 'admin.php' )
		)
	);
	exit;
}
add_action( 'admin_post_anna_apply_homepage_preset', 'anna_apply_homepage_preset_action' );

/**
 * Define settings tabs.
 *
 * @return array
 */
function anna_get_settings_tabs() {
	return array(
		'brand'      => __( 'Brand', 'anna-baylis' ),
		'typography' => __( 'Typography', 'anna-baylis' ),
		'layout'     => __( 'Layout', 'anna-baylis' ),
		'header'     => __( 'Header', 'anna-baylis' ),
		'hero'       => __( 'Hero', 'anna-baylis' ),
		'sections'   => __( 'Sections', 'anna-baylis' ),
		'content'    => __( 'Content', 'anna-baylis' ),
		'cta'        => __( 'CTA', 'anna-baylis' ),
		'footer'     => __( 'Footer', 'anna-baylis' ),
		'social'     => __( 'Social', 'anna-baylis' ),
		'animations' => __( 'Animations', 'anna-baylis' ),
		'seo'        => __( 'SEO', 'anna-baylis' ),
	);
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

		<?php if ( isset( $_GET['anna_preset'] ) && 'applied' === sanitize_key( wp_unslash( $_GET['anna_preset'] ) ) ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Homepage preset applied. Recheck Hero, Content, CTA, Footer, logo, menu, and image selections.', 'anna-baylis' ); ?></p></div>
		<?php endif; ?>

		<div style="margin:16px 0 20px;">
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="anna_apply_homepage_preset">
				<?php wp_nonce_field( 'anna_apply_homepage_preset' ); ?>
				<button type="submit" class="button button-secondary"><?php esc_html_e( 'Apply Homepage Design Preset', 'anna-baylis' ); ?></button>
				<p class="description" style="margin-top:8px;"><?php esc_html_e( 'This overwrites saved homepage text/settings with the current design preset. It does not assign menus, logo, or media images.', 'anna-baylis' ); ?></p>
			</form>
		</div>

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
					<?php anna_field_select( 'header_style', __( 'Header Style', 'anna-baylis' ), array( 'transparent' => 'Transparent (Hero)', 'solid' => 'Solid Background' ) ); ?>
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
