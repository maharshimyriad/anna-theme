<?php
/**
 * Reviews page: CTA section.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$data = get_query_var( 'anna_reviews_page_content', array() );
if ( empty( $data ) ) {
	$data = anna_get_reviews_page_content();
}
?>

<section class="anna-reviews-page-cta">
	<div class="anna-container anna-container--max">
		<div class="anna-reviews-page-cta__inner anna-reveal">

			<?php if ( ! empty( $data['cta_heading'] ) ) : ?>
				<h2 class="anna-reviews-page-cta__heading"><?php echo esc_html( $data['cta_heading'] ); ?></h2>
			<?php endif; ?>

			<?php if ( ! empty( $data['cta_body'] ) ) : ?>
				<p class="anna-reviews-page-cta__body"><?php echo esc_html( $data['cta_body'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $data['cta_button_text'] ) && ! empty( $data['cta_button_url'] ) ) : ?>
				<a href="<?php echo esc_url( $data['cta_button_url'] ); ?>" class="anna-btn anna-btn--primary anna-reviews-page-cta__btn">
					<?php echo esc_html( $data['cta_button_text'] ); ?>
				</a>
			<?php endif; ?>

		</div>
	</div>
</section>
