<?php
/**
 * MOVE page what M.O.V.E was section.
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

<section class="anna-move-page-section anna-move-page-was">
	<div class="anna-container anna-container--max">
		<div class="anna-move-page-was__content anna-reveal">
			<?php if ( ! empty( $move['was_heading'] ) ) : ?>
				<h2 class="anna-move-page__heading"><?php echo esc_html( $move['was_heading'] ); ?></h2>
			<?php endif; ?>

			<?php if ( ! empty( $move['was_body'] ) ) : ?>
				<div class="anna-move-page__copy"><?php echo wp_kses_post( wpautop( (string) $move['was_body'] ) ); ?></div>
			<?php endif; ?>
		</div>
	</div>
</section>
