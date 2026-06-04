<?php
/**
 * Coaching page "What to expect" section.
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

$info_cards = isset( $coaching['expect_info_cards'] ) && is_array( $coaching['expect_info_cards'] ) ? $coaching['expect_info_cards'] : array();
?>

<section class="anna-coaching-page-section anna-coaching-page-expect">
	<div class="anna-container">
		<div class="anna-coaching-page-expect__grid">
			<div class="anna-coaching-page-expect__content anna-reveal--left">
				<?php if ( ! empty( $coaching['expect_eyebrow'] ) ) : ?>
					<p class="anna-coaching-page-expect__eyebrow"><?php echo esc_html( $coaching['expect_eyebrow'] ); ?></p>
				<?php endif; ?>

				<h2 class="anna-coaching-page-expect__heading">
					<?php if ( ! empty( $coaching['expect_heading_line1'] ) ) : ?>
						<span class="anna-coaching-page-expect__heading-primary"><?php echo esc_html( $coaching['expect_heading_line1'] ); ?></span>
					<?php endif; ?>
					<?php if ( ! empty( $coaching['expect_heading_line2'] ) ) : ?>
						<span class="anna-coaching-page-expect__heading-secondary"><?php echo esc_html( $coaching['expect_heading_line2'] ); ?></span>
					<?php endif; ?>
				</h2>

				<?php if ( ! empty( $coaching['expect_body'] ) ) : ?>
					<div class="anna-coaching-page-expect__copy"><?php echo wp_kses_post( wpautop( (string) $coaching['expect_body'] ) ); ?></div>
				<?php endif; ?>

				<?php if ( ! empty( $coaching['expect_quote'] ) ) : ?>
					<blockquote class="anna-coaching-page-expect__quote">
						<p><?php echo esc_html( $coaching['expect_quote'] ); ?></p>
					</blockquote>
				<?php endif; ?>

				<?php if ( ! empty( $coaching['expect_button_text'] ) && ! empty( $coaching['expect_button_url'] ) ) : ?>
					<div class="anna-coaching-page-expect__cta">
						<a class="anna-btn anna-coaching-page-expect__btn" href="<?php echo esc_url( $coaching['expect_button_url'] ); ?>">
							<?php echo esc_html( $coaching['expect_button_text'] ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $info_cards ) ) : ?>
				<div class="anna-coaching-page-expect__cards anna-stagger">
					<?php foreach ( $info_cards as $card ) : ?>
						<?php
						if ( ! is_array( $card ) ) {
							continue;
						}
						$label = trim( (string) ( $card['label'] ?? '' ) );
						$body  = trim( (string) ( $card['body'] ?? '' ) );
						if ( '' === $label && '' === $body ) {
							continue;
						}
						?>
						<article class="anna-coaching-page-expect-card">
							<?php if ( '' !== $label ) : ?>
								<p class="anna-coaching-page-expect-card__label"><?php echo esc_html( $label ); ?></p>
							<?php endif; ?>
							<?php if ( '' !== $body ) : ?>
								<p class="anna-coaching-page-expect-card__body"><?php echo esc_html( $body ); ?></p>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
