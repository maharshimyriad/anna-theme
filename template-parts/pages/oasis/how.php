<?php
/**
 * Oasis "How it works" section.
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

$cards = isset( $oasis['how_card_items'] ) && is_array( $oasis['how_card_items'] ) ? $oasis['how_card_items'] : array();
$icon_slugs = array( 'roots', 'expression', 'growth', 'integration' );
?>

<section class="anna-oasis-page-section anna-oasis-page-how">
	<div class="anna-container anna-container--max">
		<?php if ( ! empty( $oasis['how_eyebrow'] ) ) : ?>
			<p class="anna-oasis-page-how__eyebrow"><?php echo esc_html( $oasis['how_eyebrow'] ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $oasis['how_heading'] ) ) : ?>
			<h2 class="anna-oasis-page-how__heading"><?php echo esc_html( $oasis['how_heading'] ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $oasis['how_intro'] ) ) : ?>
			<p class="anna-oasis-page-how__intro"><?php echo esc_html( $oasis['how_intro'] ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $cards ) ) : ?>
			<div class="anna-oasis-page-how__grid">
				<?php foreach ( $cards as $index => $card ) : ?>
					<?php
					if ( ! is_array( $card ) ) {
						continue;
					}
					$icon  = sanitize_key( $card['icon'] ?? $icon_slugs[ $index ] ?? 'roots' );
					$title = trim( (string) ( $card['title'] ?? '' ) );
					$body  = trim( (string) ( $card['body'] ?? '' ) );
					if ( '' === $title && '' === $body ) {
						continue;
					}
					?>
					<article class="anna-oasis-page-how-card anna-oasis-page-how-card--<?php echo esc_attr( $icon ); ?>">
						<span class="anna-oasis-page-how-card__icon" aria-hidden="true"></span>
						<?php if ( '' !== $title ) : ?>
							<h3 class="anna-oasis-page-how-card__title"><?php echo esc_html( $title ); ?></h3>
						<?php endif; ?>
						<?php if ( '' !== $body ) : ?>
							<p class="anna-oasis-page-how-card__body"><?php echo esc_html( $body ); ?></p>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $oasis['how_footer'] ) ) : ?>
			<p class="anna-oasis-page-how__footer"><?php echo esc_html( $oasis['how_footer'] ); ?></p>
		<?php endif; ?>
	</div>
</section>
