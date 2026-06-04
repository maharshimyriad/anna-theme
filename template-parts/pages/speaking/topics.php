<?php
/**
 * Speaking topics section.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$speaking = get_query_var( 'anna_speaking_page_content', array() );
if ( empty( $speaking ) ) {
	$speaking = anna_get_speaking_page_content();
}

$cards = isset( $speaking['topics_card_items'] ) && is_array( $speaking['topics_card_items'] ) ? $speaking['topics_card_items'] : array();
?>

<section id="speaking-topics" class="anna-speaking-page-section anna-speaking-page-topics">
	<div class="anna-container anna-container--max">
		<header class="anna-speaking-page-topics__header anna-reveal">
			<?php if ( ! empty( $speaking['topics_eyebrow'] ) ) : ?>
				<p class="anna-speaking-page-topics__eyebrow"><?php echo esc_html( $speaking['topics_eyebrow'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $speaking['topics_heading'] ) ) : ?>
				<h2 class="anna-speaking-page-topics__heading"><?php echo esc_html( $speaking['topics_heading'] ); ?></h2>
				<h2 class="anna-speaking-page-bring__heading-line--accent">Grounded in lived experience.</h2>
			<?php endif; ?>

			<?php if ( ! empty( $speaking['topics_intro'] ) ) : ?>
				<p class="anna-speaking-page-topics__intro"><?php echo esc_html( $speaking['topics_intro'] ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( ! empty( $cards ) ) : ?>
			<div class="anna-speaking-page-topics__grid anna-stagger">
				<?php foreach ( $cards as $card ) : ?>
					<?php
					if ( ! is_array( $card ) ) {
						continue;
					}
					$icon  = sanitize_key( $card['icon'] ?? 'brain' );
					$title = trim( (string) ( $card['title'] ?? '' ) );
					$body  = trim( (string) ( $card['body'] ?? '' ) );
					if ( '' === $title ) {
						continue;
					}
					?>
					<article class="anna-speaking-page-topic-card anna-speaking-page-topic-card--<?php echo esc_attr( $icon ); ?>">
						<span class="anna-speaking-page-topic-card__icon" aria-hidden="true"></span>
						<h3 class="anna-speaking-page-topic-card__title"><?php echo esc_html( $title ); ?></h3>
						<?php if ( '' !== $body ) : ?>
							<p class="anna-speaking-page-topic-card__body"><?php echo esc_html( $body ); ?></p>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
