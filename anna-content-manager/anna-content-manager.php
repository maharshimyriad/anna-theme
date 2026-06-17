<?php
/**
 * Anna Content Manager — bundled with Anna Baylis theme.
 *
 * Loaded directly by functions.php. Not a standalone WP plugin.
 *
 * @package Anna_Baylis
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ANNA_CONTENT_MANAGER_VERSION', '0.1.8' );
define( 'ANNA_CONTENT_MANAGER_FILE', __FILE__ );
define( 'ANNA_CONTENT_MANAGER_DIR', get_template_directory() . '/anna-content-manager/' );
define( 'ANNA_CONTENT_MANAGER_URL', get_template_directory_uri() . '/anna-content-manager/' );

require_once ANNA_CONTENT_MANAGER_DIR . 'includes/trait-anna-yoast-sync.php';
require_once ANNA_CONTENT_MANAGER_DIR . 'includes/trait-anna-oasis-page-content.php';
require_once ANNA_CONTENT_MANAGER_DIR . 'includes/trait-anna-speaking-page-content.php';
require_once ANNA_CONTENT_MANAGER_DIR . 'includes/trait-anna-mhs-page-content.php';
require_once ANNA_CONTENT_MANAGER_DIR . 'includes/trait-anna-move-page-content.php';
require_once ANNA_CONTENT_MANAGER_DIR . 'includes/trait-anna-scaffolded-page-content.php';
require_once ANNA_CONTENT_MANAGER_DIR . 'includes/trait-anna-contact-page-content.php';
require_once ANNA_CONTENT_MANAGER_DIR . 'includes/trait-anna-reviews-page-content.php';
require_once ANNA_CONTENT_MANAGER_DIR . 'includes/trait-anna-blog-page-content.php';
require_once ANNA_CONTENT_MANAGER_DIR . 'includes/class-anna-content-manager.php';

Anna_Content_Manager::instance();

/**
 * Bulk Yoast sync: overwrite post_content for every published page using the
 * text collected from custom meta fields, replacing any existing post_content.
 *
 * Triggered by visiting any admin page with ?anna_sync_yoast=1 in the URL,
 * e.g. wp-admin/index.php?anna_sync_yoast=1
 *
 * The action is protected by a capability check and can be run as many times
 * as needed — useful after content edits outside the normal save flow, or
 * when deploying this feature for the first time.
 *
 * After the initial run, individual saves keep post_content up to date
 * automatically via the save_post_page hook, so re-running this URL is only
 * needed if you want to force a full re-sync across all pages at once.
 */
add_action(
	'admin_init',
	function () {
		// Only run when the query param is present and the user has permission.
		if ( empty( $_GET['anna_sync_yoast'] ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$manager = Anna_Content_Manager::instance();

		// Fetch all published pages.
		$pages = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);

		$synced  = 0;

		foreach ( $pages as $page_id ) {
			$manager->sync_post_content_for_yoast( $page_id );
			$synced++;
		}

		// Update the flag with the latest run timestamp.
		update_option( 'anna_yoast_sync_done', time() );

		wp_die(
			sprintf(
				'<p>Yoast sync complete. Synced <strong>%d</strong> page(s).</p>',
				$synced
			),
			'Yoast Sync',
			array( 'back_link' => true )
		);
	}
);
