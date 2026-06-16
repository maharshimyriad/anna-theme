<?php
/**
 * MOVE page Google reviews section.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

$move = get_query_var('anna_move_page_content', array());
if (empty($move)) {
	$move = anna_get_move_page_content();
}

$items = isset($move['reviews_items']) && is_array($move['reviews_items']) ? $move['reviews_items'] : array();
$reviews_shortcode = (string) ($move['reviews_shortcode'] ?? '');
$heading = (string) ($move['reviews_heading'] ?? '');
$heading_main = $heading;
$heading_sub = '';

if (false !== stripos($heading, ' Google reviews')) {
	$heading_main = trim(str_ireplace('Google reviews', '', $heading));
	$heading_sub = 'Google reviews';
}
?>

<section class="anna-move-page-section anna-move-page-section--cream anna-move-page-reviews">
	<div class="anna-container anna-container--wide">
		<header class="anna-move-page-reviews__header anna-reveal">
			<?php if (!empty($move['reviews_eyebrow'])): ?>
				<p class="anna-move-page-reviews__eyebrow"><?php echo esc_html($move['reviews_eyebrow']); ?></p>
			<?php endif; ?>

			<?php if ($heading_main): ?>
				<h2 class="anna-move-page-reviews__heading">
					<span class="anna-move-page-reviews__heading-main"><?php echo esc_html($heading_main); ?></span>
					<?php if ($heading_sub): ?>
						<span class="anna-move-page-reviews__heading-sub"><?php echo esc_html($heading_sub); ?></span>
					<?php endif; ?>
				</h2>
			<?php endif; ?>

			<?php if (!empty($move['reviews_summary'])): ?>
			<div class="anna-move-page-review-card__rating"><?php echo anna_star_rating(5); ?>
				<p class="anna-move-page-reviews__summary"><?php echo esc_html($move['reviews_summary']); ?></p>
			<?php endif; ?>
		</header>

		<?php if (!empty($reviews_shortcode)): ?>
			<div class="anna-move-page-reviews__grid anna-stagger" role="list">
				<?php echo do_shortcode(wp_kses_post($reviews_shortcode)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<?php
			// Reviews powered by Reviews Bundle plugin.
			// Manage collections: /wp-admin/edit.php?post_type=brb_collection
			?>
		<?php elseif (!empty($items)): ?>
			<div class="anna-move-page-reviews__grid anna-stagger" role="list">
				<?php foreach ($items as $review): ?>
					<figure class="anna-move-page-review-card" role="listitem">
						<div class="anna-move-page-review-card__rating">
							<?php echo anna_star_rating(absint($review['rating'] ?? 5)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<blockquote class="anna-move-page-review-card__quote">
							<p><?php echo esc_html($review['quote'] ?? ''); ?></p>
						</blockquote>
						<figcaption class="anna-move-page-review-card__author">
							<cite
								class="anna-move-page-review-card__name"><?php echo esc_html($review['name'] ?? ''); ?></cite>
							<?php if (!empty($review['role'])): ?>
								<span class="anna-move-page-review-card__role"><?php echo esc_html($review['role']); ?></span>
							<?php endif; ?>
						</figcaption>
					</figure>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>