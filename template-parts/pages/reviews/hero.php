<?php
/**
 * Reviews page: hero section.
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

$has_image = ! empty( $data['hero_image_id'] );
?>

<section
	class="anna-reviews-page-hero<?php echo $has_image ? ' anna-reviews-page-hero--has-image' : ''; ?>"
	<?php if ( $has_image ) : ?>
		style="background-image:url('<?php echo esc_url( anna_responsive_image_url( absint( $data['hero_image_id'] ), 'full' ) ); ?>');"
	<?php endif; ?>
>
	<div class="anna-reviews-page-hero__overlay" aria-hidden="true"></div>
	<div class="anna-container anna-container--max">
		<div class="anna-reviews-page-hero__content anna-reveal">

			<?php if ( ! empty( $data['hero_eyebrow'] ) ) : ?>
				<p class="anna-reviews-page-hero__eyebrow"><?php echo esc_html( $data['hero_eyebrow'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $data['hero_heading'] ) ) : ?>
				<h1 class="anna-reviews-page-hero__heading"><?php echo wp_kses_post( nl2br( esc_html( $data['hero_heading'] ) ) ); ?></h1>
			<?php endif; ?>

			<?php if ( ! empty( $data['hero_rating_text'] ) ) : ?>
				<div class="anna-reviews-page-hero__rating">
					<?php anna_star_rating( 5 ); ?>
					<span class="anna-reviews-page-hero__rating-text"><?php echo esc_html( $data['hero_rating_text'] ); ?></span>
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>
