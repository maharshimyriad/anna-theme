<?php
/**
 * MOVE page hero.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$move = get_query_var( 'anna_move_page_content', array() );
if ( empty( $move ) ) {
	$move = anna_get_move_page_content();
}

$has_image = ! empty( $move['hero_image_id'] );
?>

<section
	class="anna-hero-section anna-move-page-hero<?php echo $has_image ? ' anna-move-page-hero--has-image' : ''; ?>"
>
	<?php if ( $has_image ) :
		$_img_id  = absint( $move['hero_image_id'] );
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
	<div class="anna-move-page-hero__overlay" aria-hidden="true"></div>
	<div class="anna-container anna-container--max">
		<div class="anna-move-page-hero__content anna-reveal">
			<?php if ( ! empty( $move['hero_eyebrow'] ) ) : ?>
				<p class="anna-move-page-hero__eyebrow"><?php echo esc_html( $move['hero_eyebrow'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $move['hero_heading'] ) ) : ?>
				<h1 class="anna-move-page-hero__heading"><?php echo esc_html( $move['hero_heading'] ); ?></h1>
			<?php endif; ?>
		</div>
	</div>
</section>
