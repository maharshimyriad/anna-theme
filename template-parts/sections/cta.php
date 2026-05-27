<?php
/**
 * Template part: Final CTA Section
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$section_data = anna_get_final_cta_section_content();
$heading      = $section_data['heading'];
$desc         = $section_data['description'];
$cta_p        = $section_data['primary_cta'];
$cta_s        = $section_data['secondary_cta'];
?>

<section class="anna-cta anna-section anna-section--lg" id="final-cta" aria-labelledby="cta-heading">
	<div class="anna-container">
		<div class="anna-cta__inner anna-reveal">
			<?php if ( $section_data['eyebrow'] ) : ?>
				<span class="anna-eyebrow"><?php echo esc_html( $section_data['eyebrow'] ); ?></span>
			<?php endif; ?>

			<h2 class="anna-cta__heading" id="cta-heading"><?php echo wp_kses_post( $heading ); ?></h2>

			<?php if ( $desc ) : ?>
				<p class="anna-cta__description"><?php echo esc_html( $desc ); ?></p>
			<?php endif; ?>

			<?php if ( $section_data['trust_text'] ) : ?>
				<p class="anna-cta__trust"><?php echo esc_html( $section_data['trust_text'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $cta_p['text'] ) || ! empty( $cta_s['text'] ) ) : ?>
				<div class="anna-cta__actions">
					<?php if ( ! empty( $cta_p['text'] ) ) : ?>
						<a href="<?php echo esc_url( $cta_p['url'] ); ?>" class="anna-btn anna-btn--primary anna-btn--lg"><?php echo esc_html( $cta_p['text'] ); ?></a>
					<?php endif; ?>
					<?php if ( ! empty( $cta_s['text'] ) ) : ?>
						<a href="<?php echo esc_url( $cta_s['url'] ); ?>" class="anna-btn anna-btn--ghost anna-btn--lg"><?php echo esc_html( $cta_s['text'] ); ?></a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
