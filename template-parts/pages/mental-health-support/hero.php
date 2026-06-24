<?php
/**
 * Mental health support page hero.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mhs = get_query_var( 'anna_mhs_page_content', array() );
if ( empty( $mhs ) ) {
	$mhs = anna_get_mhs_page_content();
}

$has_image = ! empty( $mhs['hero_image_id'] );
?>

<section
	class="anna-hero-section anna-mhs-page-hero<?php echo $has_image ? ' anna-mhs-page-hero--has-image' : ''; ?>"
>
	<?php if ( $has_image ) :
		$_img_id  = absint( $mhs['hero_image_id'] );
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
	<div class="anna-mhs-page-hero__overlay" aria-hidden="true"></div>
	<div class="anna-container anna-container--max">
		<div class="anna-mhs-page-hero__content anna-reveal">
			<?php if ( ! empty( $mhs['hero_eyebrow'] ) ) : ?>
				<p class="anna-mhs-page-hero__eyebrow"><?php echo esc_html( $mhs['hero_eyebrow'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $mhs['hero_heading'] ) ) : ?>
				<h1 class="anna-mhs-page-hero__heading"><?php echo esc_html( $mhs['hero_heading'] ); ?></h1>
			<?php endif; ?>
		</div>
	</div>
</section>
