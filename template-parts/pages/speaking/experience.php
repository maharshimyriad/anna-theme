<?php
/**
 * Speaking recent experience section.
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

<section class="anna-speaking-section anna-speaking-experience">
	<div class="anna-container anna-container--max">
		<div class="anna-speaking-experience__grid">
			<figure class="anna-speaking-testimonial">
				<?php echo anna_star_rating( 5 ); ?>
				<blockquote>
					<p><?php echo esc_html( $speaking['experience_quote'] ?? '' ); ?></p>
				</blockquote>
				<figcaption>
					<strong><?php echo esc_html( $speaking['experience_author'] ?? '' ); ?></strong>
					<span><?php echo esc_html( $speaking['experience_role'] ?? '' ); ?></span>
				</figcaption>
			</figure>

			<div class="anna-speaking-experience__content">
				<p class="anna-speaking-eyebrow"><?php echo esc_html( $speaking['experience_eyebrow'] ?? '' ); ?></p>
				<h2 class="anna-speaking-section__heading">
					<span><?php echo esc_html( $speaking['experience_heading_primary'] ?? '' ); ?></span>
					<span><?php echo esc_html( $speaking['experience_heading_secondary'] ?? '' ); ?></span>
				</h2>
				<?php if ( ! empty( $speaking['experience_body'] ) ) : ?>
					<div class="anna-speaking-experience__copy"><?php echo wp_kses_post( wpautop( (string) $speaking['experience_body'] ) ); ?></div>
				<?php endif; ?>
				<?php if ( ! empty( $speaking['experience_link_text'] ) && ! empty( $speaking['experience_link_url'] ) ) : ?>
					<p class="anna-speaking-experience__link-wrap">
						<?php esc_html_e( 'You can learn more about the event at', 'anna-baylis' ); ?><br>
						<a href="<?php echo esc_url( $speaking['experience_link_url'] ); ?>" target="_blank" rel="noopener noreferrer">
							<?php echo esc_html( $speaking['experience_link_text'] ); ?>
						</a>
					</p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
