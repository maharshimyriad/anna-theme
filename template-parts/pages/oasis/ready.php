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
						<svg width="14" height="11" viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M1 5.5L5 9.5L13 1.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</span>
					<span class="anna-oasis-page-ready-card__text"><?php echo esc_html( $text ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
