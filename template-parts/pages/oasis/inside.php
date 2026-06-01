<?php
/**
 * Oasis "Inside Oasis" — pills and weekly schedule.
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
$schedule = isset( $oasis['inside_schedule_items'] ) && is_array( $oasis['inside_schedule_items'] ) ? $oasis['inside_schedule_items'] : array();
?>

<section class="anna-oasis-page-section anna-oasis-page-inside">
	<div class="anna-container anna-container--max">
		<div class="anna-oasis-page-inside__grid">
			<div class="anna-oasis-page-inside__content">
				<?php if ( ! empty( $oasis['inside_eyebrow'] ) ) : ?>
					<p class="anna-oasis-page-inside__eyebrow"><?php echo esc_html( $oasis['inside_eyebrow'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $oasis['inside_heading'] ) ) : ?>
					<h2 class="anna-oasis-page-inside__heading"><?php echo esc_html( $oasis['inside_heading'] ); ?></h2>
				<?php endif; ?>

				<?php if ( ! empty( $oasis['inside_body'] ) ) : ?>
					<div class="anna-oasis-page-inside__copy"><?php echo wp_kses_post( wpautop( (string) $oasis['inside_body'] ) ); ?></div>
				<?php endif; ?>

				<?php if ( ! empty( $oasis['inside_highlight'] ) ) : ?>
					<p class="anna-oasis-page-inside__highlight"><?php echo esc_html( $oasis['inside_highlight'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $oasis['inside_pills_intro'] ) ) : ?>
					<p class="anna-oasis-page-inside__pills-intro"><?php echo esc_html( $oasis['inside_pills_intro'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $pills ) ) : ?>
					<ul class="anna-oasis-page-inside__pills" role="list">
						<?php foreach ( $pills as $item ) : ?>
							<?php
							$text = is_array( $item ) ? (string) ( $item['text'] ?? '' ) : (string) $item;
							if ( '' === trim( $text ) ) {
								continue;
							}
							?>
							<li class="anna-oasis-page-inside__pill"><?php echo esc_html( $text ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $schedule ) ) : ?>
				<div class="anna-oasis-page-inside__schedule">
					<ul class="anna-oasis-page-inside__schedule-list" role="list">
						<?php foreach ( $schedule as $item ) : ?>
							<?php
							if ( ! is_array( $item ) ) {
								continue;
							}
							$title = trim( (string) ( $item['title'] ?? '' ) );
							$body  = trim( (string) ( $item['body'] ?? '' ) );
							if ( '' === $title && '' === $body ) {
								continue;
							}
							?>
							<li class="anna-oasis-page-inside__schedule-item">
								<span class="anna-oasis-page-inside__schedule-icon" aria-hidden="true">
									<svg width="14" height="11" viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M1 5.5L5 9.5L13 1.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</span>
								<div class="anna-oasis-page-inside__schedule-text">
									<?php if ( '' !== $title ) : ?>
										<strong><?php echo esc_html( $title ); ?></strong>
									<?php endif; ?>
									<?php if ( '' !== $body ) : ?>
										<span><?php echo esc_html( $body ); ?></span>
									<?php endif; ?>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
