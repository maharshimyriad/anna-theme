<?php
/**
 * Speaking recent experience section.
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

$has_testimonial = ! empty( $speaking['experience_testimonial_quote'] );
?>

<section class="anna-speaking-page-section anna-speaking-page-experience">
	<div class="anna-container anna-container--max">
		<div class="anna-speaking-page-experience__grid">
			<?php if ( $has_testimonial ) : ?>
				<blockquote class="anna-speaking-page-experience__testimonial anna-reveal--left">
					<div class="anna-speaking-page-experience__stars" aria-hidden="true">
						<?php for ( $i = 0; $i < 5; $i++ ) : ?>
							<span>★</span>
						<?php endfor; ?>
					</div>
					<p class="anna-speaking-page-experience__quote"><?php echo esc_html( $speaking['experience_testimonial_quote'] ); ?></p>
					<?php if ( ! empty( $speaking['experience_testimonial_name'] ) ) : ?>
						<cite class="anna-speaking-page-experience__name"><?php echo esc_html( $speaking['experience_testimonial_name'] ); ?></cite>
					<?php endif; ?>
					<?php if ( ! empty( $speaking['experience_testimonial_role'] ) ) : ?>
						<span class="anna-speaking-page-experience__role"><?php echo esc_html( $speaking['experience_testimonial_role'] ); ?></span>
					<?php endif; ?>
				</blockquote>
			<?php endif; ?>

			<div class="anna-speaking-page-experience__content anna-reveal--right">
				<?php if ( ! empty( $speaking['experience_eyebrow'] ) ) : ?>
					<p class="anna-speaking-page-experience__eyebrow"><?php echo esc_html( $speaking['experience_eyebrow'] ); ?></p>
				<?php endif; ?>

				<h2 class="anna-speaking-page-experience__heading">
					<?php if ( ! empty( $speaking['experience_heading_primary'] ) ) : ?>
						<span class="anna-speaking-page-experience__heading-primary"><?php echo esc_html( $speaking['experience_heading_primary'] ); ?></span>
					<?php endif; ?>
					<?php if ( ! empty( $speaking['experience_heading_secondary'] ) ) : ?>
						<span class="anna-speaking-page-experience__heading-secondary"><?php echo esc_html( $speaking['experience_heading_secondary'] ); ?></span>
					<?php endif; ?>
				</h2>

				<?php if ( ! empty( $speaking['experience_body'] ) ) : ?>
					<div class="anna-speaking-page-experience__copy"><?php echo wp_kses_post( wpautop( (string) $speaking['experience_body'] ) ); ?></div>
				<?php endif; ?>

				<?php if ( ! empty( $speaking['experience_link_url'] ) ) : ?>
					<p class="anna-speaking-page-experience__link-wrap">
						<?php if ( ! empty( $speaking['experience_link_prefix'] ) ) : ?>
							<?php echo esc_html( $speaking['experience_link_prefix'] ); ?>
						<?php endif; ?>
						<a href="<?php echo esc_url( $speaking['experience_link_url'] ); ?>" target="_blank" rel="noopener noreferrer">
							<strong><?php echo esc_html( $speaking['experience_link_label'] ?: $speaking['experience_link_url'] ); ?></strong>
						</a>
					</p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
