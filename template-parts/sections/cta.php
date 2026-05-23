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

$heading = anna_get_option( 'cta_heading', 'Ready to Begin Your Transformation?' );
$desc    = anna_get_option( 'cta_description', 'Take the first step toward the life you\'ve always envisioned. Book a complimentary discovery call today.' );
$cta_p   = anna_get_cta( 'primary' );
$cta_s   = anna_get_cta( 'secondary' );
?>

<section class="anna-cta anna-section anna-section--xl" id="final-cta" aria-labelledby="cta-heading">

	<!-- Animated BG -->
	<div class="anna-cta__bg" aria-hidden="true"></div>
	<div class="anna-cta__pattern" aria-hidden="true"></div>
	<div class="anna-cta__orb anna-cta__orb--1" aria-hidden="true"></div>
	<div class="anna-cta__orb anna-cta__orb--2" aria-hidden="true"></div>

	<div class="anna-container">
		<div class="anna-cta__inner anna-reveal">
			<span class="anna-eyebrow"><?php echo esc_html( anna_get_option( 'cta_eyebrow', __( 'Let\'s Connect', 'anna-baylis' ) ) ); ?></span>

			<h2 class="anna-cta__heading" id="cta-heading">
				<?php echo esc_html( $heading ); ?>
			</h2>

			<p class="anna-cta__description">
				<?php echo esc_html( $desc ); ?>
			</p>

			<div class="anna-cta__actions">
				<a href="<?php echo esc_url( $cta_p['url'] ); ?>" class="anna-btn anna-btn--primary anna-btn--xl">
					<?php echo esc_html( $cta_p['text'] ); ?>
				</a>
				<a href="<?php echo esc_url( $cta_s['url'] ); ?>" class="anna-btn anna-btn--ghost anna-btn--xl">
					<?php echo esc_html( $cta_s['text'] ); ?>
				</a>
			</div>

			<p class="anna-cta__trust">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
				<?php echo esc_html( anna_get_option( 'cta_trust', __( 'Free discovery call · No obligations · 100% confidential', 'anna-baylis' ) ) ); ?>
			</p>
		</div>
	</div>

</section>
