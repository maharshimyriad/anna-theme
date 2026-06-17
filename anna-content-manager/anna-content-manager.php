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
 * One-time bulk sync: write post_content for every published page whose
 * post_content is currently empty (or only whitespace).
 *
 * Triggered by visiting any admin page with ?anna_sync_yoast=1 in the URL,
 * e.g. wp-admin/index.php?anna_sync_yoast=1
 *
 * The action is protected by a capability check and fires only once — a flag
 * is stored in the options table so subsequent requests are no-ops.
 *
 * To re-run (e.g. after adding new pages), delete the option from the DB:
 *   DELETE FROM wp_options WHERE option_name = 'anna_yoast_sync_done';
 * or in WP-CLI:
 *   wp option delete anna_yoast_sync_done
 */
add_action(
	'admin_init',
	function () {
		// Only run when the query param is present and the user has permission.
		if ( empty( $_GET['anna_sync_yoast'] ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Guard: only run once (delete the option to re-run).
		if ( get_option( 'anna_yoast_sync_done' ) ) {
			wp_die(
				'<p>Yoast sync already completed. To re-run, delete the <code>anna_yoast_sync_done</code> option from the database.</p>',
				'Yoast Sync',
				array( 'back_link' => true )
			);
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
		$skipped = 0;

		foreach ( $pages as $page_id ) {
			// Only backfill pages that have no post_content yet.
			$current = get_post_field( 'post_content', $page_id );
			if ( '' !== trim( (string) $current ) ) {
				$skipped++;
				continue;
			}

			$manager->sync_post_content_for_yoast( $page_id );
			$synced++;
		}

		// Mark as done so the action won't run again accidentally.
		update_option( 'anna_yoast_sync_done', time() );

		wp_die(
			sprintf(
				'<p>Yoast sync complete. Synced <strong>%d</strong> page(s), skipped <strong>%d</strong> (already had content).</p>',
				$synced,
				$skipped
			),
			'Yoast Sync',
			array( 'back_link' => true )
		);
	}
);
