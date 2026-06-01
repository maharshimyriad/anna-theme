<?php
/**
 * Speaking stage section.
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

<section class="anna-speaking-section anna-speaking-stage">
	<div class="anna-container anna-container--max">
		<div class="anna-speaking-stage__grid">
			<div class="anna-speaking-stage__content">
				<p class="anna-speaking-eyebrow"><?php echo esc_html( $speaking['stage_eyebrow'] ?? '' ); ?></p>
				<h2 class="anna-speaking-section__heading">
					<span><?php echo esc_html( $speaking['stage_heading_primary'] ?? '' ); ?></span>
					<span><?php echo esc_html( $speaking['stage_heading_secondary'] ?? '' ); ?></span>
				</h2>
				<?php if ( ! empty( $speaking['stage_body'] ) ) : ?>
					<div class="anna-speaking-stage__copy"><?php echo wp_kses_post( wpautop( (string) $speaking['stage_body'] ) ); ?></div>
				<?php endif; ?>
				<?php if ( ! empty( $speaking['stage_quote'] ) ) : ?>
					<blockquote class="anna-speaking-stage__quote">
						<p><?php echo esc_html( $speaking['stage_quote'] ); ?></p>
					</blockquote>
				<?php endif; ?>
				<?php if ( ! empty( $speaking['stage_button_text'] ) && ! empty( $speaking['stage_button_url'] ) ) : ?>
					<a class="anna-speaking-btn anna-speaking-btn--primary" href="<?php echo esc_url( $speaking['stage_button_url'] ); ?>">
						<?php echo esc_html( $speaking['stage_button_text'] ); ?>
					</a>
				<?php endif; ?>
			</div>

			<figure class="anna-speaking-stage__media">
				<?php if ( ! empty( $speaking['stage_image_id'] ) ) : ?>
					<?php anna_responsive_image( absint( $speaking['stage_image_id'] ), 'large', 'anna-img-cover' ); ?>
				<?php else : ?>
					<img src="<?php echo esc_url( ANNA_IMAGES . '/speaking-stage-fallback.png' ); ?>" alt="" class="anna-img-cover">
				<?php endif; ?>
			</figure>
		</div>
	</div>
</section>
