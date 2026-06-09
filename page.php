<?php
/**
 * Page template.
 * @package Anna_Baylis
 * @since   1.0.0
 */
get_header(); ?>
<main id="main-content" class="anna-main" role="main">
	<?php while ( have_posts() ) : the_post();
		$content = get_the_content();
		// Detect old page builder content (Avada/WPBakery shortcodes) and suppress it.
		$has_builder_content = (
			strpos( $content, '[av_' ) !== false ||
			strpos( $content, '[vc_' ) !== false ||
			strpos( $content, '[fusion_' ) !== false ||
			strpos( $content, 'av_uid=' ) !== false ||
			strpos( $content, 'av_element_hidden_in_editor' ) !== false
		);
		// Also suppress if the page has a custom template — post_content is managed by meta boxes.
		$page_template       = get_page_template_slug( get_the_ID() );
		$managed_templates   = array(
			'page-contact.php', 'page-reviews.php', 'page-oasis.php',
			'page-coaching.php', 'page-speaking.php', 'page-mental-health-support.php',
			'page-move.php', 'page-about.php',
		);
		$is_managed_template = in_array( $page_template, $managed_templates, true );
		if ( $has_builder_content || $is_managed_template ) : ?>
			<?php if ( current_user_can( 'edit_pages' ) ) : ?>
				<div style="margin:2rem auto;max-width:700px;padding:1.25rem 1.5rem;background:#fff3cd;border:1px solid #ffc107;border-radius:6px;font-family:sans-serif;font-size:14px;color:#856404;">
					<strong>Admin notice:</strong> This page contains content from an old page builder. It has been hidden from the frontend.
					Please assign the correct page template under <strong>Page Attributes → Template</strong> on the right side of the editor, then update the page.
				</div>
			<?php endif; ?>
		<?php else : ?>
			<article class="anna-section anna-section--md">
				<div class="anna-container anna-container--narrow">
					<h1 class="anna-heading anna-heading--1"><?php the_title(); ?></h1>
					<div class="anna-post-content" style="margin-top:var(--space-10);">
						<?php the_content(); ?>
					</div>
				</div>
			</article>
		<?php endif; ?>
	<?php endwhile; ?>
</main>
<?php get_footer();
