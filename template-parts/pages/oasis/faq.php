<?php
/**
 * Oasis FAQ section.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$oasis = get_query_var( 'anna_oasis_page_content', array() );
if ( empty( $oasis ) ) {
	$oasis = anna_get_oasis_page_content();
}

$faq_items = isset( $oasis['faq_items'] ) && is_array( $oasis['faq_items'] ) ? $oasis['faq_items'] : array();
if ( empty( $faq_items ) ) {
	return;
}

$midpoint = (int) ceil( count( $faq_items ) / 2 );
$left     = array_slice( $faq_items, 0, $midpoint );
$right    = array_slice( $faq_items, $midpoint );
?>

<section class="anna-oasis-page-section anna-oasis-page-faq">
	<div class="anna-container anna-container--max">
		<?php if ( ! empty( $oasis['faq_heading'] ) ) : ?>
			<h2 class="anna-oasis-page-faq__heading anna-reveal"><?php echo esc_html( $oasis['faq_heading'] ); ?></h2>
		<?php endif; ?>

		<div class="anna-oasis-page-faq__grid anna-reveal" data-anna-oasis-faq>
			<div class="anna-oasis-page-faq__column">
				<?php foreach ( $left as $index => $item ) : ?>
					<?php anna_render_oasis_faq_item( $item, $index ); ?>
				<?php endforeach; ?>
			</div>
			<div class="anna-oasis-page-faq__column">
				<?php foreach ( $right as $index => $item ) : ?>
					<?php anna_render_oasis_faq_item( $item, $index + $midpoint ); ?>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
