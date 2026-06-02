<?php
/**
 * Template part: Move page evolution.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = anna_get_option( 'move_pg_evo_heading' );
$body    = anna_get_option( 'move_pg_evo_body' );
$gallery_heading = anna_get_option( 'move_pg_gallery_heading' );

$images = array();
for ( $i = 1; $i <= 5; $i++ ) {
	$img_id = anna_get_option( 'move_pg_gallery_image_' . $i . '_id' );
	if ( $img_id ) {
		$images[] = wp_get_attachment_image( $img_id, 'large', false, array( 'class' => 'anna-move-gallery__img' ) );
	}
}
?>

<section class="anna-section anna-section--md anna-move-evolution">
	<div class="anna-container anna-container--narrow">
		<div class="anna-move-evolution__text" data-reveal="fade-up">
			<?php if ( $heading ) : ?>
				<h2 class="anna-heading-2 anna-text-primary"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( $body ) : ?>
				<div class="anna-body-text">
					<?php echo wpautop( esc_html( $body ) ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( ! empty( $images ) ) : ?>
		<div class="anna-container">
			<div class="anna-move-gallery" data-reveal="fade-up" data-reveal-delay="0.2">
				<?php if ( $gallery_heading ) : ?>
					<h3 class="anna-heading-3 anna-text-center anna-move-gallery__title"><?php echo esc_html( $gallery_heading ); ?></h3>
				<?php endif; ?>
				<div class="anna-grid anna-grid--3-col anna-move-gallery__grid">
					<?php foreach ( $images as $image ) : ?>
						<div class="anna-move-gallery__item">
							<?php echo $image; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</section>
