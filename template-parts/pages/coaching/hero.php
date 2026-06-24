<?php
/**
 * Coaching page hero section.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$coaching = get_query_var( 'anna_coaching_page_content', array() );
if ( empty( $coaching ) ) {
	$coaching = anna_get_coaching_page_content();
}

$has_bg_image = ! empty( $coaching['hero_image_id'] );
?>

<section
	class="anna-hero-section anna-coaching-page-hero<?php echo $has_bg_image ? ' anna-coaching-page-hero--has-image' : ''; ?>"
>
	<?php if ( $has_bg_image ) :
		$_img_id  = absint( $coaching['hero_image_id'] );
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
		<div class="anna-coaching-page-hero__content anna-reveal">
			<?php if ( ! empty( $coaching['hero_eyebrow'] ) ) : ?>
				<p class="anna-coaching-page-hero__eyebrow"><?php echo esc_html( $coaching['hero_eyebrow'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $coaching['hero_heading'] ) ) : ?>
				<h1 class="anna-coaching-page-hero__heading"><?php echo wp_kses_post( nl2br( (string) $coaching['hero_heading'] ) ); ?></h1>
			<?php endif; ?>

			<?php if ( ! empty( $coaching['hero_description'] ) ) : ?>
				<p class="anna-coaching-page-hero__description"><?php echo esc_html( $coaching['hero_description'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $coaching['hero_button_text'] ) && ! empty( $coaching['hero_button_url'] ) ) : ?>
				<div class="anna-coaching-page-hero__cta">
					<a class="anna-btn anna-coaching-page-hero__btn" href="<?php echo esc_url( $coaching['hero_button_url'] ); ?>">
						<?php echo esc_html( $coaching['hero_button_text'] ); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
