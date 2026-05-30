<?php
/**
 * Coaching page "What we work on" section.
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

$topics = isset( $coaching['work_topics_items'] ) && is_array( $coaching['work_topics_items'] ) ? $coaching['work_topics_items'] : array();
$gains  = isset( $coaching['work_gains_items'] ) && is_array( $coaching['work_gains_items'] ) ? $coaching['work_gains_items'] : array();
?>

<section class="anna-coaching-page-section anna-coaching-page-work">
	<div class="anna-container">
		<div class="anna-coaching-page-work__grid">
			<div class="anna-coaching-page-work__topics">
				<?php if ( ! empty( $coaching['work_eyebrow'] ) ) : ?>
					<p class="anna-coaching-page-work__eyebrow"><?php echo esc_html( $coaching['work_eyebrow'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $coaching['work_heading'] ) ) : ?>
					<h2 class="anna-coaching-page-work__heading"><?php echo esc_html( $coaching['work_heading'] ); ?></h2>
				<?php endif; ?>

				<?php if ( ! empty( $topics ) ) : ?>
					<ul class="anna-coaching-page-work__list" role="list">
						<?php foreach ( $topics as $item ) : ?>
							<?php
							$text = is_array( $item ) ? (string) ( $item['text'] ?? '' ) : (string) $item;
							if ( '' === trim( $text ) ) {
								continue;
							}
							?>
							<li class="anna-coaching-page-work__list-item">
								<span class="anna-coaching-page-work__bullet" aria-hidden="true"></span>
								<span class="anna-coaching-page-work__list-text"><?php echo esc_html( $text ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<div class="anna-coaching-page-work__gains">
				<?php if ( ! empty( $coaching['work_gains_heading'] ) ) : ?>
					<h3 class="anna-coaching-page-work__gains-heading"><?php echo esc_html( $coaching['work_gains_heading'] ); ?></h3>
				<?php endif; ?>

				<?php if ( ! empty( $gains ) ) : ?>
					<ul class="anna-coaching-page-work__gains-list" role="list">
						<?php foreach ( $gains as $item ) : ?>
							<?php
							$text = is_array( $item ) ? (string) ( $item['text'] ?? '' ) : (string) $item;
							if ( '' === trim( $text ) ) {
								continue;
							}
							?>
							<li class="anna-coaching-page-work__gain-card">
								<span class="anna-coaching-page-work__gain-icon" aria-hidden="true">
									<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M3.5 9.2L7.1 12.8L14.5 5.4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</span>
								<span class="anna-coaching-page-work__gain-text"><?php echo wp_kses_post( anna_format_coaching_emphasis_text( $text ) ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
