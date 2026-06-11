<?php
/**
 * Contact page helpers.
 *
 * Content is stored in page post meta (_anna_content_contact_page)
 * and managed from the Contact page edit screen.
 * Falls back to design defaults when no meta has been saved.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Hard-coded design defaults — shown until an admin edits the page.
 *
 * @return array<string, mixed>
 */
function anna_get_contact_default_content()
{
    return [
        "hero_eyebrow" => "Get in touch",
        "hero_heading" => "Let's start a\nconversation",
        "hero_image_id" => 0,
        "info_heading" => "Contact Information",
        "info_email" => anna_get_option("contact_email", "info@annabaylis.com.au"),
        "info_phone" => anna_get_option("contact_phone", ""),
        "info_address" => anna_get_option("contact_address", ""),
        "info_hours" => anna_get_option("contact_hours", ""),
        "cta_card_heading" => "Ready to begin?",
        "cta_card_body" =>
            "Start with a complimentary discovery call to see if we're the right fit.",
        "cta_card_button_text" => "Book a Discovery Call",
        "cta_card_button_url" => anna_get_discovery_call_url(),
        "form_heading" => "Send a Message",
        "form_shortcode" => '[gravityform id="2" title="false"]',
        "form_name_label" => "Name",
        "form_email_label" => "Email",
        "form_subject_label" => "Subject",
        "form_message_label" => "Message",
        "form_button_text" => "Send Message",
        "form_response_note" =>
            "I typically respond within <strong>24–48 hours</strong>. For urgent matters, please call directly.",
        "form_action_url" => "",
    ];
}

/**
 * Contact page content meta key.
 *
 * @return string
 */
function anna_get_contact_content_meta_key()
{
    return "_anna_content_contact_page";
}

/**
 * Legacy/porter contact page content meta key.
 *
 * @return string
 */
function anna_get_contact_legacy_content_meta_key()
{
    return "_anna_content_contact_pg";
}

/**
 * Merge saved contact page meta with defaults.
 *
 * @param int $post_id Page ID.
 * @return array<string, mixed>
 */
function anna_get_contact_page_content_for_post($post_id)
{
    $defaults = anna_get_contact_default_content();
    $post_id = absint($post_id);

    if (!$post_id) {
        return $defaults;
    }

    $saved = get_post_meta($post_id, anna_get_contact_content_meta_key(), true);
    if (!is_array($saved) || empty($saved)) {
        $saved = get_post_meta(
            $post_id,
            anna_get_contact_legacy_content_meta_key(),
            true,
        );
    }

    if (!is_array($saved) || empty($saved)) {
        return $defaults;
    }

    $content = [];
    foreach ($defaults as $key => $default_value) {
        if ("hero_image_id" === $key) {
            $content[$key] = isset($saved[$key])
                ? absint($saved[$key])
                : $default_value;
            continue;
        }

        $trimmed = trim((string) ($saved[$key] ?? ""));
        if (function_exists("anna_is_intentionally_blank") && anna_is_intentionally_blank($trimmed)) {
            $content[$key] = "";
        } elseif ("" !== $trimmed) {
            $content[$key] = $saved[$key];
        } else {
            $content[$key] = $default_value;
        }
    }

    return $content;
}

/**
 * Get contact page content: page meta overrides defaults.
 *
 * @return array<string, mixed>
 */
function anna_get_contact_page_content()
{
    $post_id = anna_get_current_page_content_id();

    return anna_get_contact_page_content_for_post($post_id);
}

/**
 * Determine whether a page is the Contact page managed by this theme.
 *
 * @param int|WP_Post $post Post object or ID.
 * @return bool
 */
function anna_is_contact_content_page($post)
{
    $post = get_post($post);
    if (!$post || "page" !== $post->post_type) {
        return false;
    }

    $template = get_post_meta($post->ID, "_wp_page_template", true);

    return in_array($template, ["page-contact.php", "page-contact-test.php"], true) ||
        "contact" === $post->post_name;
}

/**
 * Register Contact edit-page fields.
 */
function anna_register_contact_page_content_meta_box($post)
{
    if (!anna_is_contact_content_page($post)) {
        return;
    }

    add_meta_box(
        "anna_contact_page_content",
        __( "Anna Contact Page Content", "anna-baylis" ),
        "anna_render_contact_page_content_meta_box",
        "page",
        "normal",
        "high",
    );
}
add_action("add_meta_boxes_page", "anna_register_contact_page_content_meta_box");

/**
 * Render Contact edit-page fields.
 *
 * @param WP_Post $post Current post.
 */
function anna_render_contact_page_content_meta_box($post)
{
    $content = anna_get_contact_page_content_for_post($post->ID);
    wp_nonce_field("anna_save_contact_page_content", "anna_contact_page_content_nonce");
    ?>
    <div class="anna-contact-page-admin-fields">
        <p class="description"><?php esc_html_e("These fields control the Contact page left information column and form area. Use the normal Update button to save changes.", "anna-baylis"); ?></p>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <td colspan="2"><h3><?php esc_html_e("Hero", "anna-baylis"); ?></h3></td>
                </tr>
                <?php anna_render_contact_admin_text_field("hero_eyebrow", __("Hero eyebrow", "anna-baylis"), $content); ?>
                <?php anna_render_contact_admin_textarea_field("hero_heading", __("Hero heading", "anna-baylis"), $content, 3); ?>

                <tr>
                    <td colspan="2"><h3><?php esc_html_e("Left contact section", "anna-baylis"); ?></h3></td>
                </tr>
                <?php anna_render_contact_admin_text_field("info_heading", __("Heading", "anna-baylis"), $content); ?>
                <?php anna_render_contact_admin_text_field("info_email", __("Email", "anna-baylis"), $content, "email"); ?>
                <?php anna_render_contact_admin_text_field("info_phone", __("Phone", "anna-baylis"), $content); ?>
                <?php anna_render_contact_admin_textarea_field("info_address", __("Address / location", "anna-baylis"), $content, 3); ?>
                <?php anna_render_contact_admin_text_field("info_hours", __("Hours", "anna-baylis"), $content); ?>
                <?php anna_render_contact_admin_text_field("cta_card_heading", __("CTA card heading", "anna-baylis"), $content); ?>
                <?php anna_render_contact_admin_textarea_field("cta_card_body", __("CTA card body", "anna-baylis"), $content, 3); ?>
                <?php anna_render_contact_admin_text_field("cta_card_button_text", __("CTA button text", "anna-baylis"), $content); ?>
                <?php anna_render_contact_admin_text_field("cta_card_button_url", __("CTA button URL", "anna-baylis"), $content); ?>

                <tr>
                    <td colspan="2"><h3><?php esc_html_e("Form section", "anna-baylis"); ?></h3></td>
                </tr>
                <?php anna_render_contact_admin_text_field("form_heading", __("Form heading", "anna-baylis"), $content); ?>
                <?php anna_render_contact_admin_textarea_field("form_shortcode", __("Form shortcode", "anna-baylis"), $content, 2, __("Example: [gravityform id=\"2\" title=\"false\"]", "anna-baylis")); ?>
                <?php anna_render_contact_admin_textarea_field("form_response_note", __("Response note", "anna-baylis"), $content, 3, __("Allowed inline HTML includes strong, em, and br.", "anna-baylis")); ?>
            </tbody>
        </table>
    </div>
    <?php
}

/**
 * Render a Contact admin text field.
 *
 * @param string               $key Field key.
 * @param string               $label Field label.
 * @param array<string, mixed> $content Current content.
 * @param string               $type Input type.
 */
function anna_render_contact_admin_text_field($key, $label, $content, $type = "text")
{
    ?>
    <tr>
        <th scope="row"><label for="anna-contact-<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label></th>
        <td><input type="<?php echo esc_attr($type); ?>" class="regular-text" id="anna-contact-<?php echo esc_attr($key); ?>" name="anna_contact_page_content[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr((string) ($content[$key] ?? "")); ?>"></td>
    </tr>
    <?php
}

/**
 * Render a Contact admin textarea field.
 *
 * @param string               $key Field key.
 * @param string               $label Field label.
 * @param array<string, mixed> $content Current content.
 * @param int                  $rows Textarea rows.
 * @param string               $description Optional description.
 */
function anna_render_contact_admin_textarea_field($key, $label, $content, $rows = 4, $description = "")
{
    ?>
    <tr>
        <th scope="row"><label for="anna-contact-<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label></th>
        <td>
            <textarea class="large-text" rows="<?php echo esc_attr($rows); ?>" id="anna-contact-<?php echo esc_attr($key); ?>" name="anna_contact_page_content[<?php echo esc_attr($key); ?>]"><?php echo esc_textarea((string) ($content[$key] ?? "")); ?></textarea>
            <?php if ($description): ?>
                <p class="description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </td>
    </tr>
    <?php
}

/**
 * Save Contact edit-page fields.
 *
 * @param int $post_id Current post ID.
 */
function anna_save_contact_page_content_meta_box($post_id)
{
    if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE) {
        return;
    }

    if (!isset($_POST["anna_contact_page_content_nonce"])) {
        return;
    }

    $nonce = sanitize_text_field(wp_unslash($_POST["anna_contact_page_content_nonce"]));
    if (!wp_verify_nonce($nonce, "anna_save_contact_page_content")) {
        return;
    }

    if (!current_user_can("edit_post", $post_id) || !anna_is_contact_content_page($post_id)) {
        return;
    }

    $raw = isset($_POST["anna_contact_page_content"]) && is_array($_POST["anna_contact_page_content"])
        ? wp_unslash($_POST["anna_contact_page_content"])
        : [];

    $defaults = anna_get_contact_default_content();
    $textarea_keys = [
        "hero_heading",
        "info_address",
        "cta_card_body",
        "form_shortcode",
        "form_response_note",
    ];
    $url_keys = ["cta_card_button_url", "form_action_url"];
    $email_keys = ["info_email"];
    $saved = [];

    foreach ($defaults as $key => $default_value) {
        if ("hero_image_id" === $key) {
            $saved[$key] = absint($raw[$key] ?? 0);
            continue;
        }

        $value = isset($raw[$key]) ? (string) $raw[$key] : "";
        if (in_array($key, $url_keys, true)) {
            $saved[$key] = esc_url_raw($value);
        } elseif (in_array($key, $email_keys, true)) {
            $saved[$key] = sanitize_email($value);
        } elseif ("form_response_note" === $key) {
            $saved[$key] = wp_kses($value, ["strong" => [], "em" => [], "br" => []]);
        } elseif (in_array($key, $textarea_keys, true)) {
            $saved[$key] = sanitize_textarea_field($value);
        } else {
            $saved[$key] = sanitize_text_field($value);
        }
    }

    update_post_meta($post_id, anna_get_contact_content_meta_key(), $saved);
    update_post_meta($post_id, anna_get_contact_legacy_content_meta_key(), $saved);
}
add_action("save_post_page", "anna_save_contact_page_content_meta_box");

// ─── Contact Form Handler ─────────────────────────────────────────────────────

add_action("admin_post_anna_contact_form", "anna_handle_contact_form");
add_action("admin_post_nopriv_anna_contact_form", "anna_handle_contact_form");

/**
 * Handle the contact form POST submission.
 */
function anna_handle_contact_form()
{
    $referer = wp_get_referer() ?: home_url("/what-is-a-life-coach/");

    if (
        !isset($_POST["anna_contact_nonce"]) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST["anna_contact_nonce"])),
            "anna_contact_form",
        )
    ) {
        wp_safe_redirect(add_query_arg("contact", "error", $referer));
        exit();
    }

    $name = sanitize_text_field(wp_unslash($_POST["contact_name"] ?? ""));
    $email = sanitize_email(wp_unslash($_POST["contact_email"] ?? ""));
    $subject = sanitize_text_field(wp_unslash($_POST["contact_subject"] ?? ""));
    $message = sanitize_textarea_field(
        wp_unslash($_POST["contact_message"] ?? ""),
    );

    if (!$name || !is_email($email) || !$message) {
        wp_safe_redirect(add_query_arg("contact", "error", $referer));
        exit();
    }

    $to = anna_get_option("contact_email", get_option("admin_email"));
    $email_subj =
        $subject ?:
        sprintf(
            /* translators: %s: sender name */
            __("New contact form message from %s", "anna-baylis"),
            $name,
        );
    $body = sprintf("Name: %s\nEmail: %s\n\n%s", $name, $email, $message);
    $headers = [
        "Content-Type: text/plain; charset=UTF-8",
        "Reply-To: " . $name . " <" . $email . ">",
    ];

    wp_mail($to, $email_subj, $body, $headers);

    wp_safe_redirect(add_query_arg("contact", "sent", $referer));
    exit();
}
