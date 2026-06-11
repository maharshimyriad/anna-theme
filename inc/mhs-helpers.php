<?php
/**
 * Mental Health Support page helpers.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Default Mental Health Support page content (from design).
 *
 * @return array<string, mixed>
 */
function anna_get_mhs_default_content()
{
    return [
        "hero_eyebrow" => "Mental Health Support",
        "hero_heading" => "Empowering you to prioritise your mental wellbeing.",
        "hero_image_id" => 0,

        "opening_heading" => "Opening — your story as an athlete",
        "opening_body" =>
            "Mental health has always mattered deeply to me. As an Olympic athlete, my focus was split I trained my body relentlessly, but I also learned early that physical health alone was never enough.\n\nWith a healthy mindset I could withstand the ups and downs of elite sport. I could stay motivated through gruelling training blocks, maintain perspective through injury and setback and show up on race day with clarity and conviction. My physical health was optimised. But it was my mental health that determined how I performed when it counted.\n\nIn all sports, athletes can reach the same physical level. What determines who stands on the podium that day is what happens internally the mental strength, the self-talk, the beliefs we hold about ourselves. That understanding crossed over completely from sport into life. And it is at the heart of everything I do as a coach.",
        "opening_image_id" => 0,

        "programs_heading" => "Mental programs",
        "programs_body" =>
            "In my work as a life coach I see every day how the programs running beneath the surface shape everything - our choices, our relationships, our health, our capacity to change.\n\nThese programs form our identity, our beliefs and our values. They are shaped from a very young age through our upbringing, our experiences and our conditioning. And most of the time we are completely unaware they are running.\n\nA common example is the identity of *I am lazy*. If you see yourself that way that belief will win every single time something requires effort or discomfort. The comfortable choice gets made automatically. And with each repetition, the identity gets reinforced. The result is a cycle of self-sabotage, self-criticism and a growing sense of being stuck.\n\nThis is not a character flaw. It is a program. And programs can be changed.",

        "inner_heading" => "Inner health",
        "inner_body" =>
            "Our inner mental health shapes how we feel, how we behave and our overall sense of wellbeing. When we can understand and release the programs that are no longer serving us - something opens up. A new way of seeing ourselves. A different quality of thought, feeling and action.\n\nI use the analogy of a door.\n\nLife presents us with many opportunities. let us call them doors. Our mental programs guide us toward certain doors and away from others. Some are wide open. Some are closed. Some we walk through and find more doors beyond them. Some we never even notice are standing right in front of us.\n\nThe closed doors often have a key and that key is something inside us we have not yet accessed. A belief we are holding about ourselves. A fear of failure or rejection. A wound we have been protecting.\n\nWhen we become aware of those programs when we bring them into the light — those doors begin to open. And the possibilities become endless.",
        "inner_image_id" => 0,

        "work_heading" => "How I work",
        "work_body" =>
            "My approach is not surface level. I work bottom-up through the body, the nervous system and the subconscious to get to the root of what is actually running underneath behaviour.\n\nThe framework I use is Awareness, Acceptance and Action. We start by seeing clearly what is actually happening in the thoughts, the body, the patterns. We move into acceptance — not resignation, but honest acknowledgment of what is here. And then we take aligned action. Not forced. Not pushed. Rooted.\n\nI also draw on parts work — the understanding that we are not one unified self but a collection of parts, each with their own needs and protective roles. When we meet those parts with compassion rather than criticism, everything shifts.\n\nAnd I bring the body into everything. Movement, breath, the nervous system because lasting change is not just cognitive. It lives in the body.",

        "practice_heading" => "My daily practice",
        "practice_body" =>
            "Part of my daily routine is doing my own mental health check-in. Questioning thoughts or behaviours that are not aligning with my values or my future self. Asking myself — what is standing in my way? What choices will I no longer make? What do I need to think, feel and do to create the life I want?\n\nThis is not theory. This is how I live. And it is what I teach.",
        "practice_link_text" => "How often are you checking in?",
        "practice_link_url" => home_url( '/what-is-a-life-coach/' ),

        "ready_heading" => "Ready to go deeper?",
        "ready_subheading" =>
            "If something here resonates — I would love to connect.",
        "ready_body" =>
            "You can work with me 1-1, explore Oasis or book a free discovery call to find out which path feels right for you.",
        "ready_button_primary_text" => "Book a Discovery Call",
        "ready_button_primary_url" => anna_get_discovery_call_url(),
        "ready_button_secondary_text" => "Explore Oasis",
        "ready_button_secondary_url" => "/oasis/",
        "ready_button_tertiary_text" => "Learn About 1-1 Coaching",
        "ready_button_tertiary_url" => "/coaching/",
    ];
}

/**
 * @return array<string, mixed>
 */
function anna_get_mhs_theme_option_defaults()
{
    $out = [];
    foreach (anna_get_mhs_default_content() as $key => $value) {
        $out["mhs_pg_" . $key] = $value;
    }
    return $out;
}

/**
 * @return array<string, string>
 */
function anna_get_mhs_page_option_map()
{
    $map = [];
    foreach (array_keys(anna_get_mhs_default_content()) as $key) {
        $map[$key] = "mhs_pg_" . $key;
    }
    return $map;
}

/**
 * @param mixed $text Text with *italic* or **bold** markers.
 * @return string
 */
function anna_format_mhs_emphasis_text($text)
{
    $text = (string) $text;
    if ("" === $text) {
        return "";
    }

    $text = htmlspecialchars($text, ENT_QUOTES, "UTF-8");
    $text = preg_replace("/\*\*(.+?)\*\*/", '<strong>$1</strong>', $text);
    $text = preg_replace("/\*(.+?)\*/", '<em>$1</em>', $text);

    return $text;
}

/**
 * Get merged Mental Health Support page content.
 *
 * @return array<string, mixed>
 */
function anna_get_mhs_page_content()
{
    $defaults = anna_get_mhs_default_content();
    $theme_defs = anna_get_default_options();
    $content = [];

    $image_keys = ["hero_image_id", "opening_image_id", "inner_image_id"];

    foreach ($defaults as $key => $default_value) {
        $option_key = "mhs_pg_" . $key;
        $fallback = $theme_defs[$option_key] ?? $default_value;

        if (in_array($key, $image_keys, true)) {
            $content[$key] = absint(anna_get_option($option_key, $fallback));
        } else {
            $content[$key] = anna_get_option($option_key, $fallback);
        }
    }

    $post_id = anna_get_current_page_content_id();
    if ($post_id && function_exists("anna_content_get_mhs_page_content")) {
        $saved = anna_content_get_mhs_page_content($post_id);
        if (is_array($saved)) {
            $merge = [];
            foreach ($saved as $key => $value) {
                if (is_array($value)) {
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
 * Render MHS body copy with optional emphasis markers.
 *
 * @param string $body Raw body text.
 * @return string
 */
function anna_render_mhs_body($body)
{
    if ("" === trim((string) $body)) {
        return "";
    }

    $paragraphs = preg_split('/\n\s*\n/', trim((string) $body));
    $html = "";

    foreach ($paragraphs as $paragraph) {
        $paragraph = trim($paragraph);
        if ("" === $paragraph) {
            continue;
        }
        $html .= "<p>" . anna_format_mhs_emphasis_text($paragraph) . "</p>";
    }

    return $html;
}

/**
 * Sanitize MHS theme options.
 *
 * @param string $key   Option key.
 * @param mixed  $value Raw value.
 * @return mixed
 */
function anna_sanitize_mhs_option($key, $value)
{
    $image_keys = [
        "mhs_pg_hero_image_id",
        "mhs_pg_opening_image_id",
        "mhs_pg_inner_image_id",
    ];
    if (in_array($key, $image_keys, true)) {
        return absint($value);
    }

    $url_keys = [
        "mhs_pg_practice_link_url",
        "mhs_pg_ready_button_primary_url",
        "mhs_pg_ready_button_secondary_url",
        "mhs_pg_ready_button_tertiary_url",
    ];
    if (in_array($key, $url_keys, true)) {
        return sanitize_text_field($value);
    }

    $textarea_keys = [
        "mhs_pg_opening_body",
        "mhs_pg_programs_body",
        "mhs_pg_inner_body",
        "mhs_pg_work_body",
        "mhs_pg_practice_body",
        "mhs_pg_ready_body",
    ];
    if (in_array($key, $textarea_keys, true)) {
        return sanitize_textarea_field($value);
    }

    return sanitize_text_field($value);
}
