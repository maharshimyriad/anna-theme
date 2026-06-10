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
    $saved = anna_get_home_page_saved_content($post_id);
    if (!empty($saved)) {
        return $saved;
    }

    $defaults = anna_get_default_options();

    return [
        "sections" => [
            "hero" => (bool) anna_get_option("section_hero_enabled", $defaults["section_hero_enabled"] ?? true),
            "intro" => (bool) anna_get_option("section_intro_enabled", $defaults["section_intro_enabled"] ?? true),
            "recognition" => (bool) anna_get_option("section_recognition_enabled", $defaults["section_recognition_enabled"] ?? true),
            "services" => (bool) anna_get_option("section_services_enabled", $defaults["section_services_enabled"] ?? true),
            "about" => (bool) anna_get_option("section_about_enabled", $defaults["section_about_enabled"] ?? true),
            "testimonials" => (bool) anna_get_option("section_testimonials_enabled", $defaults["section_testimonials_enabled"] ?? true),
            "cta" => (bool) anna_get_option("section_cta_enabled", $defaults["section_cta_enabled"] ?? true),
        ],
        "hero" => [
            "eyebrow" => anna_get_option("hero_eyebrow", $defaults["hero_eyebrow"] ?? ""),
            "heading" => anna_get_option("hero_heading", $defaults["hero_heading"] ?? ""),
            "description" => anna_get_option("hero_description", $defaults["hero_description"] ?? ""),
            "trust_text" => anna_get_option("hero_trust_text", $defaults["hero_trust_text"] ?? ""),
            "image_id" => absint(anna_get_option("hero_image_id", $defaults["hero_image_id"] ?? 0)),
            "primary_button_text" => anna_get_option("cta_primary_text", $defaults["cta_primary_text"] ?? ""),
            "primary_button_url" => anna_get_option("cta_primary_url", $defaults["cta_primary_url"] ?? ""),
            "secondary_button_text" => anna_get_option("cta_secondary_text", $defaults["cta_secondary_text"] ?? ""),
            "secondary_button_url" => anna_get_option("cta_secondary_url", $defaults["cta_secondary_url"] ?? ""),
            "stat_1_value" => anna_get_option("stat_1_value", $defaults["stat_1_value"] ?? ""),
            "stat_1_label" => anna_get_option("stat_1_label", $defaults["stat_1_label"] ?? ""),
            "stat_2_value" => anna_get_option("stat_2_value", $defaults["stat_2_value"] ?? ""),
            "stat_2_label" => anna_get_option("stat_2_label", $defaults["stat_2_label"] ?? ""),
            "stat_3_value" => anna_get_option("stat_3_value", $defaults["stat_3_value"] ?? ""),
            "stat_3_label" => anna_get_option("stat_3_label", $defaults["stat_3_label"] ?? ""),
        ],
        "intro" => [
            "intro_eyebrow" => anna_get_option("intro_eyebrow", $defaults["intro_eyebrow"] ?? ""),
            "intro_heading" => anna_get_option("intro_heading", $defaults["intro_heading"] ?? ""),
            "intro_body" => anna_get_option("intro_body", $defaults["intro_body"] ?? ""),
            "intro_quote" => anna_get_option("intro_quote", $defaults["intro_quote"] ?? ""),
            "intro_quote_cite" => anna_get_option("intro_quote_cite", $defaults["intro_quote_cite"] ?? ""),
            "recognition_eyebrow" => anna_get_option("recognition_eyebrow", $defaults["recognition_eyebrow"] ?? ""),
            "recognition_heading" => anna_get_option("recognition_heading", $defaults["recognition_heading"] ?? ""),
            "recognition_description" => anna_get_option("recognition_description", $defaults["recognition_description"] ?? ""),
            "recognition_items_text" => anna_get_option("recognition_items_text", $defaults["recognition_items_text"] ?? ""),
        ],
        "services" => [
            "eyebrow" => anna_get_option("services_eyebrow", $defaults["services_eyebrow"] ?? ""),
            "heading" => anna_get_option("services_heading", $defaults["services_heading"] ?? ""),
            "description" => anna_get_option("services_description", $defaults["services_description"] ?? ""),
            "cta_text" => anna_get_option("services_cta_text", $defaults["services_cta_text"] ?? ""),
            "cta_url" => anna_get_option("services_cta_url", $defaults["services_cta_url"] ?? ""),
        ],
        "about" => [
            "eyebrow" => anna_get_option("about_eyebrow", $defaults["about_eyebrow"] ?? ""),
            "heading" => anna_get_option("about_heading", $defaults["about_heading"] ?? ""),
            "body" => anna_get_option("about_body", $defaults["about_body"] ?? ""),
            "quote" => anna_get_option("about_quote", $defaults["about_quote"] ?? ""),
            "image_id" => absint(anna_get_option("about_image_id", $defaults["about_image_id"] ?? 0)),
            "badge_number" => anna_get_option("about_badge_number", $defaults["about_badge_number"] ?? ""),
            "badge_text" => anna_get_option("about_badge_text", $defaults["about_badge_text"] ?? ""),
            "expertise_text" => anna_get_option("about_expertise_text", $defaults["about_expertise_text"] ?? ""),
            "cta_text" => anna_get_option("about_cta_text", $defaults["about_cta_text"] ?? ""),
            "cta_url" => anna_get_option("about_cta_url", $defaults["about_cta_url"] ?? ""),
        ],
        "testimonials" => [
            "eyebrow" => anna_get_option("testimonials_eyebrow", $defaults["testimonials_eyebrow"] ?? ""),
            "heading" => anna_get_option("testimonials_heading", $defaults["testimonials_heading"] ?? ""),
            "summary" => anna_get_option("testimonials_summary", $defaults["testimonials_summary"] ?? ""),
            "cta_text" => anna_get_option("testimonials_cta_text", $defaults["testimonials_cta_text"] ?? ""),
            "cta_url" => anna_get_option("testimonials_cta_url", $defaults["testimonials_cta_url"] ?? ""),
        ],
        "cta" => [
            "eyebrow" => anna_get_option("cta_eyebrow", $defaults["cta_eyebrow"] ?? ""),
            "heading" => anna_get_option("cta_heading", $defaults["cta_heading"] ?? ""),
            "description" => anna_get_option("cta_description", $defaults["cta_description"] ?? ""),
            "trust_text" => anna_get_option("cta_trust", $defaults["cta_trust"] ?? ""),
            "primary_button_text" => anna_get_option("cta_primary_text", $defaults["cta_primary_text"] ?? ""),
            "primary_button_url" => anna_get_option("cta_primary_url", $defaults["cta_primary_url"] ?? ""),
            "secondary_button_text" => anna_get_option("cta_secondary_text", $defaults["cta_secondary_text"] ?? ""),
            "secondary_button_url" => anna_get_option("cta_secondary_url", $defaults["cta_secondary_url"] ?? ""),
        ],
    ];
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
            <tr><td colspan="2"><h3><?php esc_html_e("Visible sections", "anna-baylis"); ?></h3></td></tr>
            <?php foreach (["hero", "intro", "recognition", "services", "about", "testimonials", "cta"] as $section): ?>
                <?php anna_home_admin_checkbox("sections", $section, ucwords(str_replace("_", " ", $section)), $content); ?>
            <?php endforeach; ?>

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
function anna_home_admin_text($section, $key, $label, $content)
{
    $id = "anna-home-" . $section . "-" . $key;
    $value = (string) ($content[$section][$key] ?? "");
    ?>
    <tr><th scope="row"><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label></th><td><input type="text" class="regular-text" id="<?php echo esc_attr($id); ?>" name="anna_home_page_content[<?php echo esc_attr($section); ?>][<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($value); ?>"></td></tr>
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
    <tr><th scope="row"><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label></th><td><div class="anna-media-field"><input type="hidden" id="<?php echo esc_attr($id); ?>" name="anna_home_page_content[<?php echo esc_attr($section); ?>][<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($value); ?>"><div class="anna-media-preview" id="<?php echo esc_attr($preview_id); ?>"><?php if ($image_url): ?><img src="<?php echo esc_url($image_url); ?>" alt="" style="max-width:150px;height:auto;border-radius:8px;"><?php endif; ?></div><button type="button" class="button anna-media-upload-btn" data-target="<?php echo esc_attr($id); ?>" data-preview="<?php echo esc_attr($preview_id); ?>"><?php esc_html_e("Select Image", "anna-baylis"); ?></button> <button type="button" class="button anna-media-remove-btn" data-target="<?php echo esc_attr($id); ?>" data-preview="<?php echo esc_attr($preview_id); ?>" <?php echo !$value ? 'style="display:none;"' : ''; ?>><?php esc_html_e("Remove", "anna-baylis"); ?></button></div></td></tr>
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
        "services" => ["eyebrow", "heading", "description", "cta_text", "cta_url"],
        "about" => ["eyebrow", "heading", "body", "quote", "image_id", "badge_number", "badge_text", "expertise_text", "cta_text", "cta_url"],
        "testimonials" => ["eyebrow", "heading", "summary", "cta_text", "cta_url"],
        "cta" => ["eyebrow", "heading", "description", "trust_text", "primary_button_text", "primary_button_url", "secondary_button_text", "secondary_button_url"],
    ];
    $textarea_keys = ["heading", "description", "intro_heading", "intro_body", "intro_quote", "recognition_description", "recognition_items_text", "body", "quote", "expertise_text", "summary"];
    $url_keys = ["primary_button_url", "secondary_button_url", "cta_url"];

    $saved = ["sections" => []];
    foreach (["hero", "intro", "recognition", "services", "about", "testimonials", "cta"] as $section) {
        $saved["sections"][$section] = !empty($raw["sections"][$section]);
    }

    foreach ($schema as $section => $keys) {
        $saved[$section] = [];
        foreach ($keys as $key) {
            $value = isset($raw[$section][$key]) ? (string) $raw[$section][$key] : "";
            if ("image_id" === $key) {
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
