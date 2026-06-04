<?php
/**
 * Speaking "What I bring to the stage" section.
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
?>

<section class="anna-speaking-page-section anna-speaking-page-bring">
	<div class="anna-container anna-container--max">
		<div class="anna-speaking-page-bring__grid">
			<div class="anna-speaking-page-bring__content anna-reveal--left">
				<?php if ( ! empty( $speaking['bring_eyebrow'] ) ) : ?>
					<p class="anna-speaking-page-bring__eyebrow"><?php echo esc_html( $speaking['bring_eyebrow'] ); ?></p>
				<?php endif; ?>

				<h2 class="anna-speaking-page-bring__heading">
					<?php if ( ! empty( $speaking['bring_heading_line1'] ) ) : ?>
						<span class="anna-speaking-page-bring__heading-line anna-speaking-page-bring__heading-line--primary"><?php echo esc_html( $speaking['bring_heading_line1'] ); ?></span>
					<?php endif; ?>
					<?php if ( ! empty( $speaking['bring_heading_line2'] ) ) : ?>
						<span class="anna-speaking-page-bring__heading-line anna-speaking-page-bring__heading-line--accent"><?php echo esc_html( $speaking['bring_heading_line2'] ); ?></span>
					<?php endif; ?>
				</h2>

				<?php if ( ! empty( $speaking['bring_body'] ) ) : ?>
					<div class="anna-speaking-page-bring__copy"><?php echo wp_kses_post( wpautop( (string) $speaking['bring_body'] ) ); ?></div>
				<?php endif; ?>

				<?php if ( ! empty( $speaking['bring_quote'] ) ) : ?>
					<blockquote class="anna-speaking-page-bring__quote">
						<p><?php echo esc_html( $speaking['bring_quote'] ); ?></p>
					</blockquote>
				<?php endif; ?>

				<?php if ( ! empty( $speaking['bring_button_text'] ) && ! empty( $speaking['bring_button_url'] ) ) : ?>
					<a class="anna-speaking-page-bring__btn" href="<?php echo esc_url( $speaking['bring_button_url'] ); ?>">
						<?php echo esc_html( $speaking['bring_button_text'] ); ?>
					</a>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $speaking['bring_image_id'] ) ) : ?>
				<figure class="anna-speaking-page-bring__figure anna-reveal--right">
					<?php echo wp_get_attachment_image( absint( $speaking['bring_image_id'] ), 'large', false, array( 'class' => 'anna-speaking-page-bring__image' ) ); ?>
				</figure>
			<?php endif; ?>
		</div>
	</div>
</section>
