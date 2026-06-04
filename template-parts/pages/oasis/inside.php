<?php
/**
 * Oasis "Inside Oasis" — seasonal rhythm (dark).
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

$cards     = isset( $oasis['how_card_items'] ) && is_array( $oasis['how_card_items'] ) ? $oasis['how_card_items'] : array();
$icon_slugs = array( 'restore', 'rejuvenate', 'ground', 'invigorate' );
?>

<section class="anna-oasis-page-section anna-oasis-page-inside">
	<div class="anna-container anna-container--max anna-oasis-page-inside__inner">
		<?php if ( ! empty( $oasis['inside_eyebrow'] ) ) : ?>
			<p class="anna-oasis-page-inside__eyebrow anna-reveal"><?php echo esc_html( $oasis['inside_eyebrow'] ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $oasis['inside_heading'] ) ) : ?>
			<h2 class="anna-oasis-page-inside__heading anna-reveal"><?php echo esc_html( $oasis['inside_heading'] ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $oasis['inside_body'] ) ) : ?>
			<p class="anna-oasis-page-inside__intro anna-reveal"><?php echo esc_html( $oasis['inside_body'] ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $cards ) ) : ?>
			<div class="anna-oasis-page-inside__grid anna-stagger">
				<?php foreach ( $cards as $index => $card ) : ?>
					<?php
					if ( ! is_array( $card ) ) {
						continue;
					}
					$icon  = sanitize_key( $card['icon'] ?? $icon_slugs[ $index ] ?? 'restore' );
					$title = trim( (string) ( $card['title'] ?? '' ) );
					$body  = trim( (string) ( $card['body'] ?? '' ) );
					if ( '' === $title && '' === $body ) {
						continue;
					}
					?>
					<article class="anna-oasis-page-inside-card anna-oasis-page-inside-card--<?php echo esc_attr( $icon ); ?>">
						<span class="anna-oasis-page-inside-card__icon" aria-hidden="true"></span>
						<?php if ( '' !== $title ) : ?>
							<h3 class="anna-oasis-page-inside-card__title"><?php echo esc_html( $title ); ?></h3>
						<?php endif; ?>
						<?php if ( '' !== $body ) : ?>
							<p class="anna-oasis-page-inside-card__body"><?php echo esc_html( $body ); ?></p>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $oasis['how_footer'] ) ) : ?>
			<p class="anna-oasis-page-inside__footer"><?php echo esc_html( $oasis['how_footer'] ); ?></p>
		<?php endif; ?>
	</div>
</section>
