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

		// About page (template: page-about.php).
		'about_pg_hero_eyebrow'       => 'About Anna',
		'about_pg_hero_heading'       => "I'm Anna.",
		'about_pg_hero_subheading'    => 'Life Coach. Motivational Speaker. Olympian.',
		'about_pg_hero_description'   => 'And I became the coach I am because of what I have lived through — not in spite of it.',
		'about_pg_hero_image_id'      => '',
		'about_pg_story_eyebrow'      => 'About Anna',
		'about_pg_story_heading'      => 'My story the beginning',
		'about_pg_story_body'         => "My childhood gave me roots to grow and wings to fly. I always knew I was going to be an athlete. It was just a matter of finding my sport.\n\nOver two decades competing as an elite mountain biker and Ironwoman, I developed a deep awareness of what the body needs to perform. I learned to nourish it, train it, rest it. I represented Australia at the Olympics and Commonwealth Games. I finished the Hawaii Ironman.\n\nBut I also learned something that no amount of physical training could teach me. Success isn't just about the body. It's about the mind. The emotions. The energy you bring to every single day.",
		'about_pg_story_image_id'     => '',
		'about_pg_rock_heading'       => 'My rock bottom',
		'about_pg_rock_left_body'     => "I had achieved everything I had dreamed of as an athlete. And then my life fell apart in a way I never could have anticipated.\n\nI was completely shattered. Sixteen thousand kilometres from my family in Australia. Four months pregnant.\n\nWhat followed was the most painful and ultimately the most transformative chapter of my life. I had to rebuild everything — my finances, my identity, my sense of self. And in doing so, I found my purpose.",
		'about_pg_rock_right_body'    => "After ten years of marriage, and with a baby on the way, I had to make the hardest — and ultimately the best — decision of my life. My then husband, best friend and father-to-be did the unfathomable and betrayed both my son and myself.\n\nI walked away from my life in Germany — from everything I had built over twelve years. With my eight-month-old son, a suitcase of his clothes, and $2000. I returned home to Australia to start again.",
		'about_pg_coach_heading'      => 'How I became <span class="anna-about-page__heading-accent">a coach</span>',
		'about_pg_coach_left_body'    => "My coaching journey began in 2017 when a personal training client opened my eyes to the possibility of helping others through trauma and challenging life experiences. That moment ignited something in me.\n\nI began studying. And I haven't stopped.\n\nOver the past seven years I have immersed myself in the deepest, most rigorous training I could find — from NLP and Timeline Therapy to Internal Family Systems, somatic psychology, trauma-informed practice and Compassionate Inquiry under Gabor Maté.",
		'about_pg_coach_right_body'   => "I did this not just to become a better coach. I did it to heal my own wounds. To understand my own programs. To come back to myself.\n\nI am the coach I am because of everything I have lived through. The trauma and the triumphs. I know what it feels like to be completely lost. And I know — without any doubt — that it is possible to create a life with genuine happiness and joy on the other side of it.",
		'about_pg_coach_quote'        => "That's not theory. That's my life.",
		'about_pg_approach_eyebrow'   => 'My Approach',
		'about_pg_approach_heading'   => 'Different to most talk therapies',
		'about_pg_approach_intro'     => '',
		'about_pg_approach_left_body' => "My work is different to most talk therapies.\n\nI use a bottom-up approach — working through the body to access the subconscious mind. Because real change doesn't start with thinking differently. It starts with understanding the programs that have been running underneath your thoughts, behaviours and emotions — often since childhood.\n\nWhen you understand those programs, you have a choice. And that choice is everything.",
		'about_pg_approach_right_body'=> 'I draw on neuroscience, somatic awareness, NLP, IFS parts work, trauma-informed practice and the wisdom of the body to help my clients create change that is deep, lasting and genuinely theirs.',
		'about_pg_qual_heading'       => 'My qualifications',
		'about_pg_qual_intro'         => 'I am committed to ongoing learning and hold qualifications in:',
		'about_pg_qual_items_text'    => "Olympian and Commonwealth Games representative\nHawaii Ironman finisher\nNLP Practitioner\nTimeline Therapy Practitioner\nInternal Family Systems (IFS) trained\nTrauma-informed practitioner\nSomatic psychology\nCompassionate Inquiry (Gabor Maté)\nCertified Hypnotherapist\nInner Child work",
		'about_pg_life_eyebrow'       => 'Present Day',
		'about_pg_life_heading'       => 'My life now',
		'about_pg_life_body'          => "I live in Melbourne, coaching clients in person across south-east Melbourne and online throughout Australia and worldwide.\n\nThese days my work, my son and the outdoors fill my life. I still train — because movement is how I think, process and stay grounded. But the drive is different now. It's not about proving anything. It's about living fully, and helping others do the same.",
		'about_pg_life_image_id'      => '',
	);
}
