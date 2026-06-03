<?php
/**
 * MOVE page four pillars section.
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

$pillars = isset( $move['pillar_items'] ) && is_array( $move['pillar_items'] ) ? $move['pillar_items'] : array();
?>

<section class="anna-move-page-section anna-move-page-pillars">
	<div class="anna-container anna-container--max">
		<?php if ( ! empty( $move['pillars_heading'] ) ) : ?>
			<h2 class="anna-move-page__heading"><?php echo esc_html( $move['pillars_heading'] ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $pillars ) ) : ?>
			<div class="anna-move-page-pillars__list">
				<?php foreach ( $pillars as $pillar ) : ?>
					<article class="anna-move-page-pillar">
						<?php if ( ! empty( $pillar['title'] ) ) : ?>
							<h3 class="anna-move-page-pillar__title"><?php echo esc_html( $pillar['title'] ); ?></h3>
						<?php endif; ?>
						<?php if ( ! empty( $pillar['body'] ) ) : ?>
							<div class="anna-move-page-pillar__body"><?php echo wp_kses_post( wpautop( (string) $pillar['body'] ) ); ?></div>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $move['pillars_body'] ) ) : ?>
			<div class="anna-move-page-pillars__closing"><?php echo wp_kses_post( wpautop( (string) $move['pillars_body'] ) ); ?></div>
		<?php endif; ?>
	</div>
</section>
