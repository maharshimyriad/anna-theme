<?php
/**
 * MOVE page evolved CTA section.
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
?>

<section class="anna-move-page-section anna-move-page-evolved">
	<div class="anna-container anna-container--max anna-move-page-evolved__inner anna-reveal">
		<?php if ( ! empty( $move['evolved_heading'] ) ) : ?>
			<h2 class="anna-move-page-evolved__heading"><?php echo esc_html( $move['evolved_heading'] ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $move['evolved_body'] ) ) : ?>
			<p class="anna-move-page-evolved__body"><?php echo esc_html( $move['evolved_body'] ); ?></p>
		<?php endif; ?>

		<div class="anna-move-page-evolved__actions">
			<?php if ( ! empty( $move['evolved_button_primary_text'] ) && ! empty( $move['evolved_button_primary_url'] ) ) : ?>
				<a class="anna-btn anna-move-page-evolved__btn anna-move-page-evolved__btn--primary" href="<?php echo esc_url( $move['evolved_button_primary_url'] ); ?>">
					<?php echo esc_html( $move['evolved_button_primary_text'] ); ?>
				</a>
			<?php endif; ?>

			<?php if ( ! empty( $move['evolved_button_secondary_text'] ) && ! empty( $move['evolved_button_secondary_url'] ) ) : ?>
				<a class="anna-btn anna-move-page-evolved__btn anna-move-page-evolved__btn--outline" href="<?php echo esc_url( $move['evolved_button_secondary_url'] ); ?>">
					<?php echo esc_html( $move['evolved_button_secondary_text'] ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
