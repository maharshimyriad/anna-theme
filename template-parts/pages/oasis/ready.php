<?php
/**
 * Oasis "Is Oasis right for you?" section.
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

$items = isset( $oasis['ready_items'] ) && is_array( $oasis['ready_items'] ) ? $oasis['ready_items'] : array();
if ( empty( $items ) ) {
	return;
}
?>

<section id="oasis-ready" class="anna-oasis-page-section anna-oasis-page-ready">
	<div class="anna-container anna-container--max">
		<header class="anna-oasis-page-ready__header">
			<?php if ( ! empty( $oasis['ready_eyebrow'] ) ) : ?>
				<p class="anna-oasis-page-ready__eyebrow"><?php echo esc_html( $oasis['ready_eyebrow'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $oasis['ready_heading'] ) ) : ?>
				<h2 class="anna-oasis-page-ready__heading"><?php echo esc_html( $oasis['ready_heading'] ); ?></h2>
			<?php endif; ?>
		</header>

		<ul class="anna-oasis-page-ready__grid" role="list">
			<?php foreach ( $items as $item ) : ?>
				<?php
				$text = is_array( $item ) ? (string) ( $item['text'] ?? '' ) : (string) $item;
				if ( '' === trim( $text ) ) {
					continue;
				}
				?>
				<li class="anna-oasis-page-ready-card">
					<span class="anna-oasis-page-ready-card__icon" aria-hidden="true">
							<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M9.15632 16.67C6.11867 16.6792 3.5811 14.3612 3.31982 11.3385C3.05855 8.31574 5.16081 5.59763 8.1551 5.08671C12.9109 4.17004 14.1624 3.73671 15.8311 1.67004C16.6655 3.33671 17.4998 5.15338 17.4998 8.33671C17.4998 12.92 13.5116 16.67 9.15632 16.67Z" stroke="#007063" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							<path d="M1.66992 17.5C1.66992 15 3.21215 13.0333 5.90479 12.5C7.92219 12.1 10.0063 10.8333 10.8399 10" stroke="#007063" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>

					</span>
					<span class="anna-oasis-page-ready-card__text"><?php echo esc_html( $text ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
