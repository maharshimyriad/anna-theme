<?php
/**
 * Coaching page FAQ section.
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

$faq_items = isset( $coaching['faq_items'] ) && is_array( $coaching['faq_items'] ) ? $coaching['faq_items'] : array();
if ( empty( $faq_items ) ) {
	return;
}

$midpoint = (int) ceil( count( $faq_items ) / 2 );
$left     = array_slice( $faq_items, 0, $midpoint );
$right    = array_slice( $faq_items, $midpoint );
?>

<section class="anna-coaching-page-section anna-coaching-page-faq">
	<div class="anna-container">
		<?php if ( ! empty( $coaching['faq_heading'] ) ) : ?>
			<h2 class="anna-coaching-page-faq__heading"><?php echo esc_html( $coaching['faq_heading'] ); ?></h2>
		<?php endif; ?>

		<div class="anna-coaching-page-faq__grid" data-anna-coaching-faq>
			<div class="anna-coaching-page-faq__column">
				<?php foreach ( $left as $index => $item ) : ?>
					<?php anna_render_coaching_faq_item( $item, $index ); ?>
				<?php endforeach; ?>
			</div>
			<div class="anna-coaching-page-faq__column">
				<?php foreach ( $right as $index => $item ) : ?>
					<?php anna_render_coaching_faq_item( $item, $index + $midpoint ); ?>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
