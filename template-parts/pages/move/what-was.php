<?php
/**
 * Template part: Move page what was.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$data = get_query_var( 'anna_move_page_content' );
$heading = $data['what_heading'] ?? '';
$body    = $data['what_body'] ?? '';

$quotes = array();
for ( $i = 1; $i <= 4; $i++ ) {
	$quote = $data['quote_' . $i] ?? '';
	if ( $quote ) {
		$quotes[] = $quote;
	}
}
?>

<section class="anna-section anna-section--md anna-move-what">
	<div class="anna-container anna-container--narrow">
		<div class="anna-move-what__text" data-reveal="fade-up">
			<?php if ( $heading ) : ?>
				<h2 class="anna-heading-2 anna-text-primary"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( $body ) : ?>
				<div class="anna-body-text">
					<?php echo wpautop( esc_html( $body ) ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( ! empty( $quotes ) ) : ?>
		<div class="anna-section anna-section--primary anna-section--md anna-move-quotes" data-reveal="fade-up">
			<div class="anna-container">
				<h3 class="anna-heading-3 anna-text-center anna-text-white"><?php esc_html_e( 'What women said', 'anna-baylis' ); ?></h3>
				<div class="anna-grid anna-grid--split-6-6 anna-move-quotes__grid">
					<?php foreach ( $quotes as $quote ) : ?>
						<div class="anna-move-quotes__item">
							<p class="anna-move-quotes__text">"<?php echo esc_html( $quote ); ?>"</p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</section>
