<?php
/**
 * Homepage content helpers and edit-page metabox.
 *
 * The homepage now stores editable content in one post meta row, matching the
 * inner-page pattern instead of using one meta row per section.
 *
 * @package Anna_Baylis
 */

if (!defined("ABSPATH")) {
    exit();
}

/**
 * @return string
 */
function anna_get_home_content_meta_key()
{
    return "_anna_content_home_page";
}

/**
 * Check whether a post is the Anna homepage content page.
 *
 * @param int|WP_Post $post Post object or ID.
 * @return bool
 */
function anna_is_home_content_page($post)
{
    $post = get_post($post);
    if (!$post || "page" !== $post->post_type) {
        return false;
    }

    return "home" === $post->post_name || absint(get_option("page_on_front")) === absint($post->ID);
}

/**
 * Return homepage content saved in the single meta row.
 *
 * @param int $post_id Page ID.
 * @return array<string, mixed>
 */
function anna_get_home_page_saved_content($post_id)
{
    $post_id = absint($post_id);
    if (!$post_id) {
        return [];
    }

    $saved = get_post_meta($post_id, anna_get_home_content_meta_key(), true);

    return is_array($saved) ? $saved : [];
}

/**
 * Return one saved homepage section from the single meta row.
 *
 * @param int    $post_id Page ID.
 * @param string $section Section key.
 * @return array<string, mixed>|null
 */
function anna_get_home_page_section_content($post_id, $section)
{
    $saved = anna_get_home_page_saved_content($post_id);
    if (empty($saved) || !isset($saved[$section]) || !is_array($saved[$section])) {
        return null;
    }

    return $saved[$section];
}

/**
 * Return saved homepage section enabled state.
 *
 * @param int    $post_id Page ID.
 * @param string $section Section key.
 * @return bool|null
 */
function anna_get_home_page_section_enabled($post_id, $section)
{
    $saved = anna_get_home_page_saved_content($post_id);
    if (empty($saved) || !isset($saved["sections"]) || !is_array($saved["sections"])) {
        return null;
    }

    if (!array_key_exists($section, $saved["sections"])) {
        return null;
    }

    return (bool) $saved["sections"][$section];
}

/**
 * Apply a saved homepage section to the already-built frontend content array.
 *
 * @param array<string, mixed> $content Base content.
 * @param array<string, mixed> $saved Saved section data.
 * @param string               $section Section key.
 * @return array<string, mixed>
 */
function anna_apply_home_page_section_content($content, $saved, $section)
{
    foreach ($saved as $key => $value) {
        if (is_array($value)) {
            continue;
        }

        $value = (string) $value;
        if ("" === trim($value) && !anna_is_intentionally_blank($value)) {
            continue;
        }

        $value = anna_is_intentionally_blank($value) ? "" : $value;

        switch ($section) {
            case "hero":
                if ("image_id" === $key) {
                    $content["image_id"] = absint($value);
                } elseif ("heading" === $key) {
                    $content["heading"] = nl2br($value);
                } elseif ("primary_button_text" === $key) {
                    $content["primary_cta"]["text"] = $value;
                } elseif ("primary_button_url" === $key) {
                    $content["primary_cta"]["url"] = $value;
                } elseif ("secondary_button_text" === $key) {
                    $content["secondary_cta"]["text"] = $value;
                } elseif ("secondary_button_url" === $key) {
                    $content["secondary_cta"]["url"] = $value;
                } elseif (isset($content[$key])) {
                    $content[$key] = $value;
                }
                break;

            case "intro":
                if ("recognition_items_text" === $key) {
                    $items = preg_split('/\r\n|\r|\n/', $value);
                    $content["recognition_items"] = array_values(array_filter(array_map("trim", $items ?: [])));
                } elseif (isset($content[$key])) {
                    $content[$key] = $value;
                }
                break;

            case "about":
                if ("image_id" === $key) {
                    $content["image_id"] = absint($value);
                } elseif ("expertise_text" === $key) {
                    $items = preg_split('/\r\n|\r|\n/', $value);
                    $content["expertise"] = array_values(array_filter(array_map("trim", $items ?: [])));
                } elseif (isset($content[$key])) {
                    $content[$key] = $value;
                }
                break;

            case "cta":
                if ("primary_button_text" === $key) {
                    $content["primary_cta"]["text"] = $value;
                } elseif ("primary_button_url" === $key) {
                    $content["primary_cta"]["url"] = $value;
                } elseif ("secondary_button_text" === $key) {
                    $content["secondary_cta"]["text"] = $value;
                } elseif ("secondary_button_url" === $key) {
                    $content["secondary_cta"]["url"] = $value;
                } elseif (isset($content[$key])) {
                    $content[$key] = $value;
                }
                break;

            default:
                if (isset($content[$key])) {
                    $content[$key] = $value;
                }
                break;
        }
    }

    if ("hero" === $section) {
        $stats = [];
        for ($i = 1; $i <= 3; $i++) {
            $value = (string) ($saved["stat_" . $i . "_value"] ?? "");
            $label = (string) ($saved["stat_" . $i . "_label"] ?? "");
            if ("" !== trim($value) || "" !== trim($label)) {
                $stats[] = ["value" => $value, "label" => $label];
            }
        }
        if (!empty($stats)) {
            $content["stats"] = $stats;
        }
    }

    return $content;
}

/**
 * Build admin field defaults from current frontend values for easy migration.
 *
 * @param int $post_id Page ID.
 * @return array<string, mixed>
 */
function anna_get_home_page_content_for_admin($post_id)
{
    $saved   = anna_get_home_page_saved_content($post_id);
    $opts    = anna_get_default_options();

    // Full defaults — used both when no saved data exists AND to fill in
    // any keys missing from an existing (but incomplete) saved row.
    $defaults = [
        "hero" => [
            "eyebrow"              => anna_get_option("hero_eyebrow",          $opts["hero_eyebrow"]          ?? ""),
            "heading"              => anna_get_option("hero_heading",           $opts["hero_heading"]           ?? ""),
            "description"          => anna_get_option("hero_description",       $opts["hero_description"]       ?? ""),
            "trust_text"           => anna_get_option("hero_trust_text",        $opts["hero_trust_text"]        ?? ""),
            "image_id"             => absint(anna_get_option("hero_image_id",   $opts["hero_image_id"]         ?? 0)),
            "primary_button_text"  => anna_get_option("cta_primary_text",       $opts["cta_primary_text"]       ?? ""),
            "primary_button_url"   => anna_get_option("cta_primary_url",        $opts["cta_primary_url"]        ?? ""),
            "secondary_button_text"=> anna_get_option("cta_secondary_text",     $opts["cta_secondary_text"]     ?? ""),
            "secondary_button_url" => anna_get_option("cta_secondary_url",      $opts["cta_secondary_url"]      ?? ""),
            "stat_1_value"         => anna_get_option("stat_1_value",           $opts["stat_1_value"]           ?? ""),
            "stat_1_label"         => anna_get_option("stat_1_label",           $opts["stat_1_label"]           ?? ""),
            "stat_2_value"         => anna_get_option("stat_2_value",           $opts["stat_2_value"]           ?? ""),
            "stat_2_label"         => anna_get_option("stat_2_label",           $opts["stat_2_label"]           ?? ""),
            "stat_3_value"         => anna_get_option("stat_3_value",           $opts["stat_3_value"]           ?? ""),
            "stat_3_label"         => anna_get_option("stat_3_label",           $opts["stat_3_label"]           ?? ""),
        ],
        "intro" => [
            "intro_eyebrow"           => anna_get_option("intro_eyebrow",           $opts["intro_eyebrow"]           ?? ""),
            "intro_heading"           => anna_get_option("intro_heading",           $opts["intro_heading"]           ?? ""),
            "intro_body"              => anna_get_option("intro_body",              $opts["intro_body"]              ?? ""),
            "intro_quote"             => anna_get_option("intro_quote",             $opts["intro_quote"]             ?? ""),
            "intro_quote_cite"        => anna_get_option("intro_quote_cite",        $opts["intro_quote_cite"]        ?? ""),
            "recognition_eyebrow"     => anna_get_option("recognition_eyebrow",     $opts["recognition_eyebrow"]     ?? ""),
            "recognition_heading"     => anna_get_option("recognition_heading",     $opts["recognition_heading"]     ?? ""),
            "recognition_description" => anna_get_option("recognition_description", $opts["recognition_description"] ?? ""),
            "recognition_items_text"  => anna_get_option("recognition_items_text",  $opts["recognition_items_text"]  ?? ""),
        ],
        "services" => [
            "eyebrow"        => anna_get_option("services_eyebrow",     $opts["services_eyebrow"]     ?? ""),
            "heading"        => anna_get_option("services_heading",     $opts["services_heading"]     ?? "What's the change you're needing?"),
            "description"    => anna_get_option("services_description", $opts["services_description"] ?? ""),
            "cta_text"       => anna_get_option("services_cta_text",    $opts["services_cta_text"]    ?? ""),
            "cta_url"        => anna_get_option("services_cta_url",     $opts["services_cta_url"]     ?? ""),
            "bg_image_id"    => 0,
            "card_1_title"   => "1-1 Life Coaching",
            "card_1_excerpt" => "Deep, personalised work using a bottom-up approach that accesses the subconscious through the body and the nervous system. We get to the root of what is actually running underneath and change it.",
            "card_1_link"    => "Find out more",
            "card_1_url"     => "",
            "card_1_image_id"=> 0,
            "card_2_title"   => "Oasis Community",
            "card_2_excerpt" => "A womens wellness community for sustainable health and wellbeing. Ongoing live guidance, daily practices, guided movement, nutrition, meditation, breathwork and community connection. A space to come back to yourself week after week.",
            "card_2_link"    => "Find out more",
            "card_2_url"     => "",
            "card_2_image_id"=> 0,
            "card_3_title"   => "Speaking and Workshops",
            "card_3_excerpt" => "Keynotes and interactive sessions for conferences, corporate events and womens gatherings. Drawing on Olympic experience, deep coaching expertise and lived transformation. Topics include stress and the nervous system, building resilience, the mind-body connection and more.",
            "card_3_link"    => "Enquire about speaking",
            "card_3_url"     => "",
            "card_3_image_id"=> 0,
        ],
        "about" => [
            "eyebrow"        => anna_get_option("about_eyebrow",        $opts["about_eyebrow"]        ?? ""),
            "heading"        => anna_get_option("about_heading",        $opts["about_heading"]        ?? ""),
            "body"           => anna_get_option("about_body",           $opts["about_body"]           ?? ""),
            "quote"          => anna_get_option("about_quote",          $opts["about_quote"]          ?? ""),
            "image_id"       => absint(anna_get_option("about_image_id",$opts["about_image_id"]       ?? 0)),
            "badge_number"   => anna_get_option("about_badge_number",   $opts["about_badge_number"]   ?? ""),
            "badge_text"     => anna_get_option("about_badge_text",     $opts["about_badge_text"]     ?? ""),
            "expertise_text" => anna_get_option("about_expertise_text", $opts["about_expertise_text"] ?? ""),
            "cta_text"       => anna_get_option("about_cta_text",       $opts["about_cta_text"]       ?? ""),
            "cta_url"        => anna_get_option("about_cta_url",        $opts["about_cta_url"]        ?? ""),
        ],
        "testimonials" => [
            "eyebrow"   => anna_get_option("testimonials_eyebrow",  $opts["testimonials_eyebrow"]  ?? ""),
            "heading"   => anna_get_option("testimonials_heading",  $opts["testimonials_heading"]  ?? ""),
            "summary"   => anna_get_option("testimonials_summary",  $opts["testimonials_summary"]  ?? ""),
            "cta_text"  => anna_get_option("testimonials_cta_text", $opts["testimonials_cta_text"] ?? ""),
            "cta_url"   => anna_get_option("testimonials_cta_url",  $opts["testimonials_cta_url"]  ?? ""),
            "shortcode" => "",
        ],
        "cta" => [
            "eyebrow"               => anna_get_option("cta_eyebrow",        $opts["cta_eyebrow"]        ?? ""),
            "heading"               => anna_get_option("cta_heading",        $opts["cta_heading"]        ?? ""),
            "description"           => anna_get_option("cta_description",    $opts["cta_description"]    ?? ""),
            "trust_text"            => anna_get_option("cta_trust",          $opts["cta_trust"]          ?? ""),
            "primary_button_text"   => anna_get_option("cta_primary_text",   $opts["cta_primary_text"]   ?? ""),
            "primary_button_url"    => anna_get_option("cta_primary_url",    $opts["cta_primary_url"]    ?? ""),
            "secondary_button_text" => anna_get_option("cta_secondary_text", $opts["cta_secondary_text"] ?? ""),
            "secondary_button_url"  => anna_get_option("cta_secondary_url",  $opts["cta_secondary_url"]  ?? ""),
        ],
    ];

    if ( empty( $saved ) ) {
        return $defaults;
    }

    // Saved row exists — merge defaults into each section so newly-added
    // fields (like card_* and bg_image_id) always show their default values
    // instead of blank when they haven't been saved yet.
    foreach ( $defaults as $section => $section_defaults ) {
        if ( ! isset( $saved[ $section ] ) || ! is_array( $saved[ $section ] ) ) {
            $saved[ $section ] = $section_defaults;
            continue;
        }
        foreach ( $section_defaults as $key => $default_value ) {
            if ( ! array_key_exists( $key, $saved[ $section ] ) || $saved[ $section ][ $key ] === '' || $saved[ $section ][ $key ] === 0 ) {
                $saved[ $section ][ $key ] = $default_value;
            }
        }
    }

    return $saved;
}

/**
 * Register homepage content metabox.
 *
 * @param WP_Post $post Current post.
 */
function anna_register_home_page_content_meta_box($post)
{
    if (!anna_is_home_content_page($post)) {
        return;
    }

    add_meta_box(
        "anna_home_page_content",
        __("Anna Home Page Content", "anna-baylis"),
        "anna_render_home_page_content_meta_box",
        "page",
        "normal",
        "high",
    );
}
add_action("add_meta_boxes_page", "anna_register_home_page_content_meta_box", 20);

/**
 * Render homepage content metabox.
 *
 * @param WP_Post $post Current post.
 */
function anna_render_home_page_content_meta_box($post)
{
    $content = anna_get_home_page_content_for_admin($post->ID);
    wp_nonce_field("anna_save_home_page_content", "anna_home_page_content_nonce");
    ?>
    <div class="anna-home-admin-fields">
        <p class="description"><?php esc_html_e("Homepage content is saved in one database meta row. Use the normal Update button to save.", "anna-baylis"); ?></p>
        <table class="form-table" role="presentation"><tbody>
            <tr><td colspan="2"><h3><?php esc_html_e("Hero", "anna-baylis"); ?></h3></td></tr>
            <?php anna_home_admin_text("hero", "eyebrow", __("Eyebrow", "anna-baylis"), $content); ?>
            <?php anna_home_admin_textarea("hero", "heading", __("Heading", "anna-baylis"), $content, 3); ?>
            <?php anna_home_admin_textarea("hero", "description", __("Description", "anna-baylis"), $content, 3); ?>
            <?php anna_home_admin_text("hero", "trust_text", __("Trust text", "anna-baylis"), $content); ?>
            <?php anna_home_admin_media("hero", "image_id", __("Hero image", "anna-baylis"), $content); ?>
            <?php anna_home_admin_text("hero", "primary_button_text", __("Primary button text", "anna-baylis"), $content); ?>
            <?php anna_home_admin_text("hero", "primary_button_url", __("Primary button URL", "anna-baylis"), $content); ?>
            <?php anna_home_admin_text("hero", "secondary_button_text", __("Secondary button text", "anna-baylis"), $content); ?>
            <?php anna_home_admin_text("hero", "secondary_button_url", __("Secondary button URL", "anna-baylis"), $content); ?>
            <?php for ($i = 1; $i <= 3; $i++): ?>
                <?php anna_home_admin_text("hero", "stat_" . $i . "_value", sprintf(__("Stat %d value", "anna-baylis"), $i), $content); ?>
                <?php anna_home_admin_text("hero", "stat_" . $i . "_label", sprintf(__("Stat %d label", "anna-baylis"), $i), $content); ?>
            <?php endfor; ?>

            <tr><td colspan="2"><h3><?php esc_html_e("Intro / Recognition", "anna-baylis"); ?></h3></td></tr>
            <?php anna_home_admin_text("intro", "intro_eyebrow", __("Intro eyebrow", "anna-baylis"), $content); ?>
            <?php anna_home_admin_textarea("intro", "intro_heading", __("Intro heading", "anna-baylis"), $content, 3); ?>
            <?php anna_home_admin_textarea("intro", "intro_body", __("Intro body", "anna-baylis"), $content, 6); ?>
            <?php anna_home_admin_textarea("intro", "intro_quote", __("Intro quote", "anna-baylis"), $content, 3); ?>
            <?php anna_home_admin_text("intro", "intro_quote_cite", __("Intro quote cite", "anna-baylis"), $content); ?>
            <?php anna_home_admin_text("intro", "recognition_eyebrow", __("Recognition eyebrow", "anna-baylis"), $content); ?>
            <?php anna_home_admin_text("intro", "recognition_heading", __("Recognition heading", "anna-baylis"), $content); ?>
            <?php anna_home_admin_textarea("intro", "recognition_description", __("Recognition description", "anna-baylis"), $content, 3); ?>
            <?php anna_home_admin_textarea("intro", "recognition_items_text", __("Recognition items", "anna-baylis"), $content, 6, __("One item per line.", "anna-baylis")); ?>

            <tr><td colspan="2"><h3><?php esc_html_e("Services", "anna-baylis"); ?></h3></td></tr>
            <?php anna_home_admin_text("services", "eyebrow", __("Eyebrow", "anna-baylis"), $content); ?>
            <?php anna_home_admin_text("services", "heading", __("Heading", "anna-baylis"), $content); ?>
            <?php anna_home_admin_textarea("services", "description", __("Description", "anna-baylis"), $content, 3); ?>
            <?php anna_home_admin_text("services", "cta_text", __("CTA text", "anna-baylis"), $content); ?>
            <?php anna_home_admin_text("services", "cta_url", __("CTA URL", "anna-baylis"), $content); ?>
            <?php anna_home_admin_media("services", "bg_image_id", __("Background Image", "anna-baylis"), $content); ?>

            <tr><td colspan="2"><h4 style="margin:8px 0 4px"><?php esc_html_e("Card 1", "anna-baylis"); ?></h4></td></tr>
            <?php anna_home_admin_text("services", "card_1_title", __("Card 1 Title", "anna-baylis"), $content); ?>
            <?php anna_home_admin_textarea("services", "card_1_excerpt", __("Card 1 Excerpt", "anna-baylis"), $content, 3); ?>
            <?php anna_home_admin_text("services", "card_1_link", __("Card 1 Link text", "anna-baylis"), $content); ?>
            <?php anna_home_admin_text("services", "card_1_url", __("Card 1 URL", "anna-baylis"), $content); ?>
            <?php anna_home_admin_media("services", "card_1_image_id", __("Card 1 Background Image", "anna-baylis"), $content); ?>

            <tr><td colspan="2"><h4 style="margin:8px 0 4px"><?php esc_html_e("Card 2", "anna-baylis"); ?></h4></td></tr>
            <?php anna_home_admin_text("services", "card_2_title", __("Card 2 Title", "anna-baylis"), $content); ?>
            <?php anna_home_admin_textarea("services", "card_2_excerpt", __("Card 2 Excerpt", "anna-baylis"), $content, 3); ?>
            <?php anna_home_admin_text("services", "card_2_link", __("Card 2 Link text", "anna-baylis"), $content); ?>
            <?php anna_home_admin_text("services", "card_2_url", __("Card 2 URL", "anna-baylis"), $content); ?>
            <?php anna_home_admin_media("services", "card_2_image_id", __("Card 2 Background Image", "anna-baylis"), $content); ?>

            <tr><td colspan="2"><h4 style="margin:8px 0 4px"><?php esc_html_e("Card 3", "anna-baylis"); ?></h4></td></tr>
            <?php anna_home_admin_text("services", "card_3_title", __("Card 3 Title", "anna-baylis"), $content); ?>
            <?php anna_home_admin_textarea("services", "card_3_excerpt", __("Card 3 Excerpt", "anna-baylis"), $content, 3); ?>
            <?php anna_home_admin_text("services", "card_3_link", __("Card 3 Link text", "anna-baylis"), $content); ?>
            <?php anna_home_admin_text("services", "card_3_url", __("Card 3 URL", "anna-baylis"), $content); ?>
            <?php anna_home_admin_media("services", "card_3_image_id", __("Card 3 Background Image", "anna-baylis"), $content); ?>

            <tr><td colspan="2"><h3><?php esc_html_e("About", "anna-baylis"); ?></h3></td></tr>
            <?php anna_home_admin_text("about", "eyebrow", __("Eyebrow", "anna-baylis"), $content); ?>
            <?php anna_home_admin_textarea("about", "heading", __("Heading", "anna-baylis"), $content, 3); ?>
            <?php anna_home_admin_textarea("about", "body", __("Body", "anna-baylis"), $content, 6); ?>
            <?php anna_home_admin_textarea("about", "quote", __("Quote", "anna-baylis"), $content, 3); ?>
            <?php anna_home_admin_media("about", "image_id", __("About image", "anna-baylis"), $content); ?>
            <?php anna_home_admin_text("about", "badge_number", __("Badge number", "anna-baylis"), $content); ?>
            <?php anna_home_admin_text("about", "badge_text", __("Badge text", "anna-baylis"), $content); ?>
            <?php anna_home_admin_textarea("about", "expertise_text", __("Expertise items", "anna-baylis"), $content, 5, __("One item per line.", "anna-baylis")); ?>
            <?php anna_home_admin_text("about", "cta_text", __("CTA text", "anna-baylis"), $content); ?>
            <?php anna_home_admin_text("about", "cta_url", __("CTA URL", "anna-baylis"), $content); ?>

            <tr><td colspan="2"><h3><?php esc_html_e("Testimonials", "anna-baylis"); ?></h3></td></tr>
            <?php anna_home_admin_text("testimonials", "eyebrow", __("Eyebrow", "anna-baylis"), $content); ?>
            <?php anna_home_admin_text("testimonials", "heading", __("Heading", "anna-baylis"), $content); ?>
            <?php anna_home_admin_textarea("testimonials", "summary", __("Summary", "anna-baylis"), $content, 3); ?>
            <?php anna_home_admin_text("testimonials", "cta_text", __("CTA text", "anna-baylis"), $content); ?>
            <?php anna_home_admin_text("testimonials", "cta_url", __("CTA URL", "anna-baylis"), $content); ?>
            <?php anna_home_admin_text(
                "testimonials",
                "shortcode",
                __("Reviews shortcode", "anna-baylis"),
                $content,
                sprintf(
                    /* translators: %s: link to Reviews Bundle collections admin page */
                    __('Shortcode from the Reviews Bundle plugin — e.g. <code>[brb_collection id="123"]</code>. Manage collections: <a href="%s" target="_blank">Reviews Bundle</a>.', "anna-baylis"),
                    esc_url(admin_url("edit.php?post_type=brb_collection"))
                )
            ); ?>

            <tr><td colspan="2"><h3><?php esc_html_e("Final CTA", "anna-baylis"); ?></h3></td></tr>
            <?php anna_home_admin_text("cta", "eyebrow", __("Eyebrow", "anna-baylis"), $content); ?>
            <?php anna_home_admin_textarea("cta", "heading", __("Heading", "anna-baylis"), $content, 3); ?>
            <?php anna_home_admin_textarea("cta", "description", __("Description", "anna-baylis"), $content, 3); ?>
            <?php anna_home_admin_text("cta", "trust_text", __("Trust text", "anna-baylis"), $content); ?>
            <?php anna_home_admin_text("cta", "primary_button_text", __("Primary button text", "anna-baylis"), $content); ?>
            <?php anna_home_admin_text("cta", "primary_button_url", __("Primary button URL", "anna-baylis"), $content); ?>
            <?php anna_home_admin_text("cta", "secondary_button_text", __("Secondary button text", "anna-baylis"), $content); ?>
            <?php anna_home_admin_text("cta", "secondary_button_url", __("Secondary button URL", "anna-baylis"), $content); ?>
        </tbody></table>
    </div>
    <?php
}

/** Render homepage admin text input. */
function anna_home_admin_text($section, $key, $label, $content, $description = "")
{
    $id = "anna-home-" . $section . "-" . $key;
    $value = (string) ($content[$section][$key] ?? "");
    ?>
    <tr><th scope="row"><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label></th><td><input type="text" class="regular-text" id="<?php echo esc_attr($id); ?>" name="anna_home_page_content[<?php echo esc_attr($section); ?>][<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($value); ?>"><?php if ($description): ?><p class="description"><?php echo wp_kses($description, ['a' => ['href' => [], 'target' => []], 'code' => []]); ?></p><?php endif; ?></td></tr>
    <?php
}

/** Render homepage admin textarea. */
function anna_home_admin_textarea($section, $key, $label, $content, $rows = 4, $description = "")
{
    $id = "anna-home-" . $section . "-" . $key;
    $value = (string) ($content[$section][$key] ?? "");
    ?>
    <tr><th scope="row"><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label></th><td><textarea class="large-text" rows="<?php echo esc_attr($rows); ?>" id="<?php echo esc_attr($id); ?>" name="anna_home_page_content[<?php echo esc_attr($section); ?>][<?php echo esc_attr($key); ?>]"><?php echo esc_textarea($value); ?></textarea><?php if ($description): ?><p class="description"><?php echo esc_html($description); ?></p><?php endif; ?></td></tr>
    <?php
}

/** Render homepage admin checkbox. */
function anna_home_admin_checkbox($section, $key, $label, $content)
{
    $id = "anna-home-" . $section . "-" . $key;
    $checked = !empty($content[$section][$key]);
    ?>
    <tr><th scope="row"><?php echo esc_html($label); ?></th><td><label for="<?php echo esc_attr($id); ?>"><input type="checkbox" id="<?php echo esc_attr($id); ?>" name="anna_home_page_content[<?php echo esc_attr($section); ?>][<?php echo esc_attr($key); ?>]" value="1" <?php checked($checked); ?>> <?php esc_html_e("Show section", "anna-baylis"); ?></label></td></tr>
    <?php
}

/** Render homepage admin media field. */
function anna_home_admin_media($section, $key, $label, $content)
{
    $id = "anna-home-" . $section . "-" . $key;
    $preview_id = $id . "-preview";
    $value = absint($content[$section][$key] ?? 0);
    $image_url = $value ? wp_get_attachment_image_url($value, "thumbnail") : "";
    ?>
    <tr><th scope="row"><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label></th><td><div class="anna-media-field"><input type="hidden" id="<?php echo esc_attr($id); ?>" name="anna_home_page_content[<?php echo esc_attr($section); ?>][<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($value); ?>"><div class="anna-media-preview" id="<?php echo esc_attr($preview_id); ?>"><?php if ($image_url): ?><img src="<?php echo esc_url($image_url); ?>" alt="" style="max-width:150px;height:auto;border-radius:8px;"><?php endif; ?></div><button type="button" class="button anna-content-media-select" data-target="<?php echo esc_attr($id); ?>" data-preview="<?php echo esc_attr($preview_id); ?>"><?php esc_html_e("Select Image", "anna-baylis"); ?></button> <button type="button" class="button anna-content-media-remove" data-target="<?php echo esc_attr($id); ?>" data-preview="<?php echo esc_attr($preview_id); ?>" <?php echo !$value ? 'style="display:none;"' : ''; ?>><?php esc_html_e("Remove", "anna-baylis"); ?></button></div></td></tr>
    <?php
}

/**
 * Save homepage content metabox.
 *
 * @param int $post_id Current post ID.
 */
function anna_save_home_page_content_meta_box($post_id)
{
    if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE) {
        return;
    }
    if (!isset($_POST["anna_home_page_content_nonce"])) {
        return;
    }

    $nonce = sanitize_text_field(wp_unslash($_POST["anna_home_page_content_nonce"]));
    if (!wp_verify_nonce($nonce, "anna_save_home_page_content")) {
        return;
    }

    if (!current_user_can("edit_post", $post_id) || !anna_is_home_content_page($post_id)) {
        return;
    }

    $raw = isset($_POST["anna_home_page_content"]) && is_array($_POST["anna_home_page_content"])
        ? wp_unslash($_POST["anna_home_page_content"])
        : [];

    $schema = [
        "hero" => ["eyebrow", "heading", "description", "trust_text", "image_id", "primary_button_text", "primary_button_url", "secondary_button_text", "secondary_button_url", "stat_1_value", "stat_1_label", "stat_2_value", "stat_2_label", "stat_3_value", "stat_3_label"],
        "intro" => ["intro_eyebrow", "intro_heading", "intro_body", "intro_quote", "intro_quote_cite", "recognition_eyebrow", "recognition_heading", "recognition_description", "recognition_items_text"],
        "services" => ["eyebrow", "heading", "description", "cta_text", "cta_url", "bg_image_id",
            "card_1_title", "card_1_excerpt", "card_1_link", "card_1_url", "card_1_image_id",
            "card_2_title", "card_2_excerpt", "card_2_link", "card_2_url", "card_2_image_id",
            "card_3_title", "card_3_excerpt", "card_3_link", "card_3_url", "card_3_image_id",
        ],
        "about" => ["eyebrow", "heading", "body", "quote", "image_id", "badge_number", "badge_text", "expertise_text", "cta_text", "cta_url"],
        "testimonials" => ["eyebrow", "heading", "summary", "cta_text", "cta_url", "shortcode"],
        "cta" => ["eyebrow", "heading", "description", "trust_text", "primary_button_text", "primary_button_url", "secondary_button_text", "secondary_button_url"],
    ];
    $textarea_keys = ["heading", "description", "intro_heading", "intro_body", "intro_quote", "recognition_description", "recognition_items_text", "body", "quote", "expertise_text", "summary", "card_1_excerpt", "card_2_excerpt", "card_3_excerpt"];
    $url_keys = ["primary_button_url", "secondary_button_url", "cta_url", "card_1_url", "card_2_url", "card_3_url"];

    $saved = [];
    foreach ($schema as $section => $keys) {
        $saved[$section] = [];
        foreach ($keys as $key) {
            $value = isset($raw[$section][$key]) ? (string) $raw[$section][$key] : "";
            if (str_ends_with($key, "_id")) {
                $saved[$section][$key] = absint($value);
            } elseif (in_array($key, $url_keys, true)) {
                $saved[$section][$key] = esc_url_raw($value);
            } elseif (in_array($key, $textarea_keys, true)) {
                $saved[$section][$key] = sanitize_textarea_field($value);
            } else {
                $saved[$section][$key] = sanitize_text_field($value);
            }
        }
    }

    update_post_meta($post_id, anna_get_home_content_meta_key(), $saved);
}
add_action("save_post_page", "anna_save_home_page_content_meta_box");

/**
 * Enqueue media library and content manager JS for the home page edit screen.
 *
 * @param string $hook Current admin page hook.
 */
function anna_home_page_admin_enqueue( $hook ) {
    if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) {
        return;
    }

    $screen = get_current_screen();
    if ( ! $screen || 'page' !== $screen->post_type ) {
        return;
    }

    $post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
    if ( ! $post_id || ! anna_is_home_content_page( $post_id ) ) {
        return;
    }

    wp_enqueue_media();

    // Reuse the content manager plugin JS — it already handles
    // .anna-content-media-select and .anna-content-media-remove.
    if ( defined( 'ANNA_CONTENT_MANAGER_URL' ) && defined( 'ANNA_CONTENT_MANAGER_DIR' ) ) {
        $js_path = ANNA_CONTENT_MANAGER_DIR . 'assets/js/admin-page-content.js';
        wp_enqueue_script(
            'anna-content-manager-admin',
            ANNA_CONTENT_MANAGER_URL . 'assets/js/admin-page-content.js',
            [ 'jquery' ],
            file_exists( $js_path ) ? (string) filemtime( $js_path ) : null,
            true
        );
    }
}
add_action( 'admin_enqueue_scripts', 'anna_home_page_admin_enqueue' );
