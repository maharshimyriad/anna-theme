<?php
/**
 * Coaching page "What this is" section.
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

$card_items = isset( $coaching['what_card_items'] ) && is_array( $coaching['what_card_items'] ) ? $coaching['what_card_items'] : array();
?>

<section class="anna-coaching-page-section anna-coaching-page-what">
	<div class="anna-container anna-container--max">
		<div class="anna-coaching-page-what__grid">
			<div class="anna-coaching-page-what__content">
				<?php if ( ! empty( $coaching['what_eyebrow'] ) ) : ?>
					<p class="anna-coaching-page-what__eyebrow"><?php echo esc_html( $coaching['what_eyebrow'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $coaching['what_heading'] ) ) : ?>
					<h2 class="anna-coaching-page-what__heading"><?php echo esc_html( $coaching['what_heading'] ); ?></h2>
				<?php endif; ?>

				<?php if ( ! empty( $coaching['what_body'] ) ) : ?>
					<div class="anna-coaching-page-what__copy"><?php echo wp_kses_post( wpautop( (string) $coaching['what_body'] ) ); ?></div>
				<?php endif; ?>

				<?php if ( ! empty( $coaching['what_button_text'] ) && ! empty( $coaching['what_button_url'] ) ) : ?>
					<div class="anna-coaching-page-what__cta">
						<a class="anna-btn anna-coaching-page-what__btn" href="<?php echo esc_url( $coaching['what_button_url'] ); ?>">
							<?php echo esc_html( $coaching['what_button_text'] ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $card_items ) || ! empty( $coaching['what_card_heading'] ) ) : ?>
				<aside class="anna-coaching-page-what__card">
					<?php if ( ! empty( $coaching['what_card_heading'] ) ) : ?>
						<h3 class="anna-coaching-page-what__card-heading"><?php echo esc_html( $coaching['what_card_heading'] ); ?></h3>
					<?php endif; ?>

					<?php if ( ! empty( $card_items ) ) : ?>
						<ul class="anna-coaching-page-what__card-list" role="list">
							<?php foreach ( $card_items as $item ) : ?>
								<?php
								$text = is_array( $item ) ? (string) ( $item['text'] ?? '' ) : (string) $item;
								if ( '' === trim( $text ) ) {
									continue;
								}
								?>
								<li class="anna-coaching-page-what__card-item">
									<span class="anna-coaching-page-what__card-bullet" aria-hidden="true"></span>
									<span><?php echo esc_html( $text ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</aside>
			<?php endif; ?>
		</div>
	</div>
</section>
