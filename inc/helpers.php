<?php
/**
 * Helper functions.
 *
 * Reusable utility functions used throughout the theme.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Check if a field value is the intentional-blank sentinel.
 *
 * When an admin types "empty--" into any text or textarea field,
 * the frontend will render nothing for that field instead of falling
 * back to the default content.
 *
 * @param  mixed $value The field value to check.
 * @return bool
 */
function anna_is_intentionally_blank($value)
{
    return is_string($value) && "empty--" === trim($value);
}

/**
 * Retrieve a theme option value with fallback.
 *
 * @param  string $key     Option key (without prefix).
 * @param  mixed  $default Fallback value.
 * @return mixed
 */
function anna_get_option($key, $default = "")
{
    $options = get_option("anna_theme_options", []);
    if (!isset($options[$key]) || $options[$key] === "") {
        return $default;
    }
    // Sentinel: admin typed "empty--" to intentionally blank this field.
    if (anna_is_intentionally_blank($options[$key])) {
        return "";
    }
    return $options[$key];
}

/**
 * Get the global discovery call / booking URL.
 *
 * All "Book a Discovery Call" buttons across the site fall back to this value.
 * Change it once in Anna Theme → Header to update every button simultaneously.
 *
 * @return string
 */
function anna_get_discovery_call_url()
{
    return anna_get_option("discovery_call_url", ANNA_DISCOVERY_CALL_URL);
}

/**
 * Get the current page ID used for dynamic content lookup.
 *
 * @return int
 */
function anna_get_current_page_content_id()
{
    if (is_front_page()) {
        return (int) get_queried_object_id();
    }

    if (is_page() || is_singular()) {
        return (int) get_queried_object_id();
    }

    return 0;
}

/**
 * Get homepage hero content from page data, falling back to theme options.
 *
 * @return array
 */
function anna_get_homepage_hero_content()
{
    $defaults = anna_get_default_options();
    $content = [
        "eyebrow" => anna_get_option("hero_eyebrow", $defaults["hero_eyebrow"]),
        "heading" => anna_get_option("hero_heading", $defaults["hero_heading"]),
        "description" => anna_get_option(
            "hero_description",
            $defaults["hero_description"],
        ),
        "trust_text" => anna_get_option(
            "hero_trust_text",
            $defaults["hero_trust_text"],
        ),
        "image_id" => absint(
            anna_get_option("hero_image_id", $defaults["hero_image_id"]),
        ),
        "stats" => anna_get_stats(),
        "primary_cta" => anna_get_cta("primary"),
        "secondary_cta" => anna_get_cta("secondary"),
    ];

    $post_id = anna_get_current_page_content_id();
    if ($post_id && function_exists("anna_content_get_page_section")) {
        $hero = anna_content_get_page_section($post_id, "hero");

        if (
            isset($hero["eyebrow"]) &&
            (!empty($hero["eyebrow"]) ||
                anna_is_intentionally_blank($hero["eyebrow"]))
        ) {
            $content["eyebrow"] = anna_is_intentionally_blank($hero["eyebrow"])
                ? ""
                : $hero["eyebrow"];
        }

        if (
            isset($hero["heading"]) &&
            (!empty($hero["heading"]) ||
                anna_is_intentionally_blank($hero["heading"]))
        ) {
            $content["heading"] = anna_is_intentionally_blank($hero["heading"])
                ? ""
                : nl2br($hero["heading"]);
        }

        if (
            isset($hero["description"]) &&
            (!empty($hero["description"]) ||
                anna_is_intentionally_blank($hero["description"]))
        ) {
            $content["description"] = anna_is_intentionally_blank(
                $hero["description"],
            )
                ? ""
                : $hero["description"];
        }

        if (
            isset($hero["trust_text"]) &&
            (!empty($hero["trust_text"]) ||
                anna_is_intentionally_blank($hero["trust_text"]))
        ) {
            $content["trust_text"] = anna_is_intentionally_blank(
                $hero["trust_text"],
            )
                ? ""
                : $hero["trust_text"];
        }

        if (!empty($hero["image_id"])) {
            $content["image_id"] = absint($hero["image_id"]);
        }

        if (
            isset($hero["primary_button_text"]) &&
            (!empty($hero["primary_button_text"]) ||
                anna_is_intentionally_blank($hero["primary_button_text"]))
        ) {
            $content["primary_cta"]["text"] = anna_is_intentionally_blank(
                $hero["primary_button_text"],
            )
                ? ""
                : $hero["primary_button_text"];
        }

        if (
            isset($hero["primary_button_url"]) &&
            (!empty($hero["primary_button_url"]) ||
                anna_is_intentionally_blank($hero["primary_button_url"]))
        ) {
            $content["primary_cta"]["url"] = anna_is_intentionally_blank(
                $hero["primary_button_url"],
            )
                ? ""
                : $hero["primary_button_url"];
        }

        if (
            isset($hero["secondary_button_text"]) &&
            (!empty($hero["secondary_button_text"]) ||
                anna_is_intentionally_blank($hero["secondary_button_text"]))
        ) {
            $content["secondary_cta"]["text"] = anna_is_intentionally_blank(
                $hero["secondary_button_text"],
            )
                ? ""
                : $hero["secondary_button_text"];
        }

        if (
            isset($hero["secondary_button_url"]) &&
            (!empty($hero["secondary_button_url"]) ||
                anna_is_intentionally_blank($hero["secondary_button_url"]))
        ) {
            $content["secondary_cta"]["url"] = anna_is_intentionally_blank(
                $hero["secondary_button_url"],
            )
                ? ""
                : $hero["secondary_button_url"];
        }

        $stats = [];
        for ($i = 1; $i <= 3; $i++) {
            $value = $hero["stat_" . $i . "_value"] ?? "";
            $label = $hero["stat_" . $i . "_label"] ?? "";
            if ("" !== $value || "" !== $label) {
                $stats[] = [
                    "value" => $value,
                    "label" => $label,
                ];
            }
        }

        if (!empty($stats)) {
            $content["stats"] = $stats;
        }
    }

    return $content;
}

/**
 * Get intro/recognition content from page data, with legacy fallback.
 *
 * @return array
 */
function anna_get_intro_section_content()
{
    $content = [
        "intro_eyebrow" => anna_get_option("intro_eyebrow", ""),
        "intro_heading" => anna_get_option(
            "intro_heading",
            "Real change. From the inside out.",
        ),
        "intro_body" => anna_get_option("intro_body", ""),
        "intro_quote" => anna_get_option("intro_quote", ""),
        "intro_quote_cite" => anna_get_option("intro_quote_cite", ""),
        "recognition_eyebrow" => anna_get_option("recognition_eyebrow", ""),
        "recognition_heading" => anna_get_option(
            "recognition_heading",
            "You might recognise yourself here",
        ),
        "recognition_description" => anna_get_option(
            "recognition_description",
            "",
        ),
        "recognition_items" => anna_get_lines_option("recognition_items_text", [
            'You feel stuck, disconnected or like you\'re going through the motions',
            'You know what you need to do but you\'re not doing it',
            'You\'ve tried therapy, programs and self-help and something still feels missing',
            "You put everyone else first and run on empty",
            'You sense there\'s more available to you but don\'t know how to access it',
            "You want to feel genuinely well, not just functional",
        ]),
    ];

    $post_id = anna_get_current_page_content_id();
    if ($post_id && function_exists("anna_content_get_page_section")) {
        $data = anna_content_get_page_section($post_id, "intro");
        if (
            isset($data["intro_eyebrow"]) &&
            (!empty($data["intro_eyebrow"]) ||
                anna_is_intentionally_blank($data["intro_eyebrow"]))
        ) {
            $content["intro_eyebrow"] = anna_is_intentionally_blank(
                $data["intro_eyebrow"],
            )
                ? ""
                : $data["intro_eyebrow"];
        }
        if (
            isset($data["intro_heading"]) &&
            (!empty($data["intro_heading"]) ||
                anna_is_intentionally_blank($data["intro_heading"]))
        ) {
            $content["intro_heading"] = anna_is_intentionally_blank(
                $data["intro_heading"],
            )
                ? ""
                : $data["intro_heading"];
        }
        if (
            isset($data["intro_body"]) &&
            (!empty($data["intro_body"]) ||
                anna_is_intentionally_blank($data["intro_body"]))
        ) {
            $content["intro_body"] = anna_is_intentionally_blank(
                $data["intro_body"],
            )
                ? ""
                : $data["intro_body"];
        }
        if (
            isset($data["intro_quote"]) &&
            (!empty($data["intro_quote"]) ||
                anna_is_intentionally_blank($data["intro_quote"]))
        ) {
            $content["intro_quote"] = anna_is_intentionally_blank(
                $data["intro_quote"],
            )
                ? ""
                : $data["intro_quote"];
        }
        if (
            isset($data["intro_quote_cite"]) &&
            (!empty($data["intro_quote_cite"]) ||
                anna_is_intentionally_blank($data["intro_quote_cite"]))
        ) {
            $content["intro_quote_cite"] = anna_is_intentionally_blank(
                $data["intro_quote_cite"],
            )
                ? ""
                : $data["intro_quote_cite"];
        }
        if (
            isset($data["recognition_eyebrow"]) &&
            (!empty($data["recognition_eyebrow"]) ||
                anna_is_intentionally_blank($data["recognition_eyebrow"]))
        ) {
            $content["recognition_eyebrow"] = anna_is_intentionally_blank(
                $data["recognition_eyebrow"],
            )
                ? ""
                : $data["recognition_eyebrow"];
        }
        if (
            isset($data["recognition_heading"]) &&
            (!empty($data["recognition_heading"]) ||
                anna_is_intentionally_blank($data["recognition_heading"]))
        ) {
            $content["recognition_heading"] = anna_is_intentionally_blank(
                $data["recognition_heading"],
            )
                ? ""
                : $data["recognition_heading"];
        }
        if (
            isset($data["recognition_description"]) &&
            (!empty($data["recognition_description"]) ||
                anna_is_intentionally_blank($data["recognition_description"]))
        ) {
            $content["recognition_description"] = anna_is_intentionally_blank(
                $data["recognition_description"],
            )
                ? ""
                : $data["recognition_description"];
        }
        if (!empty($data["recognition_items_text"])) {
            $content["recognition_items"] = preg_split(
                '/\r\n|\r|\n/',
                $data["recognition_items_text"],
            );
            $content["recognition_items"] = array_values(
                array_filter(array_map("trim", $content["recognition_items"])),
            );
        }
    }

    return $content;
}

/**
 * Get services section content from page data, with legacy fallback.
 *
 * @return array
 */
function anna_get_services_section_content()
{
    $default_cards = [
        [ 'number' => '01', 'title' => '1-1 Life Coaching',        'excerpt' => 'Deep, personalised work using a bottom-up approach that accesses the subconscious through the body and the nervous system. We get to the root of what is actually running underneath and change it.',                                                                                                                                                                                                               'link' => 'Find out more',            'url' => '', 'image_id' => 0 ],
        [ 'number' => '02', 'title' => 'Oasis Community',           'excerpt' => 'A womens wellness community for sustainable health and wellbeing. Ongoing live guidance, daily practices, guided movement, nutrition, meditation, breathwork and community connection. A space to come back to yourself week after week.',                                                                                                                                                                              'link' => 'Find out more',            'url' => '', 'image_id' => 0 ],
        [ 'number' => '03', 'title' => 'Speaking and Workshops',    'excerpt' => 'Keynotes and interactive sessions for conferences, corporate events and womens gatherings. Drawing on Olympic experience, deep coaching expertise and lived transformation. Topics include stress and the nervous system, building resilience, the mind-body connection and more.',                                                                                                                                       'link' => 'Enquire about speaking',   'url' => '', 'image_id' => 0 ],
    ];

    $content = [
        "eyebrow"     => anna_get_option("services_eyebrow", ""),
        "heading"     => anna_get_option("services_heading", 'What\'s the change you\'re needing?'),
        "description" => anna_get_option("services_description", ""),
        "cta_text"    => anna_get_option("services_cta_text", ""),
        "cta_url"     => anna_get_option("services_cta_url", "#"),
        "bg_image_id" => 0,
        "cards"       => $default_cards,
    ];

    $post_id = anna_get_current_page_content_id();
    if ($post_id && function_exists("anna_content_get_page_section")) {
        $data = anna_content_get_page_section($post_id, "services");
        foreach (["eyebrow", "heading", "description", "cta_text", "cta_url"] as $key) {
            if (isset($data[$key]) && (!empty($data[$key]) || anna_is_intentionally_blank($data[$key]))) {
                $content[$key] = anna_is_intentionally_blank($data[$key]) ? "" : $data[$key];
            }
        }

        if (!empty($data["bg_image_id"])) {
            $content["bg_image_id"] = absint($data["bg_image_id"]);
        }

        // Merge editable card data over the defaults.
        for ($i = 1; $i <= 3; $i++) {
            $idx = $i - 1;
            foreach (['title', 'excerpt', 'link', 'url', 'image_id'] as $field) {
                $key = 'card_' . $i . '_' . $field;
                if ($field === 'image_id') {
                    if (!empty($data[$key])) {
                        $content['cards'][$idx]['image_id'] = absint($data[$key]);
                    }
                } elseif (!empty($data[$key])) {
                    $content['cards'][$idx][$field] = $data[$key];
                }
            }
        }
    }

    return $content;
}

/**
 * Get about section content from page data, with legacy fallback.
 *
 * @return array
 */
function anna_get_about_section_content()
{
    $content = [
        "eyebrow" => anna_get_option("about_eyebrow", ""),
        "heading" => anna_get_option(
            "about_heading",
            "Olympian. Life Coach. Motivational Speaker.",
        ),
        "body" => anna_get_option("about_body", ""),
        "quote" => anna_get_option("about_quote", ""),
        "image_id" => anna_get_option("about_image_id", ""),
        "badge_number" => anna_get_option("about_badge_number", ""),
        "badge_text" => anna_get_option("about_badge_text", ""),
        "expertise" => anna_get_lines_option("about_expertise_text", []),
        "cta_text" => anna_get_option("about_cta_text", ""),
        "cta_url" => anna_get_option("about_cta_url", "#"),
    ];

    $post_id = anna_get_current_page_content_id();
    if ($post_id && function_exists("anna_content_get_page_section")) {
        $data = anna_content_get_page_section($post_id, "about");
        foreach (
            [
                "eyebrow",
                "heading",
                "body",
                "quote",
                "badge_number",
                "badge_text",
                "cta_text",
                "cta_url",
            ]
            as $key
        ) {
            if (
                isset($data[$key]) &&
                (!empty($data[$key]) ||
                    anna_is_intentionally_blank($data[$key]))
            ) {
                $content[$key] = anna_is_intentionally_blank($data[$key])
                    ? ""
                    : $data[$key];
            }
        }
        if (!empty($data["image_id"])) {
            $content["image_id"] = absint($data["image_id"]);
        }
        if (!empty($data["expertise_text"])) {
            $content["expertise"] = preg_split(
                '/\r\n|\r|\n/',
                $data["expertise_text"],
            );
            $content["expertise"] = array_values(
                array_filter(array_map("trim", $content["expertise"])),
            );
        }
    }

    return $content;
}

/**
 * Get testimonials section content from page data, with legacy fallback.
 *
 * @return array
 */
function anna_get_testimonials_section_content()
{
    $content = [
        "eyebrow" => anna_get_option("testimonials_eyebrow", ""),
        "heading" => anna_get_option(
            "testimonials_heading",
            "102 five-star Google reviews",
        ),
        "summary" => anna_get_option("testimonials_summary", ""),
        "cta_text" => anna_get_option("testimonials_cta_text", ""),
        "cta_url" => anna_get_option("testimonials_cta_url", "#"),
    ];

    $post_id = anna_get_current_page_content_id();
    if ($post_id && function_exists("anna_content_get_page_section")) {
        $data = anna_content_get_page_section($post_id, "testimonials");
        foreach (
            ["eyebrow", "heading", "summary", "cta_text", "cta_url"]
            as $key
        ) {
            if (
                isset($data[$key]) &&
                (!empty($data[$key]) ||
                    anna_is_intentionally_blank($data[$key]))
            ) {
                $content[$key] = anna_is_intentionally_blank($data[$key])
                    ? ""
                    : $data[$key];
            }
        }
    }

    return $content;
}

/**
 * Get final CTA section content from page data, with legacy fallback.
 *
 * @return array
 */
function anna_get_final_cta_section_content()
{
    $content = [
        "eyebrow" => anna_get_option("cta_eyebrow", ""),
        "heading" => anna_get_option("cta_heading", ""),
        "description" => anna_get_option("cta_description", ""),
        "trust_text" => anna_get_option("cta_trust", ""),
        "primary_cta" => anna_get_cta("primary"),
        "secondary_cta" => anna_get_cta("secondary"),
    ];

    $post_id = anna_get_current_page_content_id();
    if ($post_id && function_exists("anna_content_get_page_section")) {
        $data = anna_content_get_page_section($post_id, "cta");
        foreach (["eyebrow", "heading", "description", "trust_text"] as $key) {
            if (
                isset($data[$key]) &&
                (!empty($data[$key]) ||
                    anna_is_intentionally_blank($data[$key]))
            ) {
                $content[$key] = anna_is_intentionally_blank($data[$key])
                    ? ""
                    : $data[$key];
            }
        }
        if (
            isset($data["primary_button_text"]) &&
            (!empty($data["primary_button_text"]) ||
                anna_is_intentionally_blank($data["primary_button_text"]))
        ) {
            $content["primary_cta"]["text"] = anna_is_intentionally_blank(
                $data["primary_button_text"],
            )
                ? ""
                : $data["primary_button_text"];
        }
        if (
            isset($data["primary_button_url"]) &&
            (!empty($data["primary_button_url"]) ||
                anna_is_intentionally_blank($data["primary_button_url"]))
        ) {
            $content["primary_cta"]["url"] = anna_is_intentionally_blank(
                $data["primary_button_url"],
            )
                ? ""
                : $data["primary_button_url"];
        }
        if (
            isset($data["secondary_button_text"]) &&
            (!empty($data["secondary_button_text"]) ||
                anna_is_intentionally_blank($data["secondary_button_text"]))
        ) {
            $content["secondary_cta"]["text"] = anna_is_intentionally_blank(
                $data["secondary_button_text"],
            )
                ? ""
                : $data["secondary_button_text"];
        }
        if (
            isset($data["secondary_button_url"]) &&
            (!empty($data["secondary_button_url"]) ||
                anna_is_intentionally_blank($data["secondary_button_url"]))
        ) {
            $content["secondary_cta"]["url"] = anna_is_intentionally_blank(
                $data["secondary_button_url"],
            )
                ? ""
                : $data["secondary_button_url"];
        }
    }

    return $content;
}

/**
 * Default About page content keys mapped to theme option names.
 *
 * @return array<string, string> Template key => option key.
 */
function anna_get_about_page_option_map()
{
    return [
        "hero_eyebrow" => "about_pg_hero_eyebrow",
        "hero_heading" => "about_pg_hero_heading",
        "hero_subheading" => "about_pg_hero_subheading",
        "hero_description" => "about_pg_hero_description",
        "hero_image_id" => "about_pg_hero_image_id",
        "hero_tags" => "about_pg_hero_tags_text",
        "story_eyebrow" => "about_pg_story_eyebrow",
        "story_heading" => "about_pg_story_heading",
        "story_body" => "about_pg_story_body",
        "story_image_id" => "about_pg_story_image_id",
        "rock_heading" => "about_pg_rock_heading",
        "rock_left_body" => "about_pg_rock_left_body",
        "rock_right_body" => "about_pg_rock_right_body",
        // Coach (new design layout).
        "coach_eyebrow" => "about_pg_coach_eyebrow",
        "coach_title" => "about_pg_coach_title",
        "coach_body" => "about_pg_coach_body",
        "coach_button_text" => "about_pg_coach_button_text",
        "coach_button_url" => "about_pg_coach_button_url",
        "coach_image_id" => "about_pg_coach_image_id",

        // How I work section.
        "work_eyebrow" => "about_pg_work_eyebrow",
        "work_heading" => "about_pg_work_heading",
        "work_body" => "about_pg_work_body",
        "work_card_1_title" => "about_pg_work_card_1_title",
        "work_card_1_body" => "about_pg_work_card_1_body",
        "work_card_2_title" => "about_pg_work_card_2_title",
        "work_card_2_body" => "about_pg_work_card_2_body",
        "work_card_3_title" => "about_pg_work_card_3_title",
        "work_card_3_body" => "about_pg_work_card_3_body",
        "work_card_4_title" => "about_pg_work_card_4_title",
        "work_card_4_body" => "about_pg_work_card_4_body",

        // What people say section (repeater cards).
        "people_eyebrow" => "about_pg_people_eyebrow",
        "people_heading" => "about_pg_people_heading",
        "people_body" => "about_pg_people_body",
        "people_items" => "about_pg_people_items",

        // I would love to connect (CTA).
        "connect_eyebrow" => "about_pg_connect_eyebrow",
        "connect_heading" => "about_pg_connect_heading",
        "connect_button_text" => "about_pg_connect_button_text",
        "connect_button_url" => "about_pg_connect_button_url",
    ];
}

/**
 * Parse "What people say" items list.
 *
 * Each line should be one item in the format:
 * INITIALS|TITLE|ORG
 *
 * @param string $raw Newline-separated items.
 * @return array<int, array{initials:string,title:string,org:string}>
 */
function anna_parse_about_people_items($raw)
{
    if (!is_string($raw) || "" === trim($raw)) {
        return [];
    }

    $lines = preg_split('/\r\n|\r|\n/', $raw);
    $lines = array_values(array_filter(array_map("trim", (array) $lines)));

    $items = [];
    foreach ($lines as $line) {
        $parts = array_map("trim", explode("|", $line));
        $parts = array_pad($parts, 3, "");

        $items[] = [
            "initials" => (string) $parts[0],
            "title" => (string) $parts[1],
            "org" => (string) $parts[2],
        ];
    }

    return $items;
}

/**
 * Normalize a single "What people say" repeater row.
 *
 * @param array $row Raw row.
 * @return array{logo_id:int,initials:string,title:string,org:string}|null
 */
function anna_normalize_about_people_item($row)
{
    if (!is_array($row)) {
        return null;
    }

    $logo_id = absint($row["logo_id"] ?? 0);
    $initials = sanitize_text_field($row["initials"] ?? "");
    $title = sanitize_text_field($row["title"] ?? "");
    $org = sanitize_textarea_field($row["org"] ?? ($row["description"] ?? ""));

    if (
        0 === $logo_id &&
        "" === trim($initials) &&
        "" === trim($title) &&
        "" === trim($org)
    ) {
        return null;
    }

    return [
        "logo_id" => $logo_id,
        "initials" => $initials,
        "title" => $title,
        "org" => $org,
    ];
}

/**
 * Normalize repeater rows for "What people say".
 *
 * @param mixed $items Raw items.
 * @return array<int, array{logo_id:int,initials:string,title:string,org:string}>
 */
function anna_normalize_about_people_items($items)
{
    if (!is_array($items)) {
        return [];
    }

    $normalized = [];
    foreach ($items as $row) {
        $item = anna_normalize_about_people_item($row);
        if ($item) {
            $normalized[] = $item;
        }
    }

    return $normalized;
}

/**
 * Convert legacy qualification repeater rows to people items.
 *
 * @param array $qual_rows Legacy rows.
 * @return array<int, array{logo_id:int,initials:string,title:string,org:string}>
 */
function anna_convert_qualifications_to_people_items($qual_rows)
{
    if (!is_array($qual_rows)) {
        return [];
    }

    $items = [];
    foreach ($qual_rows as $row) {
        if (!is_array($row)) {
            continue;
        }

        $item = anna_normalize_about_people_item([
            "logo_id" => $row["logo_id"] ?? 0,
            "initials" => $row["initials"] ?? "",
            "title" => $row["title"] ?? "",
            "description" => $row["description"] ?? "",
        ]);

        if ($item) {
            $items[] = $item;
        }
    }

    return $items;
}

/**
 * Load "What people say" items from theme options (repeater + legacy fallbacks).
 *
 * @return array<int, array{logo_id:int,initials:string,title:string,org:string}>
 */
function anna_get_about_people_items_from_options()
{
    $defaults = anna_get_default_options();

    $saved = anna_get_option("about_pg_people_items", []);
    if (is_array($saved) && !empty($saved)) {
        $items = anna_normalize_about_people_items($saved);
        if (!empty($items)) {
            return $items;
        }
    }

    $text_default = (string) ($defaults["about_pg_people_items_text"] ?? "");
    $text_raw = (string) anna_get_option(
        "about_pg_people_items_text",
        $text_default,
    );
    $from_text = anna_parse_about_people_items($text_raw);
    if (!empty($from_text)) {
        return anna_normalize_about_people_items($from_text);
    }

    $qual_saved = anna_get_option("about_pg_qualifications", []);
    $from_qual = anna_convert_qualifications_to_people_items(
        is_array($qual_saved) ? $qual_saved : [],
    );
    if (!empty($from_qual)) {
        return $from_qual;
    }

    $default_items = $defaults["about_pg_people_items"] ?? [];
    return anna_normalize_about_people_items($default_items);
}

/**
 * Get About page content from theme options (same pattern as homepage sections).
 *
 * @return array
 */
function anna_get_about_page_content()
{
    $defaults = anna_get_default_options();
    $option_map = anna_get_about_page_option_map();
    $content = [];

    foreach ($option_map as $template_key => $option_key) {
        $default = $defaults[$option_key] ?? "";

        if ("people_items" === $template_key) {
            $content[
                "people_items"
            ] = anna_get_about_people_items_from_options();
            continue;
        }

        if (str_ends_with($template_key, "_image_id")) {
            $content[$template_key] = absint(
                anna_get_option($option_key, $default),
            );
            continue;
        }

        if ("hero_tags" === $template_key) {
            $tags_default = isset($defaults["about_pg_hero_tags_text"])
                ? preg_split(
                    '/\r\n|\r|\n/',
                    $defaults["about_pg_hero_tags_text"],
                )
                : [];
            $content["hero_tags"] = anna_get_lines_option(
                "about_pg_hero_tags_text",
                $tags_default,
            );
            continue;
        }

        $content[$template_key] = anna_get_option($option_key, $default);
    }

    // Merge page-level overrides (content-manager plugin), but only non-empty values.

    $post_id = anna_get_current_page_content_id();
    if ($post_id && function_exists("anna_content_get_about_page_content")) {
        $saved = anna_content_get_about_page_content($post_id);
        if (is_array($saved)) {
            // Only override defaults with non-empty values so that
            // empty meta does not wipe out the design defaults.
            $non_empty_saved = [];
            foreach ($saved as $key => $value) {
                if (is_array($value)) {
                    // Legacy: people_items saved as newline-split strings from the page editor.
                    if (
                        "people_items" === $key &&
                        !empty($value) &&
                        is_string(reset($value))
                    ) {
                        $lines = implode("\n", array_map("strval", $value));
                        $normalized = anna_normalize_about_people_items(
                            anna_parse_about_people_items($lines),
                        );
                        if (!empty($normalized)) {
                            $non_empty_saved["people_items"] = $normalized;
                        }
                        continue;
                    }

                    // For list-of-arrays (e.g. people repeater), keep if any row has content.
                    $is_list_of_arrays =
                        !empty($value) && is_array(reset($value));
                    if ($is_list_of_arrays) {
                        if ("people_items" === $key) {
                            $normalized = anna_normalize_about_people_items(
                                $value,
                            );
                            if (!empty($normalized)) {
                                $non_empty_saved["people_items"] = $normalized;
                            }
                        } elseif ("qualifications" === $key) {
                            $converted = anna_convert_qualifications_to_people_items(
                                $value,
                            );
                            if (!empty($converted)) {
                                $non_empty_saved["people_items"] = $converted;
                            }
                        } else {
                            $has_any = false;
                            foreach ($value as $row) {
                                if (!is_array($row)) {
                                    continue;
                                }
                                $logo = absint($row["logo_id"] ?? 0);
                                $initials = trim(
                                    (string) ($row["initials"] ?? ""),
                                );
                                $t = trim((string) ($row["title"] ?? ""));
                                $d = trim(
                                    (string) ($row["description"] ??
                                        ($row["org"] ?? "")),
                                );
                                if (
                                    $logo ||
                                    "" !== $initials ||
                                    "" !== $t ||
                                    "" !== $d
                                ) {
                                    $has_any = true;
                                    break;
                                }
                            }
                            if ($has_any) {
                                $non_empty_saved[$key] = $value;
                            }
                        }
                        continue;
                    }

                    $array_value = array_values(
                        array_filter(array_map("trim", $value)),
                    );
                    if (!empty($array_value)) {
                        $non_empty_saved[$key] = $array_value;
                    }
                    continue;
                }

                $trimmed = trim((string) $value);
                if ("empty--" === $trimmed) {
                    // Intentionally blank — override default with empty string.
                    $non_empty_saved[$key] = "";
                } elseif ("" !== $trimmed) {
                    $non_empty_saved[$key] = $value;
                }
            }

            if (!empty($non_empty_saved)) {
                $content = wp_parse_args($non_empty_saved, $content);
            }
        }
    }

    return $content;
}

/**
 * Default Coaching page content keys mapped to theme option names.
 *
 * @return array<string, string>
 */
function anna_get_coaching_page_option_map()
{
    return [
        "hero_eyebrow" => "coaching_pg_hero_eyebrow",
        "hero_heading" => "coaching_pg_hero_heading",
        "hero_description" => "coaching_pg_hero_description",
        "hero_tags" => "coaching_pg_hero_tags_text",
        "hero_image_id" => "coaching_pg_hero_image_id",
        "hero_button_text" => "coaching_pg_hero_button_text",
        "hero_button_url" => "coaching_pg_hero_button_url",
        "what_eyebrow" => "coaching_pg_what_eyebrow",
        "what_heading" => "coaching_pg_what_heading",
        "what_body" => "coaching_pg_what_body",
        "what_button_text" => "coaching_pg_what_button_text",
        "what_button_url" => "coaching_pg_what_button_url",
        "what_card_heading" => "coaching_pg_what_card_heading",
        "what_card_items" => "coaching_pg_what_card_items",
        "pillars_eyebrow" => "coaching_pg_pillars_eyebrow",
        "pillars_heading" => "coaching_pg_pillars_heading",
        "pillar_items" => "coaching_pg_pillar_items",
        "work_eyebrow" => "coaching_pg_work_eyebrow",
        "work_heading" => "coaching_pg_work_heading",
        "work_gains_heading" => "coaching_pg_work_gains_heading",
        "work_topics_items" => "coaching_pg_work_topics_items",
        "work_gains_items" => "coaching_pg_work_gains_items",
        "expect_eyebrow" => "coaching_pg_expect_eyebrow",
        "expect_heading_line1" => "coaching_pg_expect_heading_line1",
        "expect_heading_line2" => "coaching_pg_expect_heading_line2",
        "expect_body" => "coaching_pg_expect_body",
        "expect_quote" => "coaching_pg_expect_quote",
        "expect_button_text" => "coaching_pg_expect_button_text",
        "expect_button_url" => "coaching_pg_expect_button_url",
        "expect_info_cards" => "coaching_pg_expect_info_cards",
        "faq_heading" => "coaching_pg_faq_heading",
        "faq_items" => "coaching_pg_faq_items",
    ];
}

/**
 * Wrap **emphasis** markers in <strong> for coaching gain lines.
 *
 * @param string $text Raw text.
 * @return string
 */
function anna_format_coaching_emphasis_text($text)
{
    $text = (string) $text;
    if ("" === $text) {
        return "";
    }

    return preg_replace_callback(
        "/\*\*(.+?)\*\*/",
        static function ($matches) {
            return "<strong>" . esc_html($matches[1]) . "</strong>";
        },
        $text,
    );
}

/**
 * Normalize simple text repeater rows.
 *
 * @param mixed $items Raw items.
 * @return array<int, array{text:string}>
 */
function anna_normalize_coaching_text_items($items)
{
    if (!is_array($items)) {
        return [];
    }

    $normalized = [];
    foreach ($items as $row) {
        if (is_string($row)) {
            $text = sanitize_text_field($row);
        } elseif (is_array($row)) {
            $text = sanitize_text_field($row["text"] ?? "");
        } else {
            continue;
        }

        if ("" === trim($text)) {
            continue;
        }

        $normalized[] = ["text" => $text];
    }

    return $normalized;
}

/**
 * Normalize FAQ repeater rows.
 *
 * @param mixed $items Raw items.
 * @return array<int, array{question:string,answer:string}>
 */
function anna_normalize_coaching_faq_items($items)
{
    if (!is_array($items)) {
        return [];
    }

    $normalized = [];
    foreach ($items as $row) {
        if (!is_array($row)) {
            continue;
        }

        $question = sanitize_text_field($row["question"] ?? "");
        $answer = sanitize_textarea_field($row["answer"] ?? "");

        if ("" === trim($question)) {
            continue;
        }

        $normalized[] = [
            "question" => $question,
            "answer" => $answer,
        ];
    }

    return $normalized;
}

/**
 * Normalize pillar card repeater rows.
 *
 * @param mixed $items Raw items.
 * @return array<int, array{number:string,title:string,body:string}>
 */
function anna_normalize_coaching_pillar_items($items)
{
    if (!is_array($items)) {
        return [];
    }

    $normalized = [];
    foreach ($items as $row) {
        if (!is_array($row)) {
            continue;
        }

        $number = sanitize_text_field($row["number"] ?? "");
        $title = sanitize_text_field($row["title"] ?? "");
        $body = sanitize_textarea_field($row["body"] ?? "");

        if ("" === trim($title) && "" === trim($body)) {
            continue;
        }

        $normalized[] = [
            "number" => $number,
            "title" => $title,
            "body" => $body,
        ];
    }

    return $normalized;
}

/**
 * Normalize info card repeater rows.
 *
 * @param mixed $items Raw items.
 * @return array<int, array{label:string,body:string}>
 */
function anna_normalize_coaching_info_cards($items)
{
    if (!is_array($items)) {
        return [];
    }

    $normalized = [];
    foreach ($items as $row) {
        if (!is_array($row)) {
            continue;
        }

        $label = sanitize_text_field($row["label"] ?? "");
        $body = sanitize_textarea_field($row["body"] ?? "");

        if ("" === trim($label) && "" === trim($body)) {
            continue;
        }

        $normalized[] = [
            "label" => $label,
            "body" => $body,
        ];
    }

    return $normalized;
}

/**
 * Load coaching repeater items from theme options.
 *
 * @param string $key Option key for repeater array.
 * @return array
 */
function anna_get_coaching_repeater_from_options($key)
{
    $defaults = anna_get_default_options();
    $saved = anna_get_option($key, []);

    if (is_array($saved) && !empty($saved)) {
        if (
            "coaching_pg_work_topics_items" === $key ||
            "coaching_pg_work_gains_items" === $key
        ) {
            return anna_normalize_coaching_text_items($saved);
        }
        if ("coaching_pg_expect_info_cards" === $key) {
            return anna_normalize_coaching_info_cards($saved);
        }
        if ("coaching_pg_faq_items" === $key) {
            return anna_normalize_coaching_faq_items($saved);
        }
        if ("coaching_pg_what_card_items" === $key) {
            return anna_normalize_coaching_text_items($saved);
        }
        if ("coaching_pg_pillar_items" === $key) {
            return anna_normalize_coaching_pillar_items($saved);
        }
    }

    $default_items = $defaults[$key] ?? [];
    if (
        "coaching_pg_work_topics_items" === $key ||
        "coaching_pg_work_gains_items" === $key ||
        "coaching_pg_what_card_items" === $key
    ) {
        return anna_normalize_coaching_text_items($default_items);
    }
    if ("coaching_pg_expect_info_cards" === $key) {
        return anna_normalize_coaching_info_cards($default_items);
    }
    if ("coaching_pg_faq_items" === $key) {
        return anna_normalize_coaching_faq_items($default_items);
    }
    if ("coaching_pg_pillar_items" === $key) {
        return anna_normalize_coaching_pillar_items($default_items);
    }

    return [];
}

/**
 * Render a single coaching FAQ accordion item.
 *
 * @param array $item  FAQ row.
 * @param int   $index Item index for unique IDs.
 */
function anna_render_coaching_faq_item($item, $index)
{
    if (!is_array($item)) {
        return;
    }

    $question = trim((string) ($item["question"] ?? ""));
    $answer = trim((string) ($item["answer"] ?? ""));

    if ("" === $question) {
        return;
    }

    $id = "anna-coaching-faq-" . absint($index);
    $is_open = 0 === (int) $index && "" !== $answer;
    ?>
	<div class="anna-coaching-page-faq__item<?php echo $is_open
     ? " is-open"
     : ""; ?>">
		<h3 class="anna-coaching-page-faq__question-wrap">
			<button
				type="button"
				class="anna-coaching-page-faq__trigger"
				id="<?php echo esc_attr($id); ?>-trigger"
				aria-expanded="<?php echo $is_open ? "true" : "false"; ?>"
				aria-controls="<?php echo esc_attr($id); ?>-panel"
			>
				<span class="anna-coaching-page-faq__question"><?php echo esc_html(
        $question,
    ); ?></span>
				<span class="anna-coaching-page-faq__icon" aria-hidden="true"></span>
			</button>
		</h3>
		<?php if ("" !== $answer): ?>
			<div
				class="anna-coaching-page-faq__panel"
				id="<?php echo esc_attr($id); ?>-panel"
				role="region"
				aria-labelledby="<?php echo esc_attr($id); ?>-trigger"
				style="<?php echo $is_open ? 'height:auto;' : 'height:0px;'; ?>"
			>
				<div class="anna-coaching-page-faq__panel-inner">
					<div class="anna-coaching-page-faq__answer"><?php echo wp_kses_post(
        wpautop($answer),
    ); ?></div>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Get Coaching page content from theme options (same pattern as About page).
 *
 * @return array
 */
function anna_get_coaching_page_content()
{
    $defaults = anna_get_default_options();
    $option_map = anna_get_coaching_page_option_map();
    $content = [];

    $repeater_keys = [
        "what_card_items" => "coaching_pg_what_card_items",
        "pillar_items" => "coaching_pg_pillar_items",
        "work_topics_items" => "coaching_pg_work_topics_items",
        "work_gains_items" => "coaching_pg_work_gains_items",
        "expect_info_cards" => "coaching_pg_expect_info_cards",
        "faq_items" => "coaching_pg_faq_items",
    ];

    foreach ($option_map as $template_key => $option_key) {
        if (isset($repeater_keys[$template_key])) {
            $content[$template_key] = anna_get_coaching_repeater_from_options(
                $repeater_keys[$template_key],
            );
            continue;
        }

        $default = $defaults[$option_key] ?? "";

        if (str_ends_with($template_key, "_image_id")) {
            $content[$template_key] = absint(
                anna_get_option($option_key, $default),
            );
            continue;
        }

        if ("hero_tags" === $template_key) {
            $tags_default = isset($defaults["coaching_pg_hero_tags_text"])
                ? preg_split(
                    '/\r\n|\r|\n/',
                    $defaults["coaching_pg_hero_tags_text"],
                )
                : [];
            $content["hero_tags"] = anna_get_lines_option(
                "coaching_pg_hero_tags_text",
                $tags_default,
            );
            continue;
        }

        $content[$template_key] = anna_get_option($option_key, $default);
    }

    $post_id = anna_get_current_page_content_id();
    if ($post_id && function_exists("anna_content_get_coaching_page_content")) {
        $saved = anna_content_get_coaching_page_content($post_id);
        if (is_array($saved)) {
            $non_empty_saved = [];
            foreach ($saved as $key => $value) {
                if (is_array($value)) {
                    if (
                        in_array(
                            $key,
                            [
                                "work_topics_items",
                                "work_gains_items",
                                "what_card_items",
                            ],
                            true,
                        )
                    ) {
                        $normalized = anna_normalize_coaching_text_items(
                            $value,
                        );
                        if (!empty($normalized)) {
                            $non_empty_saved[$key] = $normalized;
                        }
                        continue;
                    }
                    if ("pillar_items" === $key) {
                        $normalized = anna_normalize_coaching_pillar_items(
                            $value,
                        );
                        if (!empty($normalized)) {
                            $non_empty_saved["pillar_items"] = $normalized;
                        }
                        continue;
                    }
                    if ("expect_info_cards" === $key) {
                        $normalized = anna_normalize_coaching_info_cards(
                            $value,
                        );
                        if (!empty($normalized)) {
                            $non_empty_saved[$key] = $normalized;
                        }
                        continue;
                    }
                    if ("faq_items" === $key) {
                        $normalized = anna_normalize_coaching_faq_items($value);
                        if (!empty($normalized)) {
                            $non_empty_saved[$key] = $normalized;
                        }
                        continue;
                    }
                    if ("hero_tags" === $key) {
                        $tags = array_values(
                            array_filter(array_map("trim", $value)),
                        );
                        if (!empty($tags)) {
                            $non_empty_saved["hero_tags"] = $tags;
                        }
                        continue;
                    }
                    continue;
                }

                $trimmed = trim((string) $value);
                if ("empty--" === $trimmed) {
                    // Intentionally blank — override default with empty string.
                    $non_empty_saved[$key] = "";
                } elseif ("" !== $trimmed) {
                    $non_empty_saved[$key] = $value;
                }
            }

            if (!empty($non_empty_saved)) {
                $content = wp_parse_args($non_empty_saved, $content);
            }
        }
    }

    return $content;
}

/**
 * Get a newline-separated option as an array of trimmed lines.
 *
 * @param string $key     Option key.
 * @param array  $default Fallback lines.
 * @return array
 */
function anna_get_lines_option($key, $default = [])
{
    $value = anna_get_option($key, "");

    if (is_array($value)) {
        $value = implode("\n", $value);
    }

    if (!is_string($value) || "" === trim($value)) {
        return $default;
    }

    $lines = preg_split('/\r\n|\r|\n/', $value);
    $lines = array_map("trim", $lines);
    $lines = array_filter($lines);

    return array_values($lines);
}

/**
 * Output a theme option value (escaped).
 *
 * @param  string $key     Option key.
 * @param  mixed  $default Fallback value.
 */
function anna_option($key, $default = "")
{
    echo esc_html(anna_get_option($key, $default));
}

/**
 * Get the social links array.
 *
 * @return array Associative array of platform => URL.
 */
function anna_get_social_links()
{
    $defaults = [
        "instagram" => "",
        "facebook" => "",
        "linkedin" => "",
        "twitter" => "",
        "youtube" => "",
        "tiktok" => "",
    ];

    $saved = anna_get_option("social_links", []);
    return wp_parse_args($saved, $defaults);
}

/**
 * Render social links as an accessible list.
 *
 * @param string $class Optional CSS class modifier.
 */
function anna_social_links($class = "")
{
    $links = anna_get_social_links();
    $icons = [
        "instagram" =>
            '<svg aria-hidden="true" focusable="false" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>',
        "facebook" =>
            '<svg aria-hidden="true" focusable="false" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
        "linkedin" =>
            '<svg aria-hidden="true" focusable="false" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
        "twitter" =>
            '<svg aria-hidden="true" focusable="false" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.748l7.73-8.835L1.254 2.25H8.08l4.259 5.63zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
        "youtube" =>
            '<svg aria-hidden="true" focusable="false" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23.495 6.205a3.007 3.007 0 0 0-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 0 0 .527 6.205a31.247 31.247 0 0 0-.522 5.805 31.247 31.247 0 0 0 .522 5.783 3.007 3.007 0 0 0 2.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 0 0 2.088-2.088 31.247 31.247 0 0 0 .5-5.783 31.247 31.247 0 0 0-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/></svg>',
        "tiktok" =>
            '<svg aria-hidden="true" focusable="false" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>',
    ];

    $platform_labels = [
        "instagram" => __("Instagram", "anna-baylis"),
        "facebook" => __("Facebook", "anna-baylis"),
        "linkedin" => __("LinkedIn", "anna-baylis"),
        "twitter" => __("X (Twitter)", "anna-baylis"),
        "youtube" => __("YouTube", "anna-baylis"),
        "tiktok" => __("TikTok", "anna-baylis"),
    ];

    $active = array_filter($links);

    if (empty($active)) {
        return;
    }

    $class_attr = $class ? " anna-social-links--" . esc_attr($class) : "";

    echo '<ul class="anna-social-links' .
        $class_attr .
        '" aria-label="' .
        esc_attr__("Social media profiles", "anna-baylis") .
        '">';

    foreach ($active as $platform => $url) {
        if (empty($url)) {
            continue;
        }
        $label = $platform_labels[$platform] ?? ucfirst($platform);
        $icon = $icons[$platform] ?? "";

        echo '<li class="anna-social-links__item">';
        echo '<a href="' .
            esc_url($url) .
            '" class="anna-social-links__link anna-social-links__link--' .
            esc_attr($platform) .
            '" target="_blank" rel="noopener noreferrer" aria-label="' .
            esc_attr(sprintf(__("Follow on %s", "anna-baylis"), $label)) .
            '">';
        echo wp_kses($icon, anna_allowed_svg_tags());
        echo '<span class="anna-sr-only">' . esc_html($label) . "</span>";
        echo "</a>";
        echo "</li>";
    }

    echo "</ul>";
}

/**
 * Return allowed SVG tags for wp_kses.
 *
 * @return array
 */
function anna_allowed_svg_tags()
{
    return [
        "svg" => [
            "aria-hidden" => true,
            "focusable" => true,
            "width" => true,
            "height" => true,
            "viewbox" => true,
            "fill" => true,
            "xmlns" => true,
        ],
        "path" => ["d" => true, "fill" => true],
        "circle" => ["cx" => true, "cy" => true, "r" => true, "fill" => true],
        "rect" => [
            "x" => true,
            "y" => true,
            "width" => true,
            "height" => true,
            "rx" => true,
        ],
        "polyline" => [
            "points" => true,
            "fill" => true,
            "stroke" => true,
            "stroke-width" => true,
        ],
        "line" => [
            "x1" => true,
            "y1" => true,
            "x2" => true,
            "y2" => true,
            "stroke" => true,
            "stroke-width" => true,
        ],
        "g" => ["fill" => true, "stroke" => true],
    ];
}

/**
 * Get star rating HTML.
 *
 * @param  int $rating Rating out of 5.
 * @return string
 */
function anna_star_rating($rating = 5)
{
    $rating = absint($rating);
    $rating = min(5, max(0, $rating));

    $output =
        '<span class="anna-stars" aria-label="' .
        esc_attr(sprintf(__("%d out of 5 stars", "anna-baylis"), $rating)) .
        '" role="img">';
    $output .=
        '<meter class="anna-stars__meter anna-sr-only" min="0" max="5" value="' .
        esc_attr($rating) .
        '">' .
        esc_html($rating) .
        "/5</meter>";

    for ($i = 1; $i <= 5; $i++) {
        $filled =
            $i <= $rating
                ? "anna-stars__star--filled"
                : "anna-stars__star--empty";
        $output .=
            '<span class="anna-stars__star ' .
            $filled .
            '" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M9.60889 1.91642C9.68358 1.76545 9.8374 1.66992 10.0058 1.66992C10.1742 1.66992 10.328 1.76545 10.4027 1.91642L12.3329 5.8275C12.591 6.35005 13.0892 6.71248 13.6657 6.79712L17.9823 7.42905C18.1492 7.45323 18.2879 7.57009 18.34 7.73047C18.3921 7.89086 18.3487 8.06695 18.228 8.18469L15.1062 11.2256C14.6883 11.633 14.4974 12.22 14.5957 12.7954L15.3327 17.0918C15.3621 17.2587 15.2939 17.4277 15.1568 17.5273C15.0198 17.6269 14.838 17.6396 14.6884 17.5599L10.8297 15.5304C10.3136 15.2593 9.69719 15.2593 9.18106 15.5304L5.32314 17.5599C5.17368 17.6391 4.99222 17.6262 4.85545 17.5267C4.71869 17.4272 4.65051 17.2584 4.67973 17.0918L5.41589 12.7962C5.51461 12.2205 5.32366 11.6331 4.90534 11.2256L1.78357 8.18552C1.66184 8.0679 1.61776 7.89116 1.67 7.73013C1.72224 7.56909 1.86166 7.45192 2.02924 7.42821L6.34507 6.79712C6.92222 6.71313 7.42118 6.35058 7.67951 5.8275L9.60889 1.91642Z" fill="#A1C842" stroke="#A1C842" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
</span>';
    }

    $output .= "</span>";
    return $output;
}

/**
 * Check if a section is enabled in admin settings.
 *
 * @param  string $section Section key.
 * @return bool
 */
function anna_section_enabled($section)
{
    $key = "section_" . $section . "_enabled";
    $enabled = anna_get_option($key, true);
    return (bool) $enabled;
}

/**
 * Get the CTA data from options.
 *
 * @param  string $type 'primary' or 'secondary'.
 * @return array
 */
function anna_get_cta($type = "primary")
{
    $defaults = anna_get_default_options();

    return [
        "text" => anna_get_option(
            "cta_" . $type . "_text",
            $defaults["cta_" . $type . "_text"] ?? "",
        ),
        "url" => anna_get_option(
            "cta_" . $type . "_url",
            $defaults["cta_" . $type . "_url"] ?? "#",
        ),
    ];
}

/**
 * Output a CTA button.
 *
 * @param string $type    'primary', 'secondary', or 'ghost'.
 * @param string $text    Override button text.
 * @param string $url     Override URL.
 * @param string $classes Additional CSS classes.
 */
function anna_cta_button(
    $type = "primary",
    $text = "",
    $url = "",
    $classes = "",
) {
    if (!$text) {
        $cta = anna_get_cta($type);
        $text = $cta["text"];
        $url = $cta["url"];
    }

    $modifier = "anna-btn--" . esc_attr($type);
    $class =
        "anna-btn " . $modifier . ($classes ? " " . esc_attr($classes) : "");

    printf(
        '<a href="%s" class="%s">%s</a>',
        esc_url($url),
        esc_attr($class),
        esc_html($text),
    );
}
