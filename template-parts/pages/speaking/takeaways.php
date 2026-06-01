<?php
/**
 * Speaking takeaways section.
 *
 * @package Anna_Baylis
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$speaking = get_query_var( 'anna_speaking_page_content', array() );
if ( empty( $speaking ) ) {
	$speaking = anna_get_speaking_page_content();
}
?>

<section class="anna-speaking-section anna-speaking-takeaways">
	<div class="anna-container anna-container--max">
		<div class="anna-speaking-takeaways__grid">
			<div class="anna-speaking-takeaways__content">
				<p class="anna-speaking-eyebrow"><?php echo esc_html( $speaking['takeaways_eyebrow'] ?? '' ); ?></p>
				<h2 class="anna-speaking-section__heading anna-speaking-section__heading--light">
					<span><?php echo esc_html( $speaking['takeaways_heading_primary'] ?? '' ); ?></span>
					<span><?php echo esc_html( $speaking['takeaways_heading_secondary'] ?? '' ); ?></span>
				</h2>
				<?php if ( ! empty( $speaking['takeaways_body'] ) ) : ?>
					<div class="anna-speaking-takeaways__copy"><?php echo wp_kses_post( wpautop( (string) $speaking['takeaways_body'] ) ); ?></div>
				<?php endif; ?>
			</div>

			<ul class="anna-speaking-takeaways__list">
				<?php foreach ( $speaking['takeaway_items'] as $item ) : ?>
					<li>
						<span aria-hidden="true">✓</span>
						<p><?php echo wp_kses_post( anna_format_coaching_emphasis_text( $item['text'] ?? '' ) ); ?></p>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>
