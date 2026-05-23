<?php
/**
 * Admin Settings — Pages Registration
 *
 * Registers admin menu pages and renders tab-based settings UI.
 * Each tab submits a hidden _anna_active_tab field so the sanitizer
 * knows which fields to update (preserving all other tabs).
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
 */
function anna_get_settings_tabs() {
	return array(
		'brand'       => __( 'Brand', 'anna-baylis' ),
		'typography'  => __( 'Typography', 'anna-baylis' ),
		'layout'      => __( 'Layout', 'anna-baylis' ),
		'header'      => __( 'Header', 'anna-baylis' ),
		'hero'        => __( 'Hero', 'anna-baylis' ),
		'sections'    => __( 'Sections', 'anna-baylis' ),
		'content'     => __( 'Content', 'anna-baylis' ),
		'cta'         => __( 'CTA', 'anna-baylis' ),
		'footer'      => __( 'Footer', 'anna-baylis' ),
		'social'      => __( 'Social', 'anna-baylis' ),
		'animations'  => __( 'Animations', 'anna-baylis' ),
		'seo'         => __( 'SEO', 'anna-baylis' ),
	);
}

/**
 * Render the main settings page.
 */
function anna_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$tabs        = anna_get_settings_tabs();
	$active_tab  = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'brand';
	$social      = anna_get_social_links();
	?>
	<div class="wrap anna-admin-wrap">
		<h1 class="anna-admin-title">
			<span class="anna-admin-logo">✦</span>
			<?php esc_html_e( 'Anna Baylis — Theme Settings', 'anna-baylis' ); ?>
		</h1>

		<!-- Tabs -->
		<nav class="nav-tab-wrapper anna-admin-tabs" aria-label="<?php esc_attr_e( 'Settings tabs', 'anna-baylis' ); ?>">
			<?php foreach ( $tabs as $slug => $label ) : ?>
				<a href="?page=anna-theme-settings&tab=<?php echo esc_attr( $slug ); ?>" class="nav-tab <?php echo $active_tab === $slug ? 'nav-tab-active' : ''; ?>" aria-current="<?php echo $active_tab === $slug ? 'page' : 'false'; ?>">
					<?php echo esc_html( $label ); ?>
				</a>
			<?php endforeach; ?>
		</nav>

		<form method="post" action="options.php" class="anna-admin-form">
			<?php settings_fields( 'anna_theme_options_group' ); ?>

			<!-- Hidden field: tells the sanitizer which tab was submitted -->
			<input type="hidden" name="anna_theme_options[_anna_active_tab]" value="<?php echo esc_attr( $active_tab ); ?>">

			<table class="form-table anna-admin-table">

			<?php if ( 'brand' === $active_tab ) : ?>
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
				<?php anna_field_textarea( 'hero_heading', __( 'Main Heading', 'anna-baylis' ), __( 'HTML allowed: use &lt;em&gt; for gradient emphasis', 'anna-baylis' ) ); ?>
				<?php anna_field_textarea( 'hero_description', __( 'Description', 'anna-baylis' ) ); ?>
				<?php anna_field_text( 'hero_trust_text', __( 'Trust Text', 'anna-baylis' ) ); ?>
				<?php anna_field_media( 'hero_image_id', __( 'Hero Image', 'anna-baylis' ), __( 'Main portrait image shown on the right side of the hero section. Recommended: 800×1000px or larger.', 'anna-baylis' ) ); ?>
				<?php anna_field_heading( __( 'Statistics Cards', 'anna-baylis' ), __( 'Floating stat cards displayed over the hero image.', 'anna-baylis' ) ); ?>
				<?php anna_field_text( 'stat_1_value', __( 'Stat 1 Value', 'anna-baylis' ), '', 'text', '500+' ); ?>
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
				<?php anna_field_textarea( 'intro_quote', __( 'Pull Quote', 'anna-baylis' ) ); ?>
				<?php anna_field_text( 'intro_quote_cite', __( 'Quote Citation', 'anna-baylis' ) ); ?>
				<?php anna_field_media( 'intro_image_id', __( 'Intro Section Image', 'anna-baylis' ), __( 'Optional image for the intro section. Recommended: 600×800px.', 'anna-baylis' ) ); ?>

				<?php anna_field_heading( __( 'Recognition Section', 'anna-baylis' ) ); ?>
				<?php anna_field_text( 'recognition_eyebrow', __( 'Eyebrow', 'anna-baylis' ) ); ?>
				<?php anna_field_text( 'recognition_heading', __( 'Heading', 'anna-baylis' ) ); ?>
				<?php anna_field_textarea( 'recognition_description', __( 'Description', 'anna-baylis' ) ); ?>
				<?php anna_field_media( 'recognition_image_id', __( 'Recognition Section Image', 'anna-baylis' ), __( 'Optional background or accent image. Recommended: 600×600px.', 'anna-baylis' ) ); ?>

				<?php anna_field_heading( __( 'Services Section', 'anna-baylis' ) ); ?>
				<?php anna_field_text( 'services_eyebrow', __( 'Eyebrow', 'anna-baylis' ) ); ?>
				<?php anna_field_text( 'services_heading', __( 'Heading', 'anna-baylis' ) ); ?>
				<?php anna_field_textarea( 'services_description', __( 'Description', 'anna-baylis' ) ); ?>
				<?php anna_field_text( 'services_cta_text', __( 'CTA Button Text', 'anna-baylis' ) ); ?>
				<?php anna_field_text( 'services_cta_url', __( 'CTA Button URL', 'anna-baylis' ), '', 'url' ); ?>

				<?php anna_field_heading( __( 'About Section', 'anna-baylis' ) ); ?>
				<?php anna_field_text( 'about_eyebrow', __( 'Eyebrow', 'anna-baylis' ) ); ?>
				<?php anna_field_textarea( 'about_heading', __( 'Heading', 'anna-baylis' ), __( 'HTML allowed: use &lt;em&gt; for emphasis', 'anna-baylis' ) ); ?>
				<?php anna_field_textarea( 'about_body', __( 'Body Text', 'anna-baylis' ), __( 'HTML allowed: paragraphs with &lt;p&gt; tags', 'anna-baylis' ), 6 ); ?>
				<?php anna_field_media( 'about_image_id', __( 'About Portrait Image', 'anna-baylis' ), __( 'Portrait photo shown in the About section. Recommended: 800×1000px.', 'anna-baylis' ) ); ?>
				<?php anna_field_text( 'about_badge_number', __( 'Experience Badge Number', 'anna-baylis' ), __( 'e.g. "12+" — shown in the floating badge on the image', 'anna-baylis' ), 'text', '12+' ); ?>
				<?php anna_field_text( 'about_badge_text', __( 'Experience Badge Text', 'anna-baylis' ), '', 'text', 'Years Experience' ); ?>
				<?php anna_field_textarea( 'about_quote', __( 'Pull Quote', 'anna-baylis' ) ); ?>
				<?php anna_field_text( 'about_cta_text', __( 'CTA Button Text', 'anna-baylis' ) ); ?>
				<?php anna_field_text( 'about_cta_url', __( 'CTA Button URL', 'anna-baylis' ), '', 'url' ); ?>

				<?php anna_field_heading( __( 'Testimonials Section', 'anna-baylis' ) ); ?>
				<?php anna_field_text( 'testimonials_eyebrow', __( 'Eyebrow', 'anna-baylis' ) ); ?>
				<?php anna_field_text( 'testimonials_heading', __( 'Heading', 'anna-baylis' ) ); ?>

			<?php elseif ( 'cta' === $active_tab ) : ?>
				<?php anna_field_heading( __( 'Final CTA Section', 'anna-baylis' ) ); ?>
				<?php anna_field_text( 'cta_eyebrow', __( 'Eyebrow', 'anna-baylis' ) ); ?>
				<?php anna_field_text( 'cta_heading', __( 'Heading', 'anna-baylis' ) ); ?>
				<?php anna_field_textarea( 'cta_description', __( 'Description', 'anna-baylis' ) ); ?>
				<?php anna_field_text( 'cta_trust', __( 'Trust Text', 'anna-baylis' ) ); ?>
				<?php anna_field_media( 'cta_image_id', __( 'CTA Background Image', 'anna-baylis' ), __( 'Optional background image for the CTA section. Will overlay the gradient.', 'anna-baylis' ) ); ?>
				<?php anna_field_heading( __( 'Primary CTA Button', 'anna-baylis' ) ); ?>
				<?php anna_field_text( 'cta_primary_text', __( 'Button Text', 'anna-baylis' ) ); ?>
				<?php anna_field_text( 'cta_primary_url', __( 'Button URL', 'anna-baylis' ), '', 'url' ); ?>
				<?php anna_field_heading( __( 'Secondary CTA Button', 'anna-baylis' ) ); ?>
				<?php anna_field_text( 'cta_secondary_text', __( 'Button Text', 'anna-baylis' ) ); ?>
				<?php anna_field_text( 'cta_secondary_url', __( 'Button URL', 'anna-baylis' ), '', 'url' ); ?>

			<?php elseif ( 'footer' === $active_tab ) : ?>
				<?php anna_field_heading( __( 'Footer Content', 'anna-baylis' ) ); ?>
				<?php anna_field_textarea( 'footer_description', __( 'Brand Description', 'anna-baylis' ) ); ?>
				<?php anna_field_text( 'contact_email', __( 'Email', 'anna-baylis' ), '', 'email' ); ?>
				<?php anna_field_text( 'contact_phone', __( 'Phone', 'anna-baylis' ) ); ?>
				<?php anna_field_text( 'contact_address', __( 'Address', 'anna-baylis' ) ); ?>
				<?php anna_field_textarea( 'newsletter_text', __( 'Newsletter Description', 'anna-baylis' ) ); ?>
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
				<?php anna_field_text( 'seo_default_title_suffix', __( 'Title Suffix', 'anna-baylis' ), __( 'Appended to page titles, e.g. " | Anna Baylis"', 'anna-baylis' ) ); ?>
				<?php anna_field_textarea( 'seo_default_description', __( 'Default Meta Description', 'anna-baylis' ) ); ?>
				<?php anna_field_media( 'seo_og_image_id', __( 'Default OG Image', 'anna-baylis' ), __( 'Used when pages don\'t have a featured image.', 'anna-baylis' ) ); ?>

			<?php endif; ?>

			</table>

			<?php submit_button( __( 'Save Settings', 'anna-baylis' ) ); ?>

		</form>
	</div>
	<?php
}
