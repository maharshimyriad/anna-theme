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
		'site_logo_id'        => '',
		'color_primary'       => '#007063',
		'color_accent'        => '#4CA591',
		'color_bg_soft'       => '#F2F6F2',
		'color_text'          => '#1A2B25',
		'color_heading'       => '#0F1F1B',
		'font_heading'        => "'Lexend', sans-serif",
		'font_body'           => "'Mulish', sans-serif",
		'font_size_base'      => '1rem',
		'font_weight_heading' => '600',
		'font_weight_body'    => '400',
		'container_max'       => '1320px',
		'container_wide'      => '1440px',
		'section_padding_md'  => 'clamp(5rem, 8vw, 8rem)',
		'border_radius_btn'   => '9999px',
		'header_style'        => 'transparent',
		'header_cta_text'     => 'Book a Discovery Call',
		'header_cta_url'      => '#final-cta',
		'hero_eyebrow'        => 'Life Coach · motivational Speaker · Olympian · Melbourne',
		'hero_heading'        => "You know what to do. <br>You're just not doing it.",
		'hero_description'    => 'I help people understand why change has felt so hard and create the conditions for change that actually lasts. Not through willpower. Through understanding what has been running underneath.',
		'hero_trust_text'     => '',
		'hero_image_id'       => '',
		'intro_image_id'      => '',
		'recognition_image_id' => '',
		'stat_1_value'        => '102',
		'stat_1_label'        => 'Five-star Google reviews',
		'stat_2_value'        => '7+',
		'stat_2_label'        => 'Years coaching clients',
		'stat_3_value'        => 'AUS',
		'stat_3_label'        => 'Olympian and Ironman finisher',
		'intro_eyebrow'       => 'My Approach',
		'intro_heading'       => 'Real change. From the inside out.',
		'intro_quote'         => "If you don't know, you don't know. But once you do, everything becomes possible.",
		'intro_quote_cite'    => 'Anna Baylis',
		'intro_body'          => "<p>Most people who find me have already tried. They've done the therapy, read the books, set the goals. Something keeps getting in the way.</p><p>That something lives in the subconscious. In the programs and patterns formed long before you had any say in them. My work is to help you see those programs and then change them. Not through willpower. Through understanding.</p>",
		'recognition_eyebrow' => '',
		'recognition_heading' => 'You might recognise yourself here',
		'recognition_description' => '',
		'recognition_items_text' => "You feel stuck, disconnected or like you're going through the motions\nYou know what you need to do but you're not doing it\nYou've tried therapy, programs and self-help and something still feels missing\nYou put everyone else first and run on empty\nYou sense there's more available to you but don't know how to access it\nYou want to feel genuinely well, not just functional",
		'services_eyebrow'    => 'How we can work together',
		'services_heading'    => "What's the change you're needing?",
		'services_description' => '',
		'services_cta_text'   => '',
		'services_cta_url'    => '#',
		'about_eyebrow'       => 'About Anna',
		'about_heading'       => 'Olympian. Life Coach. Motivational Speaker.',
		'about_body'          => "<p>I became the coach I am because of what I've lived through, not in spite of it. From two decades of elite sport to my own rock bottom and back, I understand real transformation from the inside out.</p><p>My approach is different to most coaches. I work bottom-up, through the body, through the subconscious, helping clients understand the programs that have been running underneath their thoughts, feelings and behaviour for years.</p>",
		'about_quote'         => "That's not theory. That's my life.",
		'about_image_id'      => '',
		'about_badge_number'  => '',
		'about_badge_text'    => '',
		'about_expertise_text' => "Olympian\nHawaii Ironman\nNLP Practitioner\nIFS Trained\nTrauma-Informed\nSomatic Psychology\nCompassionate Inquiry\nHypnotherapist\nTimeline Therapy\nInner Child Work",
		'about_cta_text'      => '',
		'about_cta_url'       => '#',
		'testimonials_eyebrow' => 'What clients say',
		'testimonials_heading' => '102 five-star Google reviews',
		'testimonials_summary' => '5.0 · Google Reviews · Australia and worldwide',
		'testimonials_cta_text' => 'Read all reviews',
		'testimonials_cta_url' => '#',
		'cta_eyebrow'         => 'Ready to begin?',
		'cta_heading'         => 'By connecting mindset, emotional health and physical wellbeing',
		'cta_description'     => 'you can move towards your most fulfilling life.',
		'cta_trust'           => "Start with a complimentary discovery call. No obligation, just a conversation to see if we're the right fit.",
		'cta_primary_text'    => 'Book a Discovery Call',
		'cta_primary_url'     => '#contact',
		'cta_secondary_text'  => '',
		'cta_secondary_url'   => '#',
		'cta_image_id'        => '',
		'footer_description'  => "Trauma-informed life coaching, speaking and community for people ready to create lasting change from the inside out.\n\nBased in Melbourne, Australia.",
		'contact_email'       => 'info@annabaylis.com.au',
		'contact_phone'       => '0486 082 013',
		'contact_address'     => "In person - South East Melbourne\nOnline - Australia and worldwide",
		'contact_hours'       => 'Mon-Fri · 9:00 AM - 5:00 PM',
		'newsletter_text'     => '',
		'newsletter_heading'  => 'Newsletter',
		'newsletter_name_placeholder' => 'Name',
		'newsletter_email_placeholder' => 'Email',
		'newsletter_button_text' => 'Subscribe',
		'copyright_text'      => '',
		'privacy_url'         => '#',
		'terms_url'           => '#',
		'social_links'        => array(
			'instagram' => '',
			'facebook'  => '',
			'linkedin'  => '',
			'twitter'   => '',
			'youtube'   => '',
			'tiktok'    => '',
		),
		'animations_enabled'  => true,
		'animation_speed'     => 'normal',
		'section_hero_enabled'         => true,
		'section_intro_enabled'        => true,
		'section_recognition_enabled'  => true,
		'section_services_enabled'     => true,
		'section_about_enabled'        => true,
		'section_testimonials_enabled' => true,
		'section_cta_enabled'          => true,
		'seo_default_title_suffix' => '',
		'seo_default_description'  => '',
		'seo_og_image_id'         => '',
	);
}
