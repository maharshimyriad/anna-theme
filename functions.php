<?php
/**
 * Functions and definitions.
 *
 * The master bootstrap file. Loads all modular includes in the correct order.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if (!defined("ABSPATH")) {
    exit();
}

// ─── Theme Constants ─────────────────────────────────────────────────────────
define("ANNA_VERSION", "3.1.16");
define("ANNA_DIR", get_template_directory());
define("ANNA_URI", get_template_directory_uri());
define("ANNA_ASSETS", ANNA_URI . "/assets");
define("ANNA_CSS", ANNA_ASSETS . "/css");
define("ANNA_JS", ANNA_ASSETS . "/js");
define("ANNA_IMAGES", ANNA_ASSETS . "/images");

/** Default booking / discovery-call URL used by every "Book a Discovery Call" button. */
define(
    "ANNA_DISCOVERY_CALL_URL",
    "https://calendly.com/mindbodycoach/discovery-call-with-anna",
);

// ─── Core Setup ───────────────────────────────────────────────────────────────
require_once ANNA_DIR . "/inc/theme-setup.php";
require_once ANNA_DIR . "/inc/enqueue.php";
require_once ANNA_DIR . "/inc/image-sizes.php";
require_once ANNA_DIR . "/inc/helpers.php";
require_once ANNA_DIR . "/inc/home-helpers.php";
require_once ANNA_DIR . "/inc/page-registry.php";

// ─── Bundled Content Manager ──────────────────────────────────────────────────
require_once ANNA_DIR . "/anna-content-manager/anna-content-manager.php";

if (file_exists(ANNA_DIR . "/anna-page-scaffolder/anna-page-scaffolder.php")) {
    require_once ANNA_DIR . "/anna-page-scaffolder/anna-page-scaffolder.php";
}

if (file_exists(ANNA_DIR . "/anna-content-porter/anna-content-porter.php")) {
    require_once ANNA_DIR . "/anna-content-porter/anna-content-porter.php";
}
require_once ANNA_DIR . "/inc/oasis-helpers.php";
require_once ANNA_DIR . "/inc/speaking-helpers.php";
require_once ANNA_DIR . "/inc/mhs-helpers.php";
require_once ANNA_DIR . "/inc/move-helpers.php";
require_once ANNA_DIR . "/inc/contact-helpers.php";
require_once ANNA_DIR . "/inc/reviews-helpers.php";
require_once ANNA_DIR . "/inc/blog-helpers.php";
require_once ANNA_DIR . "/inc/template-functions.php";
require_once ANNA_DIR . "/inc/template-tags.php";
require_once ANNA_DIR . "/inc/nav-walkers.php";

// ─── Custom Post Types ────────────────────────────────────────────────────────
require_once ANNA_DIR . "/inc/cpt-reviews.php";

// ─── Taxonomies ───────────────────────────────────────────────────────────────

// ─── Meta Boxes ───────────────────────────────────────────────────────────────

// ─── Admin Settings Framework ────────────────────────────────────────────────
require_once ANNA_DIR . "/inc/admin/settings-framework.php";
require_once ANNA_DIR . "/inc/admin/settings-fields.php";
require_once ANNA_DIR . "/inc/admin/settings-sanitize.php";
require_once ANNA_DIR . "/inc/admin/settings-pages.php";
require_once ANNA_DIR . "/inc/admin/coaching-settings-fields.php";
require_once ANNA_DIR . "/inc/admin/oasis-settings-fields.php";
require_once ANNA_DIR . "/inc/admin/speaking-settings-fields.php";
require_once ANNA_DIR . "/inc/admin/mhs-settings-fields.php";
require_once ANNA_DIR . "/inc/admin/move-settings-fields.php";
require_once ANNA_DIR . "/inc/admin/settings-css-output.php";

// ─── Blog category filter via main query ─────────────────────────────────────
/**
 * Apply the ?cat= filter to the main query on the blog posts page.
 * This keeps pagination routed through WordPress's native /page/N/ system.
 */
add_action( 'pre_get_posts', function ( WP_Query $q ) {
	if ( is_admin() || ! $q->is_main_query() || ! $q->is_home() ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$cat_slug = isset( $_GET['cat'] ) ? sanitize_key( wp_unslash( $_GET['cat'] ) ) : '';
	if ( $cat_slug ) {
		$q->set( 'category_name', $cat_slug );
	}

	$q->set( 'posts_per_page', 9 );
} );

