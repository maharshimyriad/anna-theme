<?php
/**
 * Speaking talk formats section.
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

$cards    = isset( $speaking['formats_card_items'] ) && is_array( $speaking['formats_card_items'] ) ? $speaking['formats_card_items'] : array();
$audience = isset( $speaking['formats_audience_items'] ) && is_array( $speaking['formats_audience_items'] ) ? $speaking['formats_audience_items'] : array();
?>

<section class="anna-speaking-page-section anna-speaking-page-formats">
	<div class="anna-container anna-container--max">
		<header class="anna-speaking-page-formats__header anna-reveal">
			<?php if ( ! empty( $speaking['formats_eyebrow'] ) ) : ?>
				<p class="anna-speaking-page-formats__eyebrow"><?php echo esc_html( $speaking['formats_eyebrow'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $speaking['formats_heading'] ) ) : ?>
				<h2 class="anna-speaking-page-formats__heading"><?php echo esc_html( $speaking['formats_heading'] ); ?></h2>
			<?php endif; ?>
		</header>

		<?php if ( ! empty( $cards ) ) : ?>
			<div class="anna-speaking-page-formats__grid anna-stagger">
				<?php foreach ( $cards as $card ) : ?>
					<?php
					if ( ! is_array( $card ) ) {
						continue;
					}
					$number = trim( (string) ( $card['number'] ?? '' ) );
					$title  = trim( (string) ( $card['title'] ?? '' ) );
					$body   = trim( (string) ( $card['body'] ?? '' ) );
					if ( '' === $title ) {
						continue;
					}
					?>
					<article class="anna-speaking-page-format-card">
						<?php if ( '' !== $number ) : ?>
							<span class="anna-speaking-page-format-card__number" aria-hidden="true"><?php echo esc_html( $number ); ?></span>
						<?php endif; ?>
						<h3 class="anna-speaking-page-format-card__title"><?php echo esc_html( $title ); ?></h3>
						<?php if ( '' !== $body ) : ?>
							<p class="anna-speaking-page-format-card__body"><?php echo esc_html( $body ); ?></p>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $speaking['formats_audience_heading'] ) || ! empty( $audience ) ) : ?>
			<div class="anna-speaking-page-formats__audience">
				<?php if ( ! empty( $speaking['formats_audience_heading'] ) ) : ?>
					<h3 class="anna-speaking-page-formats__audience-heading"><?php echo esc_html( $speaking['formats_audience_heading'] ); ?></h3>
				<?php endif; ?>

				<?php if ( ! empty( $audience ) ) : ?>
					<ul class="anna-speaking-page-formats__audience-list" role="list">
						<?php foreach ( $audience as $item ) : ?>
							<?php
							$text = is_array( $item ) ? (string) ( $item['text'] ?? '' ) : (string) $item;
							if ( '' === trim( $text ) ) {
								continue;
							}
							?>
							<li><?php echo esc_html( $text ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
