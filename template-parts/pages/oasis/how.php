<?php
/**
 * Oasis "How it works" — two-column features.
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

$pills    = isset( $oasis['inside_pill_items'] ) && is_array( $oasis['inside_pill_items'] ) ? $oasis['inside_pill_items'] : array();
$features = isset( $oasis['inside_schedule_items'] ) && is_array( $oasis['inside_schedule_items'] ) ? $oasis['inside_schedule_items'] : array();
?>

<section class="anna-oasis-page-section anna-oasis-page-how">
	<div class="anna-container anna-container--max">
		<div class="anna-oasis-page-how__grid">
			<div class="anna-oasis-page-how__content anna-reveal--left">
				<?php if ( ! empty( $oasis['how_eyebrow'] ) ) : ?>
					<p class="anna-oasis-page-how__eyebrow"><?php echo esc_html( $oasis['how_eyebrow'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $oasis['how_heading'] ) ) : ?>
					<h2 class="anna-oasis-page-how__heading"><?php echo esc_html( $oasis['how_heading'] ); ?></h2>
				<?php endif; ?>

				<?php if ( ! empty( $oasis['how_intro'] ) ) : ?>
					<div class="anna-oasis-page-how__intro"><?php echo wp_kses_post( wpautop( (string) $oasis['how_intro'] ) ); ?></div>
				<?php endif; ?>

				<?php if ( ! empty( $pills ) ) : ?>
					<ul class="anna-oasis-page-how__links" role="list">
						<?php foreach ( $pills as $item ) : ?>
							<?php
							$text = is_array( $item ) ? (string) ( $item['text'] ?? '' ) : (string) $item;
							if ( '' === trim( $text ) ) {
								continue;
							}
							?>
							<li class="anna-oasis-page-how__link-item"><?php echo esc_html( $text ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $features ) ) : ?>
				<div class="anna-oasis-page-how__features anna-reveal--right">
					<ul class="anna-oasis-page-how__features-list" role="list">
						<?php foreach ( $features as $item ) : ?>
							<?php
							if ( ! is_array( $item ) ) {
								continue;
							}
							$title = trim( (string) ( $item['title'] ?? '' ) );
							$body  = trim( (string) ( $item['body'] ?? '' ) );
							if ( '' === $title && '' === $body ) {
								continue;
							}
							$text = '' !== $body ? $title . ' ' . $body : $title;
							?>
							<li class="anna-oasis-page-how__feature">
								<span class="anna-oasis-page-how__feature-icon" aria-hidden="true">
									<svg width="14" height="11" viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M1 5.5L5 9.5L13 1.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</span>
								<span class="anna-oasis-page-how__feature-text"><?php echo esc_html( $text ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
