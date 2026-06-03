<?php
/**
 * MOVE page evolution section.
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

$gallery_items = isset( $move['evolution_gallery_items'] ) && is_array( $move['evolution_gallery_items'] ) ? $move['evolution_gallery_items'] : array();
?>

<section class="anna-move-page-section anna-move-page-evolution">
	<div class="anna-container anna-container--max">
		<?php if ( ! empty( $move['evolution_heading'] ) ) : ?>
			<h2 class="anna-move-page__heading"><?php echo esc_html( $move['evolution_heading'] ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $move['evolution_body'] ) ) : ?>
			<div class="anna-move-page__copy anna-move-page-evolution__copy"><?php echo wp_kses_post( wpautop( (string) $move['evolution_body'] ) ); ?></div>
		<?php endif; ?>

		<?php if ( ! empty( $move['evolution_callout'] ) ) : ?>
			<p class="anna-move-page-evolution__callout"><?php echo esc_html( $move['evolution_callout'] ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $gallery_items ) ) : ?>
			<div class="anna-move-page-evolution__gallery-wrap">
				<?php if ( ! empty( $move['evolution_gallery_heading'] ) ) : ?>
					<h3 class="anna-move-page-evolution__gallery-heading"><?php echo esc_html( $move['evolution_gallery_heading'] ); ?></h3>
				<?php endif; ?>

				<div class="anna-move-page-evolution__gallery">
					<?php foreach ( $gallery_items as $index => $item ) : ?>
						<?php
						$image_id = absint( $item['image_id'] ?? 0 );
						if ( ! $image_id ) {
							continue;
						}
						$wide = 0 === $index || 3 === $index;
						?>
						<figure class="anna-move-page-evolution__gallery-item<?php echo $wide ? ' anna-move-page-evolution__gallery-item--wide' : ''; ?>">
							<?php anna_responsive_image( $image_id, 'large', 'anna-img-cover' ); ?>
						</figure>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
