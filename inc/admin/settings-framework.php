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
 * Seed About Page defaults into existing saved options.
 *
 * WordPress will not automatically backfill new option keys if the option row
 * already exists. This ensures the About Page admin fields show the design
 * defaults instead of appearing blank.
 */
function anna_seed_about_page_defaults() {
	if ( ! is_admin() ) {
		return;
	}

	$defaults = anna_get_default_options();
	$options  = get_option( 'anna_theme_options', array() );

	if ( ! is_array( $options ) ) {
		$options = array();
	}

	$about_keys = array(
		'about_pg_hero_eyebrow',
		'about_pg_hero_heading',
		'about_pg_hero_subheading',
		'about_pg_hero_description',
		'about_pg_hero_tags_text',
		'about_pg_hero_image_id',
		'about_pg_story_eyebrow',
		'about_pg_story_heading',
		'about_pg_story_body',
		'about_pg_story_image_id',
		'about_pg_rock_heading',
		'about_pg_rock_left_body',
		'about_pg_rock_right_body',
		'about_pg_coach_eyebrow',
		'about_pg_coach_title',
		'about_pg_coach_body',
		'about_pg_coach_button_text',
		'about_pg_coach_button_url',
		'about_pg_coach_image_id',
		'about_pg_work_eyebrow',
		'about_pg_work_heading',
		'about_pg_work_body',
		'about_pg_work_card_1_title',
		'about_pg_work_card_1_body',
		'about_pg_work_card_2_title',
		'about_pg_work_card_2_body',
		'about_pg_work_card_3_title',
		'about_pg_work_card_3_body',
		'about_pg_work_card_4_title',
		'about_pg_work_card_4_body',
		'about_pg_qual_heading',
		'about_pg_qual_intro',
		'about_pg_qual_items_text',
		'about_pg_life_eyebrow',
		'about_pg_life_heading',
		'about_pg_life_body',
		'about_pg_life_image_id',
	);

	$changed = false;
	foreach ( $about_keys as $key ) {
		$has_value = isset( $options[ $key ] ) && '' !== trim( (string) $options[ $key ] );
		if ( ! $has_value && isset( $defaults[ $key ] ) && '' !== (string) $defaults[ $key ] ) {
			$options[ $key ] = $defaults[ $key ];
			$changed         = true;
		}
	}

	if ( $changed ) {
		update_option( 'anna_theme_options', $options );
	}
}
add_action( 'admin_init', 'anna_seed_about_page_defaults', 20 );

/**
 * Seed the WordPress "About" page editor content (post_content).
 *
 * The About template renders dynamic fields, so the page editor can look empty.
 * This fills the editor with the design copy for convenience, but only when the
 * page content is currently empty.
 */
function anna_seed_about_page_post_content() {
	if ( ! is_admin() ) {
		return;
	}

	$about_page_id = 0;

	// Prefer the page assigned the About template.
	$about_query = new WP_Query(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => 1,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'page-about.php',
			'fields'         => 'ids',
		)
	);

	if ( ! empty( $about_query->posts[0] ) ) {
		$about_page_id = (int) $about_query->posts[0];
	} else {
		// Fallback to common slugs/titles.
		$page = get_page_by_path( 'about' );
		if ( ! $page ) {
			$page = get_page_by_path( 'about-us' );
		}
		if ( $page instanceof WP_Post ) {
			$about_page_id = (int) $page->ID;
		}
	}

	if ( ! $about_page_id ) {
		return;
	}

	$current = get_post( $about_page_id );
	if ( ! ( $current instanceof WP_Post ) ) {
		return;
	}

	if ( '' !== trim( (string) $current->post_content ) ) {
		return;
	}

	$defaults = anna_get_default_options();

	$hero_eyebrow     = $defaults['about_pg_hero_eyebrow'] ?? 'About Anna';
	$hero_heading     = $defaults['about_pg_hero_heading'] ?? "I'm Anna.";
	$hero_subheading  = $defaults['about_pg_hero_subheading'] ?? 'Life Coach. Motivational Speaker. Olympian.';
	$hero_description = $defaults['about_pg_hero_description'] ?? '';

	$story_heading = $defaults['about_pg_story_heading'] ?? 'My story the beginning';
	$story_body    = $defaults['about_pg_story_body'] ?? '';

	$rock_heading    = $defaults['about_pg_rock_heading'] ?? 'My rock bottom';
	$rock_left_body  = $defaults['about_pg_rock_left_body'] ?? '';
	$rock_right_body = $defaults['about_pg_rock_right_body'] ?? '';

	$coach_heading    = wp_strip_all_tags( $defaults['about_pg_coach_heading'] ?? 'How I became a coach' );
	$coach_left_body  = $defaults['about_pg_coach_left_body'] ?? '';
	$coach_right_body = $defaults['about_pg_coach_right_body'] ?? '';
	$coach_quote      = $defaults['about_pg_coach_quote'] ?? '';

	$approach_eyebrow   = $defaults['about_pg_approach_eyebrow'] ?? 'My Approach';
	$approach_heading   = $defaults['about_pg_approach_heading'] ?? 'Different to most talk therapies';
	$approach_left_body = $defaults['about_pg_approach_left_body'] ?? '';
	$approach_right_body = $defaults['about_pg_approach_right_body'] ?? '';

	$qual_heading = $defaults['about_pg_qual_heading'] ?? 'My qualifications';
	$qual_intro   = $defaults['about_pg_qual_intro'] ?? '';
	$qual_items   = isset( $defaults['about_pg_qual_items_text'] ) ? preg_split( '/\r\n|\r|\n/', $defaults['about_pg_qual_items_text'] ) : array();
	$qual_items   = array_values( array_filter( array_map( 'trim', (array) $qual_items ) ) );

	$life_eyebrow = $defaults['about_pg_life_eyebrow'] ?? 'Present Day';
	$life_heading = $defaults['about_pg_life_heading'] ?? 'My life now';
	$life_body    = $defaults['about_pg_life_body'] ?? '';

	// Build simple HTML that Gutenberg will treat as a single "Custom HTML" block.
	$content  = '';
	$content .= '<h2>' . esc_html( strtoupper( $hero_eyebrow ) ) . '</h2>';
	$content .= '<h1>' . esc_html( $hero_heading ) . '</h1>';
	$content .= '<p><strong>' . esc_html( $hero_subheading ) . '</strong></p>';
	if ( $hero_description ) {
		$content .= '<p>' . esc_html( $hero_description ) . '</p>';
	}

	$content .= '<hr />';
	$content .= '<h2>' . esc_html( $story_heading ) . '</h2>';
	$content .= wpautop( esc_html( $story_body ) );

	$content .= '<hr />';
	$content .= '<h2>' . esc_html( $rock_heading ) . '</h2>';
	$content .= '<h3>Left column</h3>';
	$content .= wpautop( esc_html( $rock_left_body ) );
	$content .= '<h3>Right column</h3>';
	$content .= wpautop( esc_html( $rock_right_body ) );

	$content .= '<hr />';
	$content .= '<h2>' . esc_html( $coach_heading ) . '</h2>';
	$content .= '<h3>Left column</h3>';
	$content .= wpautop( esc_html( $coach_left_body ) );
	$content .= '<h3>Right column</h3>';
	$content .= wpautop( esc_html( $coach_right_body ) );
	if ( $coach_quote ) {
		$content .= '<blockquote><p>' . esc_html( $coach_quote ) . '</p></blockquote>';
	}

	$content .= '<hr />';
	$content .= '<h2>' . esc_html( $approach_eyebrow ) . '</h2>';
	$content .= '<h2>' . esc_html( $approach_heading ) . '</h2>';
	$content .= '<h3>Left column</h3>';
	$content .= wpautop( esc_html( $approach_left_body ) );
	$content .= '<h3>Right column</h3>';
	$content .= wpautop( esc_html( $approach_right_body ) );

	$content .= '<hr />';
	$content .= '<h2>' . esc_html( $qual_heading ) . '</h2>';
	if ( $qual_intro ) {
		$content .= '<p>' . esc_html( $qual_intro ) . '</p>';
	}
	if ( ! empty( $qual_items ) ) {
		$content .= '<ul>';
		foreach ( $qual_items as $item ) {
			$content .= '<li>' . esc_html( $item ) . '</li>';
		}
		$content .= '</ul>';
	}

	$content .= '<hr />';
	$content .= '<h2>' . esc_html( $life_eyebrow ) . '</h2>';
	$content .= '<h2>' . esc_html( $life_heading ) . '</h2>';
	$content .= wpautop( esc_html( $life_body ) );

	// Update the page content once.
	wp_update_post(
		array(
			'ID'           => $about_page_id,
			'post_content' => $content,
		)
	);
}
add_action( 'admin_init', 'anna_seed_about_page_post_content', 25 );

/**
 * One-time migration: refresh About qualifications/life defaults in saved options.
 *
 * Applies new section copy from the latest design images.
 */
function anna_migrate_about_page_copy_20260527() {
	if ( ! is_admin() ) {
		return;
	}

	$done_flag = get_option( 'anna_about_copy_migrated_20260527', false );
	if ( $done_flag ) {
		return;
	}

	$options  = get_option( 'anna_theme_options', array() );
	$defaults = anna_get_default_options();

	if ( ! is_array( $options ) ) {
		$options = array();
	}

	$old_qual_items = "Olympian and Commonwealth Games representative\nHawaii Ironman finisher\nNLP Practitioner\nTimeline Therapy Practitioner\nInternal Family Systems (IFS) trained\nTrauma-informed practitioner\nSomatic psychology\nCompassionate Inquiry (Gabor Maté)\nCertified Hypnotherapist\nInner Child work";
	$old_life_body  = "I live in Melbourne, coaching clients in person across south-east Melbourne and online throughout Australia and worldwide.\n\nThese days my work, my son and the outdoors fill my life. I still train — because movement is how I think, process and stay grounded. But the drive is different now. It's not about proving anything. It's about living fully, and helping others do the same.";

	$qual_current = isset( $options['about_pg_qual_items_text'] ) ? (string) $options['about_pg_qual_items_text'] : '';
	$life_current = isset( $options['about_pg_life_body'] ) ? (string) $options['about_pg_life_body'] : '';

	$changed = false;
	if ( '' === trim( $qual_current ) || $qual_current === $old_qual_items ) {
		$options['about_pg_qual_items_text'] = (string) ( $defaults['about_pg_qual_items_text'] ?? '' );
		$changed = true;
	}

	if ( '' === trim( $life_current ) || $life_current === $old_life_body ) {
		$options['about_pg_life_body'] = (string) ( $defaults['about_pg_life_body'] ?? '' );
		$changed = true;
	}

	if ( $changed ) {
		update_option( 'anna_theme_options', $options );
	}

	update_option( 'anna_about_copy_migrated_20260527', 1 );
}
add_action( 'admin_init', 'anna_migrate_about_page_copy_20260527', 30 );

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
		'about_pg_hero_heading'       => "Hi, I'm Anna.\nI became the coach\nI am because of\nwhat I've lived through.",
		'about_pg_hero_subheading'    => '',
		'about_pg_hero_description'   => '',
		'about_pg_hero_tags_text'     => "Olympian\nHawaii Ironman\nIFS Trained\nSomatic Psychology\nTrauma-Informed",
		'about_pg_hero_image_id'      => '',
		'about_pg_story_eyebrow'      => 'About Anna',
		'about_pg_story_heading'      => 'My story the beginning',
		'about_pg_story_body'         => "My childhood gave me roots to grow and wings to fly. I always knew I was going to be an athlete. It was just a matter of finding my sport.\n\nOver two decades competing as an elite mountain biker and Ironwoman, I developed a deep awareness of what the body needs to perform. I learned to nourish it, train it, rest it. I represented Australia at the Olympics and Commonwealth Games. I finished the Hawaii Ironman.\n\nBut I also learned something that no amount of physical training could teach me. Success isn't just about the body. It's about the mind. The emotions. The energy you bring to every single day.",
		'about_pg_story_image_id'     => '',
		'about_pg_rock_heading'       => 'My rock bottom',
		'about_pg_rock_left_body'     => "I had achieved everything I had dreamed of as an athlete. And then my life fell apart in a way I never could have anticipated.\n\nI was completely shattered. Sixteen thousand kilometres from my family in Australia. Four months pregnant.\n\nWhat followed was the most painful and ultimately the most transformative chapter of my life. I had to rebuild everything — my finances, my identity, my sense of self. And in doing so, I found my purpose.",
		'about_pg_rock_right_body'    => "After ten years of marriage, and with a baby on the way, I had to make the hardest — and ultimately the best — decision of my life. My then husband, best friend and father-to-be did the unfathomable and betrayed both my son and myself.\n\nI walked away from my life in Germany — from everything I had built over twelve years. With my eight-month-old son, a suitcase of his clothes, and $2000. I returned home to Australia to start again.",
		'about_pg_coach_eyebrow'      => 'How I Became a Coach',
		'about_pg_coach_title'        => 'A defining moment that changed everything.',
		'about_pg_coach_body'         => "My coaching journey began in 2017 when a personal training client of mine opened my eyes to the possibility of helping others through trauma and challenging life experiences. That moment ignited something in me.\n\nI began studying. And I have not stopped.\n\nOver the past seven years I have immersed myself in the deepest, most rigorous training I could find NLP and Timeline Therapy to Internal Family Systems, somatic psychology, trauma-informed practice, and Compassionate Inquiry under Gabor Maté.\n\nI did this not just to become a better coach. I did it to heal my own wounds. To understand my own programs. To come back to myself fully, so I could help others do the same.",
		'about_pg_coach_button_text'  => 'Book a Discovery Call',
		'about_pg_coach_button_url'   => '#contact',
		'about_pg_coach_image_id'     => '',

		'about_pg_work_eyebrow'       => 'How I work',
		'about_pg_work_heading'       => 'Different to most talk therapies.',
		'about_pg_work_body'          => "My approach is unlike most talk therapies. I work bottom-up through the body, the nervous system, to access the subconscious to get to the root of what is actually running underneath behaviour.\n\nThe framework I use with every client is Awareness, Acceptance and Action. We start by seeing clearly what is actually happening, in the thoughts, the body showing up as sensations in the body which we label as emotions, the behavioural patterns. We move through acceptance, not resignation, but the honest acknowledgment of what is here. And then we take aligned action. Not forced. Not pushed. Rooted\n\nI draw on parts work, the understanding that we are not one unified self but a collection of parts, each with its own needs and protective roles. When we can meet those parts with compassion rather than criticism, everything shifts.\n\nI also bring in the body. Movement. Breath. Nutrition. The nervous system. Because lasting change is not just cognitive, it is somatic. It lives in the body.",
		'about_pg_work_card_1_title'  => 'Bottom-up approach',
		'about_pg_work_card_1_body'   => 'Rather than talking about problems from the top down, I work through the body and the subconscious where the real programs live. This creates change that is deep and lasting, not just intellectual.',
		'about_pg_work_card_2_title'  => 'Trauma-informed and safe',
		'about_pg_work_card_2_body'   => 'Every session is held in a safe, non-judgmental space. I work gently and carefully with whatever is present never pushing, always honoring where you are.',
		'about_pg_work_card_3_title'  => 'Whole person body, mind and emotions',
		'about_pg_work_card_3_body'   => "I draw on movement, nutrition, mindset, emotional awareness, parts work and somatic practice. True wellbeing isn't one thing it's the integration of all of them.",
		'about_pg_work_card_4_title'  => 'Lived experience',
		'about_pg_work_card_4_body'   => "I don't coach from a textbook. I coach from a life that has included elite sport, personal devastation, and genuine rebuilding. \"I understand what it takes to heal trauma and have it stored as wisdom\" I practice what I preach.",

		'about_pg_qual_heading'       => 'My qualifications',
		'about_pg_qual_intro'         => 'I am committed to ongoing learning and hold qualifications in:',
		'about_pg_qual_items_text'    => "Bachelor of Applied Science in Human Movement — Deakin University\nCredentialled Practitioner of Coaching — The Coaching Institute\nNLP Practitioner and Coach — Institute of Empowered Psychology\nHypnotherapist — Institute of Empowered Psychology\nInner Child Work and Internal Family Systems — Embodied Philosophy Wisdom School\nCompassionate Inquiry in Action — Gabor Maté\nChakra Therapy — Awakening and Healing the Energy Body — Anodea Judith\nHonours in Food Science and Nutrition — Deakin University\nEmotional Intimacy Coach — The Coaching Institute\nTimeline Therapy Practitioner — Institute of Empowered Psychology\nTrauma-Informed Coach — The Centre for Healing\nIntroduction to Somatic Psychology and Embodied Communication — Neuroaffective Touch Institute\nComplete IFS Therapy Immersion — Integrating Internal Family Systems Across Clinical Applications",
		'about_pg_life_eyebrow'       => 'Present Day',
		'about_pg_life_heading'       => 'My life now',
		'about_pg_life_body'          => "I live and work in Melbourne, Australia. On any given day you'll find me outdoors — walking, moving, appreciating nature. Meditation, gratitude and movement are not things I recommend to clients from a distance. They are how I live.\n\nI coach men, women and couples both in person in Melbourne and online. I speak at events and conferences across Australia. And I am the founder of Oasis — a women's community for sustainable health and wellbeing that brings everything I've learned into an ongoing, accessible space for women who are ready to come back to themselves.",
		'about_pg_life_image_id'      => '',
	);
}
