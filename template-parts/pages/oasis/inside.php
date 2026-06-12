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
					$icon_svg = trim( (string) ( $card['icon'] ?? '' ) );
					$title    = trim( (string) ( $card['title'] ?? '' ) );
					$body     = trim( (string) ( $card['body'] ?? '' ) );
					if ( '' === $title && '' === $body ) {
						continue;
					}
					// SVG allowed tags for inline rendering.
					$svg_allowed = array(
						'svg'      => array( 'width' => true, 'height' => true, 'viewbox' => true, 'viewBox' => true, 'fill' => true, 'xmlns' => true, 'class' => true, 'aria-hidden' => true, 'focusable' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true ),
						'path'     => array( 'd' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'opacity' => true, 'fill-rule' => true, 'clip-rule' => true ),
						'circle'   => array( 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true ),
						'rect'     => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'ry' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true ),
						'line'     => array( 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true ),
						'polyline' => array( 'points' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true ),
						'polygon'  => array( 'points' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true ),
						'g'        => array( 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'opacity' => true, 'transform' => true ),
					);
					?>
					<article class="anna-oasis-page-inside-card">
						<?php if ( '' !== $icon_svg ) : ?>
							<span class="anna-oasis-page-inside-card__icon" aria-hidden="true">
								<?php echo wp_kses( $icon_svg, $svg_allowed ); ?>
							</span>
						<?php endif; ?>
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
