<?php
/**
 * Reviews page: reviews grid section.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$data    = get_query_var( 'anna_reviews_page_content', array() );
if ( empty( $data ) ) {
	$data = anna_get_reviews_page_content();
}

$reviews = function_exists( 'anna_get_reviews' ) ? anna_get_reviews() : array();
?>

<section class="anna-reviews-page-grid">
	<div class="anna-container anna-container--max">

		<?php if ( ! empty( $reviews ) ) : ?>
			<div class="anna-reviews-page-grid__cards anna-stagger">
				<?php foreach ( $reviews as $review ) : ?>
					<article class="anna-review-card">
						<header class="anna-review-card__header">
							<div class="anna-review-card__meta">
								<strong class="anna-review-card__name"><?php echo esc_html( $review['name'] ); ?></strong>
								<?php if ( ! empty( $review['date_text'] ) ) : ?>
									<span class="anna-review-card__date"><?php echo esc_html( $review['date_text'] ); ?></span>
								<?php endif; ?>
							</div>
							<div class="anna-review-card__stars">
								<?php anna_star_rating( $review['rating'] ); ?>
							</div>
						</header>
						<?php if ( ! empty( $review['quote'] ) ) : ?>
							<blockquote class="anna-review-card__quote">
								<p><?php echo esc_html( $review['quote'] ); ?></p>
							</blockquote>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<p class="anna-reviews-page-grid__empty"><?php esc_html_e( 'No reviews yet — check back soon.', 'anna-baylis' ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $data['google_reviews_url'] ) ) : ?>
			<div class="anna-reviews-page-grid__footer anna-reveal">
				<a
					href="<?php echo esc_url( $data['google_reviews_url'] ); ?>"
					class="anna-reviews-page-grid__all-link"
					target="_blank"
					rel="noopener noreferrer"
				>
					<?php echo esc_html( $data['google_reviews_text'] ?? __( 'View all reviews on Google', 'anna-baylis' ) ); ?>
					<span aria-hidden="true"> →</span>
				</a>
			</div>
		<?php endif; ?>

	</div>
</section>
