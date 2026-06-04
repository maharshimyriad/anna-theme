<?php
/**
 * Coaching page "How I work" pillars section.
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

$pillars = isset( $coaching['pillar_items'] ) && is_array( $coaching['pillar_items'] ) ? $coaching['pillar_items'] : array();
if ( empty( $pillars ) ) {
	return;
}
?>

<section class="anna-coaching-page-section anna-coaching-page-pillars">
	<div class="anna-container anna-container--max">
		<header class="anna-coaching-page-pillars__header anna-reveal">
			<?php if ( ! empty( $coaching['pillars_eyebrow'] ) ) : ?>
				<p class="anna-coaching-page-pillars__eyebrow"><?php echo esc_html( $coaching['pillars_eyebrow'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $coaching['pillars_heading'] ) ) : ?>
				<h2 class="anna-coaching-page-pillars__heading"><?php echo esc_html( $coaching['pillars_heading'] ); ?></h2>
			<?php endif; ?>
		</header>

		<div class="anna-coaching-page-pillars__grid anna-stagger">
			<?php foreach ( $pillars as $index => $pillar ) : ?>
				<?php
				if ( ! is_array( $pillar ) ) {
					continue;
				}
				$number = trim( (string) ( $pillar['number'] ?? '' ) );
				$title  = trim( (string) ( $pillar['title'] ?? '' ) );
				$body   = trim( (string) ( $pillar['body'] ?? '' ) );

				if ( '' === $number ) {
					$number = str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT );
				}

				if ( '' === $title && '' === $body ) {
					continue;
				}
				?>
				<article class="anna-coaching-page-pillar-card">
					<span class="anna-coaching-page-pillar-card__number"><?php echo esc_html( $number ); ?></span>
					<?php if ( '' !== $title ) : ?>
						<h3 class="anna-coaching-page-pillar-card__title"><?php echo esc_html( $title ); ?></h3>
					<?php endif; ?>
					<?php if ( '' !== $body ) : ?>
						<p class="anna-coaching-page-pillar-card__body"><?php echo esc_html( $body ); ?></p>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
