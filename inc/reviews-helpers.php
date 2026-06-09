<?php
/**
 * Reviews page helpers.
 *
 * Content is stored in page post meta (_anna_content_reviews_page)
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
function anna_get_reviews_default_content()
{
    return [
        "hero_eyebrow" => "What clients say",
        "hero_heading" => "102 five-star\nGoogle reviews",
        "hero_image_id" => 0,
        "hero_rating_text" => "5.0 · Google Reviews · Australia and worldwide",
        "google_reviews_url" => "",
        "google_reviews_text" => "View all reviews on Google",
        "cta_heading" => "Ready to begin your transformation?",
        "cta_body" => "Start with a complimentary discovery call.",
        "cta_button_text" => "Book a Discovery Call",
        "cta_button_url" => anna_get_discovery_call_url(),
    ];
}

/**
 * Get reviews page content: page meta overrides defaults.
 *
 * @return array<string, mixed>
 */
function anna_get_reviews_page_content()
{
    $defaults = anna_get_reviews_default_content();
    $post_id = anna_get_current_page_content_id();

    if (!$post_id) {
        return $defaults;
    }

    $saved = get_post_meta($post_id, "_anna_content_reviews_page", true);
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
