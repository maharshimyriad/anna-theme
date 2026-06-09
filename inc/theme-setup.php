<?php
/**
 * Theme setup.
 *
 * Registers all core WordPress features, nav menus, and widget areas.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function anna_setup()
{
    // Load theme translations.
    load_theme_textdomain("anna-baylis", ANNA_DIR . "/languages");

    // Let WordPress manage the document title.
    add_theme_support("title-tag");

    // Enable post thumbnails on posts and pages.
    add_theme_support("post-thumbnails");

    // Enable custom logo.
    add_theme_support("custom-logo", [
        "height" => 80,
        "width" => 240,
        "flex-height" => true,
        "flex-width" => true,
        "header-text" => ["site-title", "site-description"],
        "unlink-homepage-logo" => false,
    ]);

    // Switch default core markup to valid HTML5.
    add_theme_support("html5", [
        "search-form",
        "comment-form",
        "comment-list",
        "gallery",
        "caption",
        "style",
        "script",
        "navigation-widgets",
    ]);

    // Add theme support for selective refresh for widgets.
    add_theme_support("customize-selective-refresh-widgets");

    // Add support for full and wide align images.
    add_theme_support("align-wide");

    // Add support for responsive embedded content.
    add_theme_support("responsive-embeds");

    // Add support for editor styles.
    add_theme_support("editor-styles");

    // Add support for block editor color palette.
    add_theme_support("editor-color-palette", [
        [
            "name" => __("Primary Deep Green", "anna-baylis"),
            "slug" => "primary",
            "color" => "#007063",
        ],
        [
            "name" => __("Accent Green", "anna-baylis"),
            "slug" => "accent",
            "color" => "#4CA591",
        ],
        [
            "name" => __("Soft Background", "anna-baylis"),
            "slug" => "soft-bg",
            "color" => "#F2F6F2",
        ],
        [
            "name" => __("White", "anna-baylis"),
            "slug" => "white",
            "color" => "#FFFFFF",
        ],
    ]);

    // Register navigation menus.
    register_nav_menus([
        "primary" => __("Primary Navigation", "anna-baylis"),
        "footer" => __("Footer Navigation", "anna-baylis"),
        "mobile" => __("Mobile Navigation", "anna-baylis"),
        "footer-2" => __("Footer Column 2 Navigation", "anna-baylis"),
    ]);
}
add_action("after_setup_theme", "anna_setup");

/**
 * Register widget areas (sidebars).
 */
function anna_widgets_init()
{
    // Footer Widget Area — Column 1.
    register_sidebar([
        "name" => __("Footer — Column 1", "anna-baylis"),
        "id" => "footer-1",
        "description" => __(
            "Widgets in the first footer column.",
            "anna-baylis",
        ),
        "before_widget" => '<div id="%1$s" class="anna-footer__widget %2$s">',
        "after_widget" => "</div>",
        "before_title" => '<h3 class="anna-footer__widget-title">',
        "after_title" => "</h3>",
    ]);

    // Footer Widget Area — Column 2.
    register_sidebar([
        "name" => __("Footer — Column 2", "anna-baylis"),
        "id" => "footer-2",
        "description" => __(
            "Widgets in the second footer column.",
            "anna-baylis",
        ),
        "before_widget" => '<div id="%1$s" class="anna-footer__widget %2$s">',
        "after_widget" => "</div>",
        "before_title" => '<h3 class="anna-footer__widget-title">',
        "after_title" => "</h3>",
    ]);

    // Footer Widget Area — Column 3.
    register_sidebar([
        "name" => __("Footer — Column 3", "anna-baylis"),
        "id" => "footer-3",
        "description" => __(
            "Widgets in the third footer column.",
            "anna-baylis",
        ),
        "before_widget" => '<div id="%1$s" class="anna-footer__widget %2$s">',
        "after_widget" => "</div>",
        "before_title" => '<h3 class="anna-footer__widget-title">',
        "after_title" => "</h3>",
    ]);

    // Footer Widget Area — Column 4 (Newsletter).
    register_sidebar([
        "name" => __("Footer — Newsletter Column", "anna-baylis"),
        "id" => "footer-newsletter",
        "description" => __(
            "Newsletter signup area in the footer.",
            "anna-baylis",
        ),
        "before_widget" => '<div id="%1$s" class="anna-footer__widget %2$s">',
        "after_widget" => "</div>",
        "before_title" => '<h3 class="anna-footer__widget-title">',
        "after_title" => "</h3>",
    ]);
}
add_action("widgets_init", "anna_widgets_init");

/**
 * Set content width in pixels, based on the theme's design and stylesheet.
 *
 * @global int $content_width
 */
function anna_content_width()
{
    $GLOBALS["content_width"] = apply_filters("anna_content_width", 900);
}
add_action("after_setup_theme", "anna_content_width", 0);

// ─── Editor control for custom template pages ─────────────────────────────────

/**
 * Return all page template slugs that manage their own content.
 *
 * These pages use the Anna Content Manager plugin + theme settings
 * instead of the WordPress editor, so the editor area is hidden.
 *
 * @return string[]
 */
function anna_get_custom_content_templates()
{
    $templates = [
        "front-page.php",
        "page-about.php",
        "page-coaching.php",
        "page-oasis.php",
        "page-speaking.php",
        "page-mental-health-support.php",
        "page-move.php",
        "page-contact.php",
        "page-reviews.php",
        "page-blog.php",
    ];

    // Include any pages scaffolded via the Anna Page Scaffolder.
    if (function_exists("anna_get_scaffolded_pages")) {
        foreach (anna_get_scaffolded_pages() as $page) {
            if (!empty($page["template_file"])) {
                $templates[] = $page["template_file"];
            }
        }
    }

    return $templates;
}

/**
 * Disable the block editor (Gutenberg) for custom template pages.
 *
 * When this returns false WordPress falls back to the classic meta-box
 * layout, where remove_post_type_support() below then hides the textarea.
 *
 * @param bool    $use_block_editor Whether to use the block editor.
 * @param WP_Post $post             Current post.
 * @return bool
 */
function anna_disable_block_editor_for_custom_templates(
    $use_block_editor,
    $post,
) {
    if (!$post || "page" !== $post->post_type) {
        return $use_block_editor;
    }
    $template = get_post_meta($post->ID, "_wp_page_template", true);
    if (in_array($template, anna_get_custom_content_templates(), true)) {
        return false;
    }
    return $use_block_editor;
}
add_filter(
    "use_block_editor_for_post",
    "anna_disable_block_editor_for_custom_templates",
    10,
    2,
);

/**
 * Remove the classic editor textarea from custom template pages.
 *
 * Called on admin_init so remove_post_type_support() takes effect before
 * meta boxes are registered. Scoped to the specific post being edited so
 * it does not affect any other admin request in the same page load.
 */
function anna_remove_editor_for_custom_templates()
{
    if (!is_admin()) {
        return;
    }
    $post_id = absint($_GET["post"] ?? 0);
    if (!$post_id) {
        return;
    }
    $template = get_post_meta($post_id, "_wp_page_template", true);
    if (in_array($template, anna_get_custom_content_templates(), true)) {
        remove_post_type_support("page", "editor");
    }
}
add_action("admin_init", "anna_remove_editor_for_custom_templates");
