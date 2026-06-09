<?php
/**
 * Speaking page helpers.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Default Speaking page content.
 *
 * @return array<string, mixed>
 */
function anna_get_speaking_default_content()
{
    return [
        "hero_eyebrow" => "Motivational Speaker · Melbourne and Australia",
        "hero_heading" => "I speak from experience.\nNot from a textbook.",
        "hero_body" =>
            "My talks draw on decades of elite sport, years of deep coaching work and my own profound personal journey — from the Olympics to rock bottom and back. Every talk is grounded in real life, backed by neuroscience and designed to leave your audience genuinely moved to act.",
        "hero_image_id" => 0,
        "hero_button_text" => "Enquire About Speaking",
        "hero_button_url" => "#contact",
        "hero_secondary_text" => "See speaking topics",
        "hero_secondary_url" => "#speaking-topics",
        "hero_stat_items" => [
            ["value" => "AUS", "label" => "Olympian and Commonwealth Games"],
            ["value" => "7+", "label" => "Years coaching clients"],
            ["value" => "102", "label" => "Five-star Google reviews"],
            ["value" => "10+", "label" => "Professional qualifications"],
            ["value" => "IFS", "label" => "Trauma-informed and IFS trained"],
        ],

        "bring_eyebrow" => "What I bring to the stage",
        "bring_heading_line1" => "Real stories.",
        "bring_heading_line2" => "Real tools. Real impact.",
        "bring_body" =>
            "I have spent over a decade helping people understand why change feels so hard — and what actually creates it. That insight, combined with the credibility of an Olympic career and the authenticity of someone who has rebuilt their life from the ground up, makes for talks that land differently.\n\nI don't deliver polished performances. I have real conversations with audiences. And people leave feeling seen, understood and genuinely energised — not just inspired in the moment, but equipped with something they can actually use.",
        "bring_quote" =>
            "My goal is always the same — to leave people feeling inspired, energised and equipped with practical tools they can apply in their own lives.",
        "bring_button_text" => "Enquire About Speaking",
        "bring_button_url" => "#contact",
        "bring_image_id" => 0,

        "topics_eyebrow" => "Speaking Topics",
        "topics_heading" =>
            "Tailored to your audience. Grounded in lived experience.",
        "topics_intro" =>
            "Every talk is tailored to the audience and the goals of the event. My sessions combine inspiring storytelling with practical insights that people can apply in their daily lives. Popular topics include:",
        "topics_card_items" => [
            [
                "icon" => "brain",
                "title" =>
                    "The Mind-Body Connection — Why What You Think Affects How You Feel",
                "body" =>
                    "An exploration of the relationship between the nervous system, the subconscious and behaviour. How our past programs our present and what we can do about it.",
            ],
            [
                "icon" => "target",
                "title" =>
                    "Stress, the Vagus Nerve and Why Your Body Reacts the Way It Does",
                "body" =>
                    "The science of stress made practical and accessible. Why we respond the way we do and the tools that actually work to regulate the nervous system.",
            ],
            [
                "icon" => "heart",
                "title" => "Goal Setting That Creates Lasting Change",
                "body" =>
                    "A fresh perspective on goal setting that goes beyond traditional ideas of success. Encouraging audiences to focus on self-awareness, values and meaningful change — not just achievement.",
            ],
            [
                "icon" => "leaf",
                "title" =>
                    "Awareness, Acceptance, Action — A Framework for Lasting Change",
                "body" =>
                    "The three-step framework at the heart of my coaching work. Practical, powerful and immediately applicable to any area of life.",
            ],
            [
                "icon" => "compass",
                "title" => "Values and Self-Leadership",
                "body" =>
                    "Making decisions aligned with what actually matters — not what should matter. A practical exploration of values as an inner compass, and how self-leadership creates genuine confidence.",
            ],
            [
                "icon" => "shield",
                "title" => "Building Resilience From the Inside Out",
                "body" =>
                    "What resilience actually is and how to build it. Not through toughening up, but through genuine self-understanding and nervous system regulation.",
            ],
        ],

        "formats_eyebrow" => "Talk Formats",
        "formats_heading" => "Three ways to work together.",
        "formats_card_items" => [
            [
                "number" => "01",
                "title" => "Keynote presentations 30 to 60 minutes",
                "body" =>
                    "Inspiring presentations for conferences and events. 30 to 60 minutes. Combining personal storytelling, neuroscience-backed insights and practical tools audiences can apply straight away.",
            ],
            [
                "number" => "02",
                "title" => "Interactive workshops half day or full day",
                "body" =>
                    "Interactive half-day or full-day sessions focused on mindset, wellbeing and personal growth. Participants leave with a real experience of the work — not just ideas about it.",
            ],
            [
                "number" => "03",
                "title" => "Corporate wellness sessions",
                "body" =>
                    "Tailored sessions for teams and organisations. Creating space for connection, reflection and personal growth within a professional context. Also available as panel participation and MC work.",
            ],
            [
                "number" => "04",
                "title" => "Women's or Men's gatherings and retreats",
                "body" =>
                    "Immersive sessions for intimate gatherings and retreats — tailored to the audience, the setting and the outcomes you want to create together.",
            ],
            [
                "number" => "05",
                "title" => "Panel discussions and Q and A",
                "body" =>
                    "Thoughtful panel participation and facilitated Q&A — bringing depth, warmth and practical insight to the conversation.",
            ],
        ],
        "formats_audience_heading" => "My talks are well suited to",
        "formats_audience_items" => [
            ["text" => "Women's events and conferences"],
            ["text" => "Community groups and fundraising events"],
            ["text" => "Schools and youth programs"],
            ["text" => "Health and wellness conferences"],
            ["text" => "Corporate wellbeing and leadership events"],
            ["text" => "Retreats and immersive experiences"],
        ],

        "takeaway_eyebrow" => "What audiences take away",
        "takeaway_heading" => "Not just inspired. Genuinely equipped.",
        "takeaway_body" =>
            "I don't just tell stories from a stage. I give audiences something real to take back into their lives — a framework, a question, a moment of genuine self-recognition that stays with them long after the event.\n\nAudiences leave my talks feeling seen, understood and ready to do something differently. That's the goal every single time.",
        "takeaway_items" => [
            [
                "text" =>
                    "A renewed sense of motivation and genuine **possibility** — not just a temporary high",
            ],
            [
                "text" =>
                    "Practical **tools** for navigating stress, challenge and change in real life",
            ],
            [
                "text" =>
                    "Greater **self-awareness** and clarity about what has been getting in the way",
            ],
            [
                "text" =>
                    "Inspiration to take **meaningful action** — grounded in understanding, not just motivation",
            ],
            [
                "text" =>
                    "A framework they can come back to — **Awareness, Acceptance, Action**",
            ],
        ],

        "book_eyebrow" => "Book Anna to speak",
        "book_heading_line1" => "Let's create something",
        "book_heading_line2" => "your audience won't forget.",
        "book_body" =>
            "If you're looking for a speaker who brings authenticity, energy and practical insights to the stage — someone whose story stops people in their tracks and whose tools actually work in real life — I would love to hear from you.\n\nI tailor every talk to the audience and the goals of the event. Get in touch and let's have a conversation about what would work best for you.",
        "book_card_heading" => "Enquire about speaking",
        "book_card_body" =>
            "Send me a message with details about your event and I'll get back to you within 48 hours.",
        "book_card_button_text" => "Send an enquiry",
        "book_card_button_url" => "#contact",
        "book_card_contact_prefix" => "Or email me directly at",
        "book_card_email" => "info@annabaylis.com.au",
        "book_card_footer" => "I respond to all speaking enquiries personally.",

        "experience_eyebrow" => "Recent Experience",
        "experience_heading_primary" => "InspireHER",
        "experience_heading_secondary" => "Women's Conference",
        "experience_body" =>
            "I recently had the honour of speaking at the InspireHER Women's Conference, sharing my personal journey of overcoming the fear of public speaking and finding my voice.\n\nIn this talk I explored a fresh perspective on goal setting and personal growth, encouraging women to look beyond traditional ideas of success and instead focus on self-awareness, values and meaningful change.\n\nEvents like InspireHER are incredibly powerful because they bring people together to share stories, inspire one another and reconnect with what truly matters.",
        "experience_link_prefix" => "You can learn more about the event at",
        "experience_link_url" => "https://inspireherwomensconference.com.au",
        "experience_link_label" => "inspireherwomensconference.com.au",
        "experience_testimonial_quote" =>
            "I loved how Anna put a new spin on goal setting that I had never considered before. She was down to earth and knowledgeable. Having her present at InspireHER was very valuable for everyone present.",
        "experience_testimonial_name" => "Rachel Banner",
        "experience_testimonial_role" =>
            "Founder, InspireHER Women's Conference",
    ];
}

/**
 * Theme option defaults (speaking_pg_*).
 *
 * @return array<string, mixed>
 */
function anna_get_speaking_theme_option_defaults()
{
    $out = [];
    foreach (anna_get_speaking_default_content() as $key => $value) {
        $out["speaking_pg_" . $key] = $value;
    }
    return $out;
}

/**
 * @return array<string, string>
 */
function anna_get_speaking_page_option_map()
{
    $map = [];
    foreach (array_keys(anna_get_speaking_default_content()) as $key) {
        $map[$key] = "speaking_pg_" . $key;
    }
    return $map;
}

/**
 * @param mixed $text Text with **emphasis**.
 * @return string
 */
function anna_format_speaking_emphasis_text($text)
{
    return function_exists("anna_format_coaching_emphasis_text")
        ? anna_format_coaching_emphasis_text($text)
        : esc_html((string) $text);
}

/**
 * @param mixed $items Raw rows.
 * @return array<int, array{text:string}>
 */
function anna_normalize_speaking_text_items($items)
{
    if (!is_array($items)) {
        return [];
    }
    $out = [];
    foreach ($items as $row) {
        $text = is_string($row)
            ? $row
            : (is_array($row)
                ? $row["text"] ?? ""
                : "");
        $text = sanitize_text_field($text);
        if ("" !== trim($text)) {
            $out[] = ["text" => $text];
        }
    }
    return $out;
}

/**
 * @param mixed $items Raw rows.
 * @return array<int, array{value:string,label:string}>
 */
function anna_normalize_speaking_stat_items($items)
{
    if (!is_array($items)) {
        return [];
    }
    $out = [];
    foreach ($items as $row) {
        if (!is_array($row)) {
            continue;
        }
        $value = sanitize_text_field($row["value"] ?? "");
        $label = sanitize_text_field($row["label"] ?? "");
        if ("" === trim($value) && "" === trim($label)) {
            continue;
        }
        $out[] = ["value" => $value, "label" => $label];
    }
    return $out;
}

/**
 * @param mixed $items Raw rows.
 * @return array<int, array{icon:string,title:string,body:string}>
 */
function anna_normalize_speaking_topic_cards($items)
{
    if (!is_array($items)) {
        return [];
    }
    $out = [];
    foreach ($items as $row) {
        if (!is_array($row)) {
            continue;
        }
        $title = sanitize_text_field($row["title"] ?? "");
        if ("" === trim($title)) {
            continue;
        }
        $out[] = [
            "icon" => sanitize_key($row["icon"] ?? "brain"),
            "title" => $title,
            "body" => sanitize_textarea_field($row["body"] ?? ""),
        ];
    }
    return $out;
}

/**
 * @param mixed $items Raw rows.
 * @return array<int, array{number:string,title:string,body:string}>
 */
function anna_normalize_speaking_format_cards($items)
{
    if (!is_array($items)) {
        return [];
    }
    $out = [];
    foreach ($items as $index => $row) {
        if (!is_array($row)) {
            continue;
        }
        $title = sanitize_text_field($row["title"] ?? "");
        if ("" === trim($title)) {
            continue;
        }
        $number = sanitize_text_field($row["number"] ?? "");
        if ("" === $number) {
            $number = str_pad((string) ($index + 1), 2, "0", STR_PAD_LEFT);
        }
        $out[] = [
            "number" => $number,
            "title" => $title,
            "body" => sanitize_textarea_field($row["body"] ?? ""),
        ];
    }
    return $out;
}

/**
 * @param string $option_key Key without prefix.
 * @return array
 */
function anna_get_speaking_repeater_from_options($option_key)
{
    $full_key = "speaking_pg_" . $option_key;
    $defaults = anna_get_speaking_default_content();
    $saved = anna_get_option($full_key, []);

    if (is_array($saved) && !empty($saved)) {
        switch ($option_key) {
            case "hero_stat_items":
                return anna_normalize_speaking_stat_items($saved);
            case "topics_card_items":
                return anna_normalize_speaking_topic_cards($saved);
            case "formats_card_items":
                return anna_normalize_speaking_format_cards($saved);
            case "formats_audience_items":
            case "takeaway_items":
                return anna_normalize_speaking_text_items($saved);
        }
    }

    $default = $defaults[$option_key] ?? [];
    switch ($option_key) {
        case "hero_stat_items":
            return anna_normalize_speaking_stat_items($default);
        case "topics_card_items":
            return anna_normalize_speaking_topic_cards($default);
        case "formats_card_items":
            return anna_normalize_speaking_format_cards($default);
        case "formats_audience_items":
        case "takeaway_items":
            return anna_normalize_speaking_text_items($default);
    }
    return [];
}

/**
 * Get merged Speaking page content.
 *
 * @return array<string, mixed>
 */
function anna_get_speaking_page_content()
{
    $defaults = anna_get_speaking_default_content();
    $theme_defs = anna_get_default_options();
    $content = [];

    $repeaters = [
        "hero_stat_items",
        "topics_card_items",
        "formats_card_items",
        "formats_audience_items",
        "takeaway_items",
    ];

    $image_keys = ["hero_image_id", "bring_image_id"];
    $textarea_keys = [
        "hero_body",
        "bring_body",
        "topics_intro",
        "takeaway_body",
        "book_body",
        "experience_body",
    ];

    foreach ($defaults as $key => $default_value) {
        $option_key = "speaking_pg_" . $key;

        if (in_array($key, $repeaters, true)) {
            $content[$key] = anna_get_speaking_repeater_from_options($key);
            continue;
        }

        $fallback = $theme_defs[$option_key] ?? $default_value;
        if (in_array($key, $image_keys, true)) {
            $content[$key] = absint(anna_get_option($option_key, $fallback));
        } else {
            $content[$key] = anna_get_option($option_key, $fallback);
        }
    }

    $post_id = anna_get_current_page_content_id();
    if ($post_id && function_exists("anna_content_get_speaking_page_content")) {
        $saved = anna_content_get_speaking_page_content($post_id);
        if (is_array($saved)) {
            $merge = [];
            foreach ($saved as $key => $value) {
                if (is_array($value)) {
                    $normalized = [];
                    switch ($key) {
                        case "hero_stat_items":
                            $normalized = anna_normalize_speaking_stat_items(
                                $value,
                            );
                            break;
                        case "topics_card_items":
                            $normalized = anna_normalize_speaking_topic_cards(
                                $value,
                            );
                            break;
                        case "formats_card_items":
                            $normalized = anna_normalize_speaking_format_cards(
                                $value,
                            );
                            break;
                        case "formats_audience_items":
                        case "takeaway_items":
                            $normalized = anna_normalize_speaking_text_items(
                                $value,
                            );
                            break;
                    }
                    if (!empty($normalized)) {
                        $merge[$key] = $normalized;
                    }
                    continue;
                }
                if ("" !== trim((string) $value)) {
                    $merge[$key] = $value;
                }
            }
            if (!empty($merge)) {
                $content = wp_parse_args($merge, $content);
            }
        }
    }

    return $content;
}

/**
 * Sanitize Speaking theme options.
 *
 * @param string $key   Option key.
 * @param mixed  $value Raw value.
 * @return mixed
 */
function anna_sanitize_speaking_option($key, $value)
{
    if ("speaking_pg_hero_stat_items" === $key) {
        return anna_normalize_speaking_stat_items($value);
    }
    if ("speaking_pg_topics_card_items" === $key) {
        return anna_normalize_speaking_topic_cards($value);
    }
    if ("speaking_pg_formats_card_items" === $key) {
        return anna_normalize_speaking_format_cards($value);
    }
    if (
        in_array(
            $key,
            [
                "speaking_pg_formats_audience_items",
                "speaking_pg_takeaway_items",
            ],
            true,
        )
    ) {
        return anna_normalize_speaking_text_items($value);
    }
    if (
        in_array(
            $key,
            ["speaking_pg_hero_image_id", "speaking_pg_bring_image_id"],
            true,
        )
    ) {
        return absint($value);
    }
    $url_keys = [
        "speaking_pg_hero_button_url",
        "speaking_pg_hero_secondary_url",
        "speaking_pg_bring_button_url",
        "speaking_pg_book_card_button_url",
        "speaking_pg_experience_link_url",
    ];
    if (in_array($key, $url_keys, true)) {
        return sanitize_text_field($value);
    }
    $textarea_keys = [
        "speaking_pg_hero_body",
        "speaking_pg_bring_body",
        "speaking_pg_topics_intro",
        "speaking_pg_takeaway_body",
        "speaking_pg_book_body",
        "speaking_pg_experience_body",
        "speaking_pg_experience_testimonial_quote",
        "speaking_pg_bring_quote",
    ];
    if (in_array($key, $textarea_keys, true)) {
        return sanitize_textarea_field($value);
    }
    if ("speaking_pg_hero_heading" === $key) {
        return sanitize_textarea_field($value);
    }
    return sanitize_text_field($value);
}
