<?php
/**
 * Contact page helpers.
 *
 * Content is stored in page post meta (_anna_content_contact_page)
 * managed by the Anna Content Manager plugin.
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
        "cta_card_heading" => "Ready to begin?",
        "cta_card_body" =>
            "Start with a complimentary discovery call to see if we're the right fit.",
        "cta_card_button_text" => "Book a Discovery Call",
        "cta_card_button_url" => "#contact",
        "form_heading" => "Send a Message",
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
 * Get contact page content: page meta overrides defaults.
 *
 * @return array<string, mixed>
 */
function anna_get_contact_page_content()
{
    $defaults = anna_get_contact_default_content();
    $post_id = anna_get_current_page_content_id();

    if (!$post_id) {
        return $defaults;
    }

    $saved = get_post_meta($post_id, "_anna_content_contact_page", true);
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
        if (anna_is_intentionally_blank($trimmed)) {
            $content[$key] = "";
        } elseif ("" !== $trimmed) {
            $content[$key] = $saved[$key];
        } else {
            $content[$key] = $default_value;
        }
    }

    return $content;
}

// ─── Contact Form Handler ─────────────────────────────────────────────────────

add_action("admin_post_anna_contact_form", "anna_handle_contact_form");
add_action("admin_post_nopriv_anna_contact_form", "anna_handle_contact_form");

/**
 * Handle the contact form POST submission.
 */
function anna_handle_contact_form()
{
    $referer = wp_get_referer() ?: home_url("/contact/");

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
