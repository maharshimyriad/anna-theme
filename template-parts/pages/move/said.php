<?php
/**
 * MOVE page what women said section.
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

$items = isset( $move['said_items'] ) && is_array( $move['said_items'] ) ? $move['said_items'] : array();
if ( empty( $items ) ) {
	return;
}
?>

<section class="anna-move-page-section anna-move-page-section--dark anna-move-page-said">
	<div class="anna-container anna-container--max">
		<?php if ( ! empty( $move['said_heading'] ) ) : ?>
			<h2 class="anna-move-page-said__heading anna-reveal"><?php echo esc_html( $move['said_heading'] ); ?></h2>
		<?php endif; ?>

		<ul class="anna-move-page-said__grid anna-stagger" role="list">
			<?php foreach ( $items as $item ) : ?>
				<?php
				$quote = is_array( $item ) ? (string) ( $item['quote'] ?? '' ) : (string) $item;
				if ( '' === trim( $quote ) ) {
					continue;
				}
				?>
				<li class="anna-move-page-said-card">
					<blockquote class="anna-move-page-said-card__quote"><p><?php echo esc_html( $quote ); ?></p></blockquote>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
