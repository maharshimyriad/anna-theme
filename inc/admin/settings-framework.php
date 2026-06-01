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
		'about_pg_people_eyebrow',
		'about_pg_people_heading',
		'about_pg_people_body',
		'about_pg_people_items',
		'about_pg_people_items_text',
		'about_pg_connect_eyebrow',
		'about_pg_connect_heading',
		'about_pg_connect_button_text',
		'about_pg_connect_button_url',
	);

	$changed = false;
	foreach ( $about_keys as $key ) {
		$has_value = false;
		if ( isset( $options[ $key ] ) ) {
			if ( is_array( $options[ $key ] ) ) {
				$has_value = ! empty( $options[ $key ] );
			} else {
				$has_value = '' !== trim( (string) $options[ $key ] );
			}
		}

		$default_has_value = false;
		if ( isset( $defaults[ $key ] ) ) {
			if ( is_array( $defaults[ $key ] ) ) {
				$default_has_value = ! empty( $defaults[ $key ] );
			} else {
				$default_has_value = '' !== (string) $defaults[ $key ];
			}
		}

		if ( ! $has_value && $default_has_value ) {
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

	$existing_content = (string) $current->post_content;
	$existing_trimmed = trim( $existing_content );

	// Update the editor content if it's empty OR was previously auto-seeded
	// with an older structure.
	$should_update = ( '' === $existing_trimmed );
	if ( ! $should_update ) {
		$markers = array(
			'anna:about-seed:',
			'<h3>Left column</h3>',
			'My qualifications',
			'about_pg_qual_',
		);
		foreach ( $markers as $marker ) {
			if ( false !== strpos( $existing_content, $marker ) ) {
				$should_update = true;
				break;
			}
		}
	}

	if ( ! $should_update ) {
		return;
	}

	$defaults = anna_get_default_options();

	$hero_eyebrow     = $defaults['about_pg_hero_eyebrow'] ?? 'About Anna';
	$hero_heading     = $defaults['about_pg_hero_heading'] ?? "Hi, I'm Anna.";
	$hero_subheading  = $defaults['about_pg_hero_subheading'] ?? '';
	$hero_description = $defaults['about_pg_hero_description'] ?? '';

	$story_heading = $defaults['about_pg_story_heading'] ?? 'My story the beginning';
	$story_body    = $defaults['about_pg_story_body'] ?? '';

	$rock_heading    = $defaults['about_pg_rock_heading'] ?? 'My rock bottom';
	$rock_left_body  = $defaults['about_pg_rock_left_body'] ?? '';
	$rock_right_body = $defaults['about_pg_rock_right_body'] ?? '';

	$coach_eyebrow     = $defaults['about_pg_coach_eyebrow'] ?? 'How I Became a Coach';
	$coach_title       = $defaults['about_pg_coach_title'] ?? '';
	$coach_body        = $defaults['about_pg_coach_body'] ?? '';
	$coach_button_text = $defaults['about_pg_coach_button_text'] ?? '';
	$coach_button_url  = $defaults['about_pg_coach_button_url'] ?? '';

	$work_eyebrow = $defaults['about_pg_work_eyebrow'] ?? 'How I work';
	$work_heading = $defaults['about_pg_work_heading'] ?? '';
	$work_body    = $defaults['about_pg_work_body'] ?? '';
	$work_cards   = array();
	for ( $i = 1; $i <= 4; $i++ ) {
		$work_cards[] = array(
			'title' => $defaults[ 'about_pg_work_card_' . $i . '_title' ] ?? '',
			'body'  => $defaults[ 'about_pg_work_card_' . $i . '_body' ] ?? '',
		);
	}

	$people_eyebrow = $defaults['about_pg_people_eyebrow'] ?? 'What people say';
	$people_heading = $defaults['about_pg_people_heading'] ?? '';
	$people_body    = $defaults['about_pg_people_body'] ?? '';
	$people_items   = isset( $defaults['about_pg_people_items_text'] ) ? preg_split( '/\r\n|\r|\n/', $defaults['about_pg_people_items_text'] ) : array();
	$people_items   = array_values( array_filter( array_map( 'trim', (array) $people_items ) ) );

	$connect_eyebrow     = $defaults['about_pg_connect_eyebrow'] ?? 'If something in my story resonates';
	$connect_heading     = $defaults['about_pg_connect_heading'] ?? '';
	$connect_button_text = $defaults['about_pg_connect_button_text'] ?? '';
	$connect_button_url  = $defaults['about_pg_connect_button_url'] ?? '';

	// Build simple HTML that Gutenberg will treat as a single "Custom HTML" block.
	$content  = "<!-- anna:about-seed:v2 -->\n";
	$content .= '<h2>' . esc_html( $hero_eyebrow ) . '</h2>';
	$content .= '<h1>' . esc_html( $hero_heading ) . '</h1>';
	if ( $hero_subheading ) {
		$content .= '<p><strong>' . esc_html( $hero_subheading ) . '</strong></p>';
	}
	if ( $hero_description ) {
		$content .= '<p>' . esc_html( $hero_description ) . '</p>';
	}

	$content .= '<hr />';
	$content .= '<h2>' . esc_html( $story_heading ) . '</h2>';
	$content .= wpautop( esc_html( $story_body ) );

	$content .= '<hr />';
	$content .= '<h2>' . esc_html( $rock_heading ) . '</h2>';
	$content .= wpautop( esc_html( $rock_left_body ) );
	$content .= wpautop( esc_html( $rock_right_body ) );

	$content .= '<hr />';
	$content .= '<h2>' . esc_html( $coach_eyebrow ) . '</h2>';
	$content .= '<h2>' . esc_html( $coach_title ) . '</h2>';
	$content .= wpautop( esc_html( $coach_body ) );
	if ( $coach_button_text && $coach_button_url ) {
		$content .= '<p><a href="' . esc_url( $coach_button_url ) . '">' . esc_html( $coach_button_text ) . '</a></p>';
	}

	$content .= '<hr />';
	$content .= '<h2>' . esc_html( $work_eyebrow ) . '</h2>';
	$content .= '<h2>' . esc_html( $work_heading ) . '</h2>';
	$content .= wpautop( esc_html( $work_body ) );
	foreach ( $work_cards as $card ) {
		if ( '' === trim( (string) $card['title'] ) && '' === trim( (string) $card['body'] ) ) {
			continue;
		}
		$content .= '<h3>' . esc_html( (string) $card['title'] ) . '</h3>';
		$content .= '<p>' . esc_html( (string) $card['body'] ) . '</p>';
	}

	$content .= '<hr />';
	$content .= '<h2>' . esc_html( $people_eyebrow ) . '</h2>';
	$content .= '<h2>' . esc_html( $people_heading ) . '</h2>';
	if ( $people_body ) {
		$content .= '<p>' . esc_html( $people_body ) . '</p>';
	}
	if ( ! empty( $people_items ) ) {
		$content .= '<ul>';
		foreach ( $people_items as $item ) {
			$content .= '<li>' . esc_html( $item ) . '</li>';
		}
		$content .= '</ul>';
	}

	$content .= '<hr />';
	$content .= '<h2>' . esc_html( $connect_eyebrow ) . '</h2>';
	$content .= '<h2>' . esc_html( $connect_heading ) . '</h2>';
	if ( $connect_button_text && $connect_button_url ) {
		$content .= '<p><a href="' . esc_url( $connect_button_url ) . '">' . esc_html( $connect_button_text ) . '</a></p>';
	}

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
function anna_migrate_about_page_copy_20260528() {
	if ( ! is_admin() ) {
		return;
	}

	$done_flag = get_option( 'anna_about_copy_migrated_20260528', false );
	if ( $done_flag ) {
		return;
	}

	$options  = get_option( 'anna_theme_options', array() );
	$defaults = anna_get_default_options();

	if ( ! is_array( $options ) ) {
		$options = array();
	}

	$people_items_current = $options['about_pg_people_items'] ?? array();
	$people_text_current    = isset( $options['about_pg_people_items_text'] ) ? (string) $options['about_pg_people_items_text'] : '';

	$changed = false;
	if ( ! is_array( $people_items_current ) || empty( $people_items_current ) ) {
		if ( '' !== trim( $people_text_current ) && function_exists( 'anna_parse_about_people_items' ) ) {
			$options['about_pg_people_items'] = anna_parse_about_people_items( $people_text_current );
		} else {
			$options['about_pg_people_items'] = $defaults['about_pg_people_items'] ?? array();
		}
		$options['about_pg_people_heading'] = (string) ( $defaults['about_pg_people_heading'] ?? '' );
		$options['about_pg_people_body']    = (string) ( $defaults['about_pg_people_body'] ?? '' );
		$options['about_pg_people_eyebrow'] = (string) ( $defaults['about_pg_people_eyebrow'] ?? '' );
		$changed                            = true;
	}

	if ( $changed ) {
		update_option( 'anna_theme_options', $options );
	}

	update_option( 'anna_about_copy_migrated_20260528', 1 );
}
add_action( 'admin_init', 'anna_migrate_about_page_copy_20260528', 30 );

/**
 * Get default theme options.
 *
 * @return array
 */
function anna_get_default_options() {
	$defaults = array(
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

		'about_pg_people_eyebrow'     => 'What people say',
		'about_pg_people_heading'     => 'Committed to continual learning.',
		'about_pg_people_body'        => 'Over a decade of rigorous study across human movement, nutrition, coaching, somatic psychology, trauma-informed practice and inner world work. This is what I bring to every session.',
		'about_pg_people_items_text'  => "HM|Bachelor of Applied Science — Human Movement|Deakin University\nCP|Credentialled Practitioner of Coaching|The Coaching Institute\nNLP|NLP Practitioner and Master Practitioner|Institute of Empowered Psychology\nHY|Hypnotherapy|Institute of Empowered Psychology\nIFS|Parts work — Internal Family Systems informed|Embodied Philosophy Western School\nCI|Masters — currently completing|Gabor Maté\nCT|102 five-star Google reviews|Anodea Judith\nNR|Honours — Food Science and Nutrition|Deakin University\nEI|Emotional Intimacy Coach|The Coaching Institute\nTL|Timeline Therapy|Institute of Empowered Psychology\nTC|Trauma-informed coaching|The Centre for Healing\nSP|Personal trainer — 7+ years|NeuroAffective Touch Institute",
		'about_pg_people_items'       => array(
			array( 'logo_id' => 0, 'initials' => 'HM', 'title' => 'Bachelor of Applied Science — Human Movement', 'org' => 'Deakin University' ),
			array( 'logo_id' => 0, 'initials' => 'CP', 'title' => 'Credentialled Practitioner of Coaching', 'org' => 'The Coaching Institute' ),
			array( 'logo_id' => 0, 'initials' => 'NLP', 'title' => 'NLP Practitioner and Master Practitioner', 'org' => 'Institute of Empowered Psychology' ),
			array( 'logo_id' => 0, 'initials' => 'HY', 'title' => 'Hypnotherapy', 'org' => 'Institute of Empowered Psychology' ),
			array( 'logo_id' => 0, 'initials' => 'IFS', 'title' => 'Parts work — Internal Family Systems informed', 'org' => 'Embodied Philosophy Western School' ),
			array( 'logo_id' => 0, 'initials' => 'CI', 'title' => 'Masters — currently completing', 'org' => 'Gabor Maté' ),
			array( 'logo_id' => 0, 'initials' => 'CT', 'title' => '102 five-star Google reviews', 'org' => 'Anodea Judith' ),
			array( 'logo_id' => 0, 'initials' => 'NR', 'title' => 'Honours — Food Science and Nutrition', 'org' => 'Deakin University' ),
			array( 'logo_id' => 0, 'initials' => 'EI', 'title' => 'Emotional Intimacy Coach', 'org' => 'The Coaching Institute' ),
			array( 'logo_id' => 0, 'initials' => 'TL', 'title' => 'Timeline Therapy', 'org' => 'Institute of Empowered Psychology' ),
			array( 'logo_id' => 0, 'initials' => 'TC', 'title' => 'Trauma-informed coaching', 'org' => 'The Centre for Healing' ),
			array( 'logo_id' => 0, 'initials' => 'SP', 'title' => 'Personal trainer — 7+ years', 'org' => 'NeuroAffective Touch Institute' ),
		),
		'about_pg_connect_eyebrow'     => 'If something in my story resonates',
		'about_pg_connect_heading'     => 'I would love to connect.',
		'about_pg_connect_button_text' => 'Book a Discovery Call',
		'about_pg_connect_button_url'  => '#contact',

		// Coaching page (template: page-coaching.php).
		'coaching_pg_hero_eyebrow'       => '1-1 Life Coaching · Melbourne and Online',
		'coaching_pg_hero_heading'       => "Real change.\nFrom the inside out.",
		'coaching_pg_hero_description'   => 'For the person who is ready to understand what has actually been getting in the way.',
		'coaching_pg_hero_tags_text'     => '',
		'coaching_pg_hero_image_id'      => '',
		'coaching_pg_hero_button_text'   => 'Book a Discovery Call',
		'coaching_pg_hero_button_url'    => '#contact',
		'coaching_pg_what_eyebrow'       => 'What this is',
		'coaching_pg_what_heading'       => 'Different to most talk therapies.',
		'coaching_pg_what_body'          => "My approach is unlike most talk therapies. I work bottom-up — through the body and the nervous system — to access the subconscious and get to the root of what is actually running underneath your behaviour.\n\nThe framework I use with every client is Awareness, Acceptance and Action. We start by seeing clearly what is happening in your thoughts, your body, and your patterns. We move through honest acknowledgment of what is here. Then we take aligned action — not forced, not pushed, but rooted.\n\nI draw on parts work, somatic practice, NLP, Timeline Therapy and trauma-informed tools. Because lasting change is not just cognitive — it lives in the body.\n\nThis is coaching for people who are ready to go deeper than surface-level goals.",
		'coaching_pg_what_button_text'   => 'Book a Discovery Call',
		'coaching_pg_what_button_url'    => '#contact',
		'coaching_pg_what_card_heading'  => 'Does this sound like you?',
		'coaching_pg_what_card_items'    => array(
			array( 'text' => "You know what you need to do but you're not doing it" ),
			array( 'text' => "You've tried therapy, programs and self-help and something still feels missing" ),
			array( 'text' => 'You put everyone else first and run on empty' ),
			array( 'text' => "You sense there's more available to you but don't know how to access it" ),
			array( 'text' => 'You want to feel genuinely well, not just functional' ),
		),
		'coaching_pg_pillars_eyebrow'    => 'How I Work',
		'coaching_pg_pillars_heading'    => 'Three pillars of lasting change.',
		'coaching_pg_pillar_items'       => array(
			array(
				'number' => '01',
				'title'  => 'Mindset and Subconscious',
				'body'   => 'We unpack your beliefs, values and the emotional states running underneath your behaviour. Using NLP, Timeline Therapy and hypnotherapy to access and shift the programs that have been driving your reality — often since childhood.',
			),
			array(
				'number' => '02',
				'title'  => 'Emotional Healing and Parts Work',
				'body'   => 'Using Internal Family Systems, Inner Child work, trauma-informed practice and Compassionate Inquiry to meet the parts of you that have been protecting old wounds — with curiosity and compassion rather than judgment.',
			),
			array(
				'number' => '03',
				'title'  => 'Body, Movement and Nutrition',
				'body'   => 'Drawing on my degree in Human Movement and Honours in Nutrition, we look at the whole person — how you move, how you nourish yourself, and how your physical health both reflects and affects your inner world.',
			),
		),
		'coaching_pg_work_eyebrow'       => 'What We Work On',
		'coaching_pg_work_heading'       => 'In our sessions together we explore.',
		'coaching_pg_work_gains_heading' => 'What clients gain',
		'coaching_pg_work_topics_items'  => array(
			array( 'text' => 'The subconscious programs and beliefs shaping your current reality' ),
			array( 'text' => "The parts of you that protect, sabotage or hold you back — and why they're there" ),
			array( 'text' => 'Emotions and memories from the past that are still running in the present' ),
			array( 'text' => 'Your values, identity and what actually matters to you' ),
			array( 'text' => 'Nutrition and lifestyle as part of a whole-person approach' ),
			array( 'text' => 'Practical strategies and tools you can use in everyday life' ),
			array( 'text' => 'How to go from reacting to responding — above the line thinking' ),
			array( 'text' => 'The nervous system — stress, regulation, safety' ),
		),
		'coaching_pg_work_gains_items'   => array(
			array( 'text' => 'A deep **understanding** of themselves and the patterns driving their behaviour' ),
			array( 'text' => '**Freedom** from beliefs and identities that have been holding them back' ),
			array( 'text' => 'Greater emotional **clarity**, **balance** and self-awareness' ),
			array( 'text' => 'Practical **tools** for navigating stress, challenge and change' ),
			array( 'text' => 'Renewed sense of **purpose**, **direction** and **confidence**' ),
			array( 'text' => 'Improved relationships, **communication** and **connection**' ),
			array( 'text' => 'A genuine felt sense of **possibility** — not just intellectually, but in the body' ),
		),
		'coaching_pg_expect_eyebrow'        => 'Keep what clients gain!',
		'coaching_pg_expect_heading_line1'  => 'What to expect',
		'coaching_pg_expect_heading_line2'  => 'when we work together.',
		'coaching_pg_expect_body'             => "We begin with a complimentary discovery call to explore whether we're the right fit. There is no pressure and no obligation — just a conversation.\n\nI work with men, women and couples across Australia. In person in South East Melbourne and online via Zoom. I also offer guided outdoor Walk and Talk sessions for those who work best in nature.",
		'coaching_pg_expect_quote'            => 'Clients describe my coaching style as supportive, compassionate, and patient with space to open up, trust and grow.',
		'coaching_pg_expect_button_text'    => 'Book a Discovery Call',
		'coaching_pg_expect_button_url'     => '#contact',
		'coaching_pg_expect_info_cards'     => array(
			array(
				'label' => 'Who I Work With',
				'body'  => 'Men, women and couples · All walks of life · Australia and worldwide · Teenagers by arrangement',
			),
			array(
				'label' => 'Important Note',
				'body'  => 'Life coaching is not a replacement for medically advised counselling or psychiatric intervention. I work alongside your existing support where relevant.',
			),
		),
		'coaching_pg_faq_heading' => 'Everything you need to know.',
		'coaching_pg_faq_items'   => array(
			array(
				'question' => 'What is the difference between life coaching and therapy?',
				'answer'   => 'While therapy often focuses on diagnosing and treating mental health conditions or deeply exploring the past, my coaching approach is forward-focused while still acknowledging and healing the root causes of current blocks. We use somatic and subconscious techniques to create tangible change in the present, rather than just talking about the problem.',
			),
			array(
				'question' => 'Who is this type of coaching for?',
				'answer'   => 'Anyone who feels stuck, disconnected, or ready for deeper change — especially if you have tried talk therapy or self-help and something still feels missing.',
			),
			array(
				'question' => 'How long are the sessions and how often do we meet?',
				'answer'   => 'Sessions are typically 60–90 minutes. Most clients meet weekly or fortnightly, depending on what you need.',
			),
			array(
				'question' => 'Do I have to commit to a certain number of sessions?',
				'answer'   => 'No long-term lock-in is required. We agree on a rhythm that supports your goals and adjust as you progress.',
			),
			array(
				'question' => 'Do you offer online sessions?',
				'answer'   => 'Yes — online sessions via Zoom are available across Australia and internationally.',
			),
			array(
				'question' => 'What happens in a Walk and Talk session?',
				'answer'   => 'We meet outdoors for a guided walk while we talk. Many clients find movement helps them think more clearly and feel more grounded.',
			),
			array(
				'question' => 'What should I expect in our first session?',
				'answer'   => 'We clarify what you want to change, explore what has been getting in the way, and outline a practical path forward tailored to you.',
			),
			array(
				'question' => 'What is your cancellation policy?',
				'answer'   => 'Please provide at least 24 hours notice to reschedule. Late cancellations may be charged at the session rate.',
			),
		),
	);

	if ( function_exists( 'anna_get_oasis_theme_option_defaults' ) ) {
		$defaults = array_merge( $defaults, anna_get_oasis_theme_option_defaults() );
	}

	if ( function_exists( 'anna_get_speaking_theme_option_defaults' ) ) {
		$defaults = array_merge( $defaults, anna_get_speaking_theme_option_defaults() );
	}

	return $defaults;
}

/**
 * Seed Coaching Page defaults into existing saved options.
 */
function anna_seed_coaching_page_defaults() {
	if ( ! is_admin() ) {
		return;
	}

	$defaults = anna_get_default_options();
	$options  = get_option( 'anna_theme_options', array() );

	if ( ! is_array( $options ) ) {
		$options = array();
	}

	$coaching_keys = array_keys( array_filter(
		$defaults,
		static function ( $key ) {
			return str_starts_with( (string) $key, 'coaching_pg_' );
		},
		ARRAY_FILTER_USE_KEY
	) );

	$changed = false;
	foreach ( $coaching_keys as $key ) {
		$has_value = false;
		if ( isset( $options[ $key ] ) ) {
			if ( is_array( $options[ $key ] ) ) {
				$has_value = ! empty( $options[ $key ] );
			} else {
				$has_value = '' !== trim( (string) $options[ $key ] );
			}
		}

		$default_has_value = false;
		if ( isset( $defaults[ $key ] ) ) {
			if ( is_array( $defaults[ $key ] ) ) {
				$default_has_value = ! empty( $defaults[ $key ] );
			} else {
				$default_has_value = '' !== (string) $defaults[ $key ];
			}
		}

		if ( ! $has_value && $default_has_value ) {
			$options[ $key ] = $defaults[ $key ];
			$changed         = true;
		}
	}

	if ( $changed ) {
		update_option( 'anna_theme_options', $options );
	}
}
add_action( 'admin_init', 'anna_seed_coaching_page_defaults', 20 );

/**
 * Ensure a Coaching page exists and uses the Coaching template.
 */
function anna_ensure_coaching_page_exists() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( get_option( 'anna_coaching_page_created', false ) ) {
		return;
	}

	$query = new WP_Query(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => 1,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'page-coaching.php',
			'fields'         => 'ids',
		)
	);

	if ( ! empty( $query->posts[0] ) ) {
		update_option( 'anna_coaching_page_created', 1 );
		return;
	}

	$page = get_page_by_path( 'coaching' );
	if ( $page instanceof WP_Post ) {
		update_post_meta( $page->ID, '_wp_page_template', 'page-coaching.php' );
		update_option( 'anna_coaching_page_created', 1 );
		return;
	}

	$page_id = wp_insert_post(
		array(
			'post_title'   => 'Coaching',
			'post_name'    => 'coaching',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => '',
		)
	);

	if ( $page_id && ! is_wp_error( $page_id ) ) {
		update_post_meta( $page_id, '_wp_page_template', 'page-coaching.php' );
	}

	update_option( 'anna_coaching_page_created', 1 );
}
add_action( 'admin_init', 'anna_ensure_coaching_page_exists', 22 );

/**
 * Seed Oasis Page defaults into saved theme options.
 */
function anna_seed_oasis_page_defaults() {
	if ( ! is_admin() ) {
		return;
	}

	$defaults = anna_get_default_options();
	$options  = get_option( 'anna_theme_options', array() );

	if ( ! is_array( $options ) ) {
		$options = array();
	}

	$oasis_keys = array_keys( array_filter(
		$defaults,
		static function ( $key ) {
			return str_starts_with( (string) $key, 'oasis_pg_' );
		},
		ARRAY_FILTER_USE_KEY
	) );

	$changed = false;
	foreach ( $oasis_keys as $key ) {
		$has_value = false;
		if ( isset( $options[ $key ] ) ) {
			$has_value = is_array( $options[ $key ] ) ? ! empty( $options[ $key ] ) : '' !== trim( (string) $options[ $key ] );
		}

		$default_has_value = isset( $defaults[ $key ] ) && ( is_array( $defaults[ $key ] ) ? ! empty( $defaults[ $key ] ) : '' !== (string) $defaults[ $key ] );

		if ( ! $has_value && $default_has_value ) {
			$options[ $key ] = $defaults[ $key ];
			$changed         = true;
		}
	}

	if ( $changed ) {
		update_option( 'anna_theme_options', $options );
	}
}
add_action( 'admin_init', 'anna_seed_oasis_page_defaults', 20 );

/**
 * Ensure Oasis page exists with correct template.
 */
function anna_ensure_oasis_page_exists() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( get_option( 'anna_oasis_page_created', false ) ) {
		return;
	}

	$query = new WP_Query(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => 1,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'page-oasis.php',
			'fields'         => 'ids',
		)
	);

	if ( ! empty( $query->posts[0] ) ) {
		update_option( 'anna_oasis_page_created', 1 );
		return;
	}

	$page = get_page_by_path( 'oasis' );
	if ( $page instanceof WP_Post ) {
		update_post_meta( $page->ID, '_wp_page_template', 'page-oasis.php' );
		update_option( 'anna_oasis_page_created', 1 );
		return;
	}

	$page_id = wp_insert_post(
		array(
			'post_title'   => 'Oasis',
			'post_name'    => 'oasis',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => '',
		)
	);

	if ( $page_id && ! is_wp_error( $page_id ) ) {
		update_post_meta( $page_id, '_wp_page_template', 'page-oasis.php' );
	}

	update_option( 'anna_oasis_page_created', 1 );
}
add_action( 'admin_init', 'anna_ensure_oasis_page_exists', 22 );

/**
 * Seed Speaking Page defaults into saved theme options.
 */
function anna_seed_speaking_page_defaults() {
	if ( ! is_admin() ) {
		return;
	}

	$defaults = anna_get_default_options();
	$options  = get_option( 'anna_theme_options', array() );

	if ( ! is_array( $options ) ) {
		$options = array();
	}

	$speaking_keys = array_keys( array_filter(
		$defaults,
		static function ( $key ) {
			return str_starts_with( (string) $key, 'speaking_pg_' );
		},
		ARRAY_FILTER_USE_KEY
	) );

	$changed = false;
	foreach ( $speaking_keys as $key ) {
		$has_value = false;
		if ( isset( $options[ $key ] ) ) {
			$has_value = is_array( $options[ $key ] ) ? ! empty( $options[ $key ] ) : '' !== trim( (string) $options[ $key ] );
		}

		$default_has_value = isset( $defaults[ $key ] ) && ( is_array( $defaults[ $key ] ) ? ! empty( $defaults[ $key ] ) : '' !== (string) $defaults[ $key ] );

		if ( ! $has_value && $default_has_value ) {
			$options[ $key ] = $defaults[ $key ];
			$changed         = true;
		}
	}

	if ( $changed ) {
		update_option( 'anna_theme_options', $options );
	}
}
add_action( 'admin_init', 'anna_seed_speaking_page_defaults', 20 );

/**
 * Ensure Speaking page exists with correct template.
 */
function anna_ensure_speaking_page_exists() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( get_option( 'anna_speaking_page_created', false ) ) {
		return;
	}

	$query = new WP_Query(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => 1,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'page-speaking.php',
			'fields'         => 'ids',
		)
	);

	if ( ! empty( $query->posts[0] ) ) {
		update_option( 'anna_speaking_page_created', 1 );
		return;
	}

	$page = get_page_by_path( 'speaking' );
	if ( $page instanceof WP_Post ) {
		update_post_meta( $page->ID, '_wp_page_template', 'page-speaking.php' );
		update_option( 'anna_speaking_page_created', 1 );
		return;
	}

	$page_id = wp_insert_post(
		array(
			'post_title'   => 'Speaking',
			'post_name'    => 'speaking',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => '',
		)
	);

	if ( $page_id && ! is_wp_error( $page_id ) ) {
		update_post_meta( $page_id, '_wp_page_template', 'page-speaking.php' );
	}

	update_option( 'anna_speaking_page_created', 1 );
}
add_action( 'admin_init', 'anna_ensure_speaking_page_exists', 22 );
