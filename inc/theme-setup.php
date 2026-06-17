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
    if (
        $post &&
        ("home" === $post->post_name || absint(get_option("page_on_front")) === $post_id)
    ) {
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
/**
 * For pages managed by Anna Content Manager, replace the classic editor
 * with an informational notice pointing admins to the content meta box.
 * The editor is kept in the DOM (so Yoast can read post_content) but its
 * textarea is replaced with a styled read-only message.
 */
function anna_replace_editor_with_notice_for_custom_templates()
{
    $post_id = absint( $_GET['post'] ?? 0 );
    if ( ! $post_id || ! anna_is_custom_content_template_page( $post_id ) ) {
        return;
    }
    ?>
    <style>
        /* Hide the actual TinyMCE toolbar and textarea */
        #wp-content-editor-tools,
        #wp-content-wrap .wp-editor-tabs,
        #wp-content-wrap iframe,
        #wp-content-wrap #content,
        #wp-content-wrap .wp-media-buttons {
            display: none !important;
        }

        /* Style the notice area */
        #anna-content-editor-notice {
            padding: 16px 20px;
            background: #f0f6fc;
            border: 1px solid #c3d9ed;
            border-radius: 4px;
            font-size: 13px;
            line-height: 1.6;
            color: #1d2327;
        }
        #anna-content-editor-notice strong {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
        }
        #anna-content-editor-notice a {
            text-decoration: none;
            color: #2271b1;
            font-weight: 600;
        }
        #anna-content-editor-notice a:hover {
            color: #135e96;
            text-decoration: underline;
        }
    </style>
    <script>
    document.addEventListener( 'DOMContentLoaded', function () {
        var wrap = document.getElementById( 'wp-content-wrap' );
        if ( ! wrap ) return;

        // Determine which meta box to scroll to based on the page template.
        var metaBoxId = (function () {
            var bodyClasses = document.body.className;
            if ( bodyClasses.indexOf( 'page-template-page-oasis' )                 !== -1 ) return 'anna_content_oasis_page';
            if ( bodyClasses.indexOf( 'page-template-page-speaking' )              !== -1 ) return 'anna_content_speaking_page';
            if ( bodyClasses.indexOf( 'page-template-page-mental-health-support' ) !== -1 ) return 'anna_content_mhs_page';
            if ( bodyClasses.indexOf( 'page-template-page-move' )                  !== -1 ) return 'anna_content_move_page';
            if ( bodyClasses.indexOf( 'page-template-page-reviews' )               !== -1 ) return 'anna_content_reviews_page';
            if ( bodyClasses.indexOf( 'page-template-page-contact' )               !== -1 ) return 'anna_content_contact_page';
            if ( bodyClasses.indexOf( 'page-template-page-blog' )                  !== -1 ) return 'anna_content_blog_page';
            if ( bodyClasses.indexOf( 'page-template-page-about' )                 !== -1 ) return 'anna_content_about_page';
            if ( bodyClasses.indexOf( 'page-template-page-coaching' )              !== -1 ) return 'anna_content_coaching_page';
            // Scaffolded / flexible pages — meta box ID is anna_content_{code}_page.
            var match = bodyClasses.match( /page-template-page-([\w-]+)/ );
            if ( match ) return 'anna_content_' + match[1].replace( /-/g, '_' ) + '_page';
            return null;
        }() );

        var notice = document.createElement( 'div' );
        notice.id  = 'anna-content-editor-notice';

        var anchor = metaBoxId
            ? '<a href="#' + metaBoxId + '" onclick="document.getElementById(\'' + metaBoxId + '\').scrollIntoView({behavior:\'smooth\'});return false;">↓ Jump to content fields</a>'
            : '';

        notice.innerHTML =
            '<strong>Content is managed by Anna Content Manager</strong>' +
            'This page uses custom meta fields for all editable copy. ' +
            'The text below is auto-synced to this area for SEO analysis only — edit content using the fields below.' +
            ( anchor ? ' ' + anchor : '' );

        wrap.insertBefore( notice, wrap.firstChild );
    } );
    </script>
    <?php
}
add_action( 'admin_head-post.php', 'anna_replace_editor_with_notice_for_custom_templates' );

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
 * Return the known homepage content meta keys. The homepage uses multiple
 * section metaboxes, unlike inner pages which usually use one meta row.
 *
 * @return string[]
 */
function anna_get_home_template_content_meta_keys()
{
    return [
        // New single-row key (slug changed to "home", all sections in one meta row).
        "_anna_content_home_page",
        // Legacy per-section keys from the old multi-metabox system.
        "_anna_content_hero",
        "_anna_content_intro",
        "_anna_content_services",
        "_anna_content_about",
        "_anna_content_testimonials",
        "_anna_content_cta",
        "_anna_content_recognition",
        "anna_content_hero",
        "anna_content_intro",
        "anna_content_services",
        "anna_content_about",
        "anna_content_testimonials",
        "anna_content_cta",
        "anna_content_recognition",
    ];
}

/**
 * One-time migration: merge the legacy 6-row home page meta into the new
 * single _anna_content_home_page row, then delete the old rows.
 *
 * Runs once on admin_init and is guarded by a flag so it never runs again.
 */
function anna_maybe_migrate_home_page_meta()
{
    if ( get_option( 'anna_home_meta_migrated_v1' ) ) {
        return;
    }

    $front_id = absint( get_option( 'page_on_front' ) );
    if ( ! $front_id ) {
        return;
    }

    // If the new single key already has data, just clean up old rows and flag done.
    $existing_new = get_post_meta( $front_id, '_anna_content_home_page', true );
    $has_new      = is_array( $existing_new ) && ! empty( array_filter( $existing_new ) );

    if ( ! $has_new ) {
        // Build the new single row from old per-section rows.
        $hero         = get_post_meta( $front_id, '_anna_content_hero', true );
        $intro        = get_post_meta( $front_id, '_anna_content_intro', true );
        $services     = get_post_meta( $front_id, '_anna_content_services', true );
        $about        = get_post_meta( $front_id, '_anna_content_about', true );
        $testimonials = get_post_meta( $front_id, '_anna_content_testimonials', true );
        $cta          = get_post_meta( $front_id, '_anna_content_cta', true );

        $has_old = array_filter( [ $hero, $intro, $services, $about, $testimonials, $cta ], 'is_array' );

        if ( ! empty( $has_old ) ) {
            $merged = [];
            if ( is_array( $hero ) )         $merged['hero']         = $hero;
            if ( is_array( $intro ) )         $merged['intro']        = $intro;
            if ( is_array( $services ) )      $merged['services']     = $services;
            if ( is_array( $about ) )         $merged['about']        = $about;
            if ( is_array( $testimonials ) )  $merged['testimonials'] = $testimonials;
            if ( is_array( $cta ) )           $merged['cta']          = $cta;

            update_post_meta( $front_id, '_anna_content_home_page', $merged );
        }
    }

    // Delete all legacy per-section rows regardless.
    $legacy_keys = [
        '_anna_content_hero', '_anna_content_intro', '_anna_content_services',
        '_anna_content_about', '_anna_content_testimonials', '_anna_content_cta',
        '_anna_content_recognition', 'anna_content_hero', 'anna_content_intro',
        'anna_content_services', 'anna_content_about', 'anna_content_testimonials',
        'anna_content_cta', 'anna_content_recognition',
    ];
    foreach ( $legacy_keys as $key ) {
        delete_post_meta( $front_id, $key );
    }

    update_option( 'anna_home_meta_migrated_v1', 1 );
}
add_action( 'admin_init', 'anna_maybe_migrate_home_page_meta' );

/**
 * Check whether a page is the Anna homepage/front-page content page.
 *
 * @param int $post_id Page ID.
 * @return bool
 */
function anna_is_home_template_page($post_id)
{
    $post_id = absint($post_id);
    $post = get_post($post_id);
    if (!$post || "page" !== $post->post_type) {
        return false;
    }

    $template = get_post_meta($post_id, "_wp_page_template", true);

    return "home" === $post->post_name ||
        "front-page.php" === $template ||
        absint(get_option("page_on_front")) === $post_id;
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

    if (anna_is_home_template_page($post->ID)) {
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
    $nonce = wp_create_nonce( 'anna_reset_template_page_' . $post->ID );
    ?>
    <p><?php esc_html_e("Reset this page back to the theme defaults by deleting saved Anna template content from the database.", "anna-baylis"); ?></p>
    <p><strong><?php esc_html_e("This cannot be undone.", "anna-baylis"); ?></strong></p>
    <button
        type="button"
        class="button delete"
        id="anna-template-reset-btn"
        data-post-id="<?php echo esc_attr( $post->ID ); ?>"
        data-nonce="<?php echo esc_attr( $nonce ); ?>"
        data-action-url="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
        data-confirm="<?php echo esc_attr( __( 'Delete all saved Anna template content for this page and reset it to theme defaults?', 'anna-baylis' ) ); ?>"
    >
        <?php esc_html_e( 'Reset to Theme Defaults', 'anna-baylis' ); ?>
    </button>
    <script>
    (function() {
        var btn = document.getElementById('anna-template-reset-btn');
        if (!btn) return;
        btn.addEventListener('click', function() {
            if (!window.confirm(btn.dataset.confirm)) return;
            var form = document.createElement('form');
            form.method = 'post';
            form.action = btn.dataset.actionUrl;
            var fields = {
                action: 'anna_reset_template_page',
                post_id: btn.dataset.postId,
                anna_reset_template_page_nonce: btn.dataset.nonce
            };
            Object.keys(fields).forEach(function(key) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = fields[key];
                form.appendChild(input);
            });
            document.body.appendChild(form);
            form.submit();
        });
    })();
    </script>
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
    $post_id = absint($post_id);
    $all_meta = get_post_meta($post_id);
    $delete_keys = [];

    foreach (array_keys($all_meta) as $meta_key) {
        $meta_key = (string) $meta_key;
        if (str_starts_with($meta_key, "_anna_content_") || str_starts_with($meta_key, "anna_content_")) {
            $delete_keys[$meta_key] = true;
        }
    }

    if (anna_is_home_template_page($post_id)) {
        foreach (anna_get_home_template_content_meta_keys() as $meta_key) {
            $delete_keys[$meta_key] = true;
        }
    }

    $deleted = 0;
    foreach (array_keys($delete_keys) as $meta_key) {
        if (metadata_exists("post", $post_id, $meta_key)) {
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

    $deleted_meta    = absint($_GET["anna_deleted_meta"]    ?? 0);
    $deleted_options = absint($_GET["anna_deleted_options"] ?? 0);
    $message = 'Anna template content reset. Deleted ' . $deleted_meta . ' content meta key(s) and ' . $deleted_options . ' theme option key(s). The page is now using theme defaults.';
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php echo esc_html( $message ); ?></p>
    </div>
    <?php
}
add_action("admin_notices", "anna_template_page_reset_admin_notice");
