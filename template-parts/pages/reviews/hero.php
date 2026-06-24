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
	class="anna-hero-section anna-reviews-page-hero<?php echo $has_image ? ' anna-reviews-page-hero--has-image' : ''; ?>"
>
	<?php if ( $has_image ) :
		$_img_id  = absint( $data['hero_image_id'] );
		$_srcset  = wp_get_attachment_image_srcset( $_img_id, 'full' );
		$_alt     = get_post_meta( $_img_id, '_wp_attachment_image_alt', true );
	?>
		<img
			class="anna-hero__bg-image"
			src="<?php echo esc_url( anna_responsive_image_url( $_img_id, 'full' ) ); ?>"
			<?php if ( $_srcset ) : ?>srcset="<?php echo esc_attr( $_srcset ); ?>" sizes="100vw"<?php endif; ?>
			alt="<?php echo esc_attr( $_alt ); ?>"
			loading="eager"
			fetchpriority="high"
			decoding="async"
		>
	<?php endif; ?>
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
