<?php
/**
 * Admin Settings Framework
 *
 * Core settings API abstraction. Registers the options,
 * provides field rendering helpers, and manages settings storage.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register all theme settings.
 */
function anna_register_settings() {
	register_setting(
		'anna_theme_options_group',
		'anna_theme_options',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'anna_sanitize_options',
			'default'           => anna_get_default_options(),
		)
	);
}
add_action( 'admin_init', 'anna_register_settings' );

/**
 * Get default theme options.
 *
 * @return array
 */
function anna_get_default_options() {
	return array(
		// Brand
		'color_primary'        => '#007063',
		'color_accent'         => '#4CA591',
		'color_bg_soft'        => '#F2F6F2',
		'color_text'           => '#1A2B25',
		'color_heading'        => '#0F1F1B',

		// Typography
		'font_heading'         => "'Lexend', sans-serif",
		'font_body'            => "'Mulish', sans-serif",
		'font_size_base'       => '1rem',
		'font_weight_heading'  => '600',
		'font_weight_body'     => '400',

		// Layout
		'container_max'        => '1320px',
		'container_wide'       => '1440px',
		'section_padding_md'   => 'clamp(5rem, 8vw, 8rem)',

		// Buttons
		'border_radius_btn'    => '9999px',
		'btn_padding_x'        => '2rem',
		'btn_padding_y'        => '0.875rem',

		// Header
		'header_style'         => 'transparent',
		'header_cta_text'      => 'Book a Call',
		'header_cta_url'       => '#contact',

		// Hero
		'hero_eyebrow'         => 'Coaching & Wellness',
		'hero_heading'         => 'Transform Your Life with <em>Purposeful</em> Coaching',
		'hero_description'     => 'Discover clarity, confidence, and lasting transformation through personalised coaching designed to unlock your full potential.',
		'hero_trust_text'      => 'Trusted by 500+ clients worldwide',
		'hero_image_id'        => '',

		// Intro section image
		'intro_image_id'       => '',

		// Recognition section image
		'recognition_image_id' => '',

		// Stats
		'stat_1_value'         => '500+',
		'stat_1_label'         => 'Clients Transformed',
		'stat_2_value'         => '12+',
		'stat_2_label'         => 'Years Experience',
		'stat_3_value'         => '98%',
		'stat_3_label'         => 'Client Satisfaction',

		// Intro
		'intro_eyebrow'        => 'My Approach',
		'intro_quote'          => 'True transformation begins when you choose to invest in yourself — not someday, but today.',
		'intro_quote_cite'     => 'Anna Baylis',
		'intro_body'           => '',

		// Recognition
		'recognition_eyebrow'    => 'Recognise Yourself?',
		'recognition_heading'    => 'Does This Sound Like You?',
		'recognition_description' => '',

		// Services
		'services_eyebrow'     => 'Services',
		'services_heading'     => 'How I Can Help You',
		'services_description' => 'Tailored programs designed to help you thrive in every dimension of your life.',
		'services_cta_text'    => 'View All Services',
		'services_cta_url'     => '#',

		// About
		'about_eyebrow'        => 'About Me',
		'about_heading'        => 'Meet <em>Anna Baylis</em>',
		'about_body'           => '',
		'about_quote'          => 'I believe everyone has the innate capacity for extraordinary growth.',
		'about_image_id'       => '',
		'about_badge_number'   => '12+',
		'about_badge_text'     => 'Years Experience',
		'about_cta_text'       => 'More About Me',
		'about_cta_url'        => '#',

		// Testimonials
		'testimonials_eyebrow' => 'Testimonials',
		'testimonials_heading' => 'What My Clients Say',

		// CTA
		'cta_eyebrow'          => "Let's Connect",
		'cta_heading'          => 'Ready to Begin Your Transformation?',
		'cta_description'      => "Take the first step toward the life you've always envisioned.",
		'cta_trust'            => 'Free discovery call · No obligations · 100% confidential',
		'cta_primary_text'     => 'Book a Discovery Call',
		'cta_primary_url'      => '#',
		'cta_secondary_text'   => 'Learn More',
		'cta_secondary_url'    => '#',
		'cta_image_id'         => '',

		// Footer
		'footer_description'   => 'Empowering individuals to discover their true potential through transformative coaching.',
		'contact_email'        => 'hello@annabaylis.com',
		'contact_phone'        => '',
		'contact_address'      => '',
		'newsletter_text'      => 'Subscribe for insights, tips, and updates.',
		'copyright_text'       => '',
		'privacy_url'          => '#',
		'terms_url'            => '#',

		// Social links
		'social_links'         => array(
			'instagram' => '',
			'facebook'  => '',
			'linkedin'  => '',
			'twitter'   => '',
			'youtube'   => '',
			'tiktok'    => '',
		),

		// Animations
		'animations_enabled'   => true,
		'animation_speed'      => 'normal',

		// Section toggles
		'section_hero_enabled'          => true,
		'section_intro_enabled'         => true,
		'section_recognition_enabled'   => true,
		'section_services_enabled'      => true,
		'section_about_enabled'         => true,
		'section_testimonials_enabled'  => true,
		'section_cta_enabled'           => true,

		// SEO
		'seo_default_title_suffix' => '',
		'seo_default_description'  => '',
		'seo_og_image_id'         => '',
	);
}
