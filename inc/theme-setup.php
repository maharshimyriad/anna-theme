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
        "page-contact-test.php",
        "page-reviews.php",
        "page-blog.php",
    ];

    $theme_templates = wp_get_theme()->get_page_templates(null, "page");
    if (is_array($theme_templates)) {
        $templates = array_merge($templates, array_values($theme_templates));
    }

    // Include any pages scaffolded via the Anna Page Scaffolder.
    if (function_exists("anna_get_scaffolded_pages")) {
        foreach (anna_get_scaffolded_pages() as $page) {
            if (!empty($page["template_file"])) {
                $templates[] = $page["template_file"];
            }
        }
    }

    $templates = array_values(array_unique(array_filter($templates)));

    return apply_filters("anna_custom_content_templates", $templates);
}

/**
 * Check whether a page uses an Anna template that manages its own content.
 *
 * @param int $post_id Page ID.
 * @return bool
 */
function anna_is_custom_content_template_page($post_id)
{
    $post_id = absint($post_id);
    if (!$post_id || "page" !== get_post_type($post_id)) {
        return false;
    }

    $post = get_post($post_id);
    if ($post && "home" === $post->post_name) {
        return true;
    }

    $template = get_post_meta($post_id, "_wp_page_template", true);

    return in_array($template, anna_get_custom_content_templates(), true);
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
    if (anna_is_custom_content_template_page($post->ID)) {
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
    if (anna_is_custom_content_template_page($post_id)) {
        remove_post_type_support("page", "editor");
    }
}
add_action("admin_init", "anna_remove_editor_for_custom_templates");

/**
 * Hide the classic editor post area as a fallback for admin screens/plugins that
 * render it even after editor support has been removed.
 */
function anna_hide_postarea_for_custom_template_pages()
{
    $post_id = absint($_GET["post"] ?? 0);
    if (!anna_is_custom_content_template_page($post_id)) {
        return;
    }
    ?>
    <style>
        #postdivrich,
        #postdiv,
        #wp-content-wrap {
            display: none !important;
        }
    </style>
    <?php
}
add_action("admin_head-post.php", "anna_hide_postarea_for_custom_template_pages");

// ─── Reset custom template content ────────────────────────────────────────────

/**
 * Option name that stores page option prefixes whose DB defaults should not be
 * re-seeded after an admin resets a template page.
 *
 * @return string
 */
function anna_get_reset_option_prefixes_option_name()
{
    return "anna_reset_page_option_prefixes";
}

/**
 * Check whether a page-option prefix has been intentionally reset.
 *
 * @param string $prefix Option key prefix.
 * @return bool
 */
function anna_is_page_option_prefix_reset($prefix)
{
    $prefixes = get_option(anna_get_reset_option_prefixes_option_name(), []);
    if (!is_array($prefixes)) {
        return false;
    }

    return in_array($prefix, $prefixes, true);
}

/**
 * Remember that these option prefixes should stay deleted until manually saved.
 *
 * @param string[] $prefixes Option key prefixes.
 */
function anna_mark_page_option_prefixes_reset($prefixes)
{
    $existing = get_option(anna_get_reset_option_prefixes_option_name(), []);
    if (!is_array($existing)) {
        $existing = [];
    }

    $prefixes = array_filter(array_map("strval", $prefixes));
    update_option(
        anna_get_reset_option_prefixes_option_name(),
        array_values(array_unique(array_merge($existing, $prefixes))),
        false,
    );
}

/**
 * Return exact home-page option keys managed by the theme template.
 *
 * @return string[]
 */
function anna_get_home_template_option_keys()
{
    return [
        "section_hero_enabled",
        "section_intro_enabled",
        "section_recognition_enabled",
        "section_services_enabled",
        "section_about_enabled",
        "section_testimonials_enabled",
        "section_cta_enabled",
        "hero_eyebrow",
        "hero_heading",
        "hero_description",
        "hero_trust_text",
        "hero_image_id",
        "stat_1_value",
        "stat_1_label",
        "stat_2_value",
        "stat_2_label",
        "stat_3_value",
        "stat_3_label",
        "intro_eyebrow",
        "intro_heading",
        "intro_body",
        "intro_quote",
        "intro_quote_cite",
        "intro_image_id",
        "recognition_eyebrow",
        "recognition_heading",
        "recognition_description",
        "recognition_items_text",
        "recognition_image_id",
        "services_eyebrow",
        "services_heading",
        "services_description",
        "services_cta_text",
        "services_cta_url",
        "about_eyebrow",
        "about_heading",
        "about_body",
        "about_image_id",
        "about_quote",
        "about_expertise_text",
        "about_cta_text",
        "about_cta_url",
        "testimonials_eyebrow",
        "testimonials_heading",
        "testimonials_summary",
        "testimonials_cta_text",
        "testimonials_cta_url",
        "cta_eyebrow",
        "cta_heading",
        "cta_description",
        "cta_trust",
        "cta_image_id",
        "cta_primary_text",
        "cta_primary_url",
        "cta_secondary_text",
        "cta_secondary_url",
    ];
}

/**
 * Return reset config for a page using an Anna template.
 *
 * @param int $post_id Page ID.
 * @return array{option_prefixes:string[], option_keys:string[]}
 */
function anna_get_template_page_reset_config($post_id)
{
    $post = get_post($post_id);
    if (!$post || "page" !== $post->post_type) {
        return ["option_prefixes" => [], "option_keys" => []];
    }

    $template = get_post_meta($post->ID, "_wp_page_template", true);
    $option_prefixes = [];
    $option_keys = [];

    $prefix_map = [
        "page-about.php" => "about_pg_",
        "page-coaching.php" => "coaching_pg_",
        "page-oasis.php" => "oasis_pg_",
        "page-speaking.php" => "speaking_pg_",
        "page-mental-health-support.php" => "mhs_pg_",
        "page-move.php" => "move_pg_",
        "page-reviews.php" => "reviews_pg_",
        "page-contact.php" => "contact_pg_",
        "page-contact-test.php" => "contact_pg_",
        "page-blog.php" => "blog_pg_",
    ];

    if (isset($prefix_map[$template])) {
        $option_prefixes[] = $prefix_map[$template];
    }

    if (function_exists("anna_get_scaffolded_pages")) {
        foreach (anna_get_scaffolded_pages() as $page) {
            if (($page["template_file"] ?? "") === $template && !empty($page["option_prefix"])) {
                $option_prefixes[] = (string) $page["option_prefix"];
            }
        }
    }

    if (
        "front-page.php" === $template ||
        "home" === $post->post_name ||
        absint(get_option("page_on_front")) === absint($post->ID)
    ) {
        $option_keys = array_merge($option_keys, anna_get_home_template_option_keys());
    }

    return [
        "option_prefixes" => array_values(array_unique(array_filter($option_prefixes))),
        "option_keys" => array_values(array_unique(array_filter($option_keys))),
    ];
}

/**
 * Register the reset box on Anna template page edit screens.
 *
 * @param WP_Post $post Current page.
 */
function anna_register_template_page_reset_meta_box($post)
{
    if (!anna_is_custom_content_template_page($post->ID)) {
        return;
    }

    add_meta_box(
        "anna_template_page_reset",
        __("Anna Template Reset", "anna-baylis"),
        "anna_render_template_page_reset_meta_box",
        "page",
        "side",
        "low",
    );
}
add_action("add_meta_boxes_page", "anna_register_template_page_reset_meta_box");

/**
 * Render the reset box.
 *
 * @param WP_Post $post Current page.
 */
function anna_render_template_page_reset_meta_box($post)
{
    ?>
    <p><?php esc_html_e("Reset this page back to the theme defaults by deleting saved Anna template content from the database.", "anna-baylis"); ?></p>
    <p><strong><?php esc_html_e("This cannot be undone.", "anna-baylis"); ?></strong></p>
    <form method="post" action="<?php echo esc_url(admin_url("admin-post.php")); ?>" onsubmit="return confirm('<?php echo esc_js(__("Delete all saved Anna template content for this page and reset it to theme defaults?", "anna-baylis")); ?>');">
        <?php wp_nonce_field("anna_reset_template_page_" . $post->ID, "anna_reset_template_page_nonce"); ?>
        <input type="hidden" name="action" value="anna_reset_template_page">
        <input type="hidden" name="post_id" value="<?php echo esc_attr($post->ID); ?>">
        <?php submit_button(__("Reset to Theme Defaults", "anna-baylis"), "delete", "submit", false); ?>
    </form>
    <?php
}

/**
 * Delete saved Anna template content for one page and redirect back to edit page.
 */
function anna_handle_template_page_reset()
{
    $post_id = absint($_POST["post_id"] ?? 0);
    if (!$post_id || !current_user_can("edit_post", $post_id)) {
        wp_die(esc_html__("You do not have permission to reset this page.", "anna-baylis"));
    }

    $nonce = sanitize_text_field(wp_unslash($_POST["anna_reset_template_page_nonce"] ?? ""));
    if (!wp_verify_nonce($nonce, "anna_reset_template_page_" . $post_id)) {
        wp_die(esc_html__("Reset link expired. Please try again.", "anna-baylis"));
    }

    if (!anna_is_custom_content_template_page($post_id)) {
        wp_die(esc_html__("This page is not using an Anna custom template.", "anna-baylis"));
    }

    $deleted_meta = anna_delete_template_page_content_meta($post_id);
    $deleted_options = anna_delete_template_page_options($post_id);

    wp_update_post([
        "ID" => $post_id,
        "post_content" => "",
        "post_excerpt" => "",
    ]);

    $redirect = add_query_arg(
        [
            "post" => $post_id,
            "action" => "edit",
            "anna_template_reset" => 1,
            "anna_deleted_meta" => $deleted_meta,
            "anna_deleted_options" => $deleted_options,
        ],
        admin_url("post.php"),
    );

    wp_safe_redirect($redirect);
    exit();
}
add_action("admin_post_anna_reset_template_page", "anna_handle_template_page_reset");

/**
 * Delete all Anna content meta rows for a page.
 *
 * @param int $post_id Page ID.
 * @return int Number of meta keys deleted.
 */
function anna_delete_template_page_content_meta($post_id)
{
    $all_meta = get_post_meta($post_id);
    $deleted = 0;

    foreach (array_keys($all_meta) as $meta_key) {
        if (str_starts_with((string) $meta_key, "_anna_content_")) {
            delete_post_meta($post_id, $meta_key);
            $deleted++;
        }
    }

    return $deleted;
}

/**
 * Delete page-specific Anna theme option keys for a page.
 *
 * @param int $post_id Page ID.
 * @return int Number of option keys deleted.
 */
function anna_delete_template_page_options($post_id)
{
    $config = anna_get_template_page_reset_config($post_id);
    $options = get_option("anna_theme_options", []);
    if (!is_array($options)) {
        return 0;
    }

    $delete_keys = array_fill_keys($config["option_keys"], true);
    foreach (array_keys($options) as $key) {
        foreach ($config["option_prefixes"] as $prefix) {
            if (str_starts_with((string) $key, $prefix)) {
                $delete_keys[$key] = true;
                break;
            }
        }
    }

    $deleted = 0;
    foreach (array_keys($delete_keys) as $key) {
        if (array_key_exists($key, $options)) {
            unset($options[$key]);
            $deleted++;
        }
    }

    update_option("anna_theme_options", $options);
    anna_mark_page_option_prefixes_reset($config["option_prefixes"]);

    return $deleted;
}

/**
 * Show a success notice after reset.
 */
function anna_template_page_reset_admin_notice()
{
    if (empty($_GET["anna_template_reset"])) {
        return;
    }

    $deleted_meta = absint($_GET["anna_deleted_meta"] ?? 0);
    $deleted_options = absint($_GET["anna_deleted_options"] ?? 0);
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php echo esc_html(sprintf(__("Anna template content reset. Deleted %1$d content meta keys and %2$d theme option keys. The page is now using theme defaults.", "anna-baylis"), $deleted_meta, $deleted_options)); ?></p>
    </div>
    <?php
}
add_action("admin_notices", "anna_template_page_reset_admin_notice");
