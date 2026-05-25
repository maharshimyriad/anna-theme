<?php
/**
 * Template part: Hero Section
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading     = anna_get_option( 'hero_heading', 'Transform Your Life with <em>Purposeful</em> Coaching' );
$description = anna_get_option( 'hero_description', 'Discover clarity, confidence, and lasting transformation through personalised coaching designed to unlock your full potential.' );
$cta_primary = anna_get_cta( 'primary' );
$cta_second  = anna_get_cta( 'secondary' );
$hero_image  = anna_get_option( 'hero_image_id', '' );
$stats       = anna_get_stats();
?>

<section 
	class="anna-hero anna-section"
	id="hero"
	aria-labelledby="hero-heading"
	style="background-image: url('<?php echo esc_url( anna_responsive_image_url( $hero_image, 'full' ) ); ?>');"
>


	<div class="anna-container anna-container--wide">
		<div class="anna-hero__inner">

			<!-- Content -->
			<div class="anna-hero__content anna-reveal">
				<span class="anna-eyebrow anna-hero__overline">
					<?php echo esc_html( anna_get_option( 'hero_eyebrow', __( 'Coaching & Wellness', 'anna-baylis' ) ) ); ?>
					<?php echo esc_html( anna_get_option( 'hero_eyebrow_test', __( 'Coaching & Wellness', 'anna-baylis' ) ) ); ?>
				</span>

				<h1 class="anna-hero__heading" id="hero-heading">
					<?php echo wp_kses_post( $heading ); ?>
				</h1>

				<p class="anna-hero__description">
					<?php echo esc_html( $description ); ?>
				</p>

				<div class="anna-hero__ctas">
					<a href="<?php echo esc_url( $cta_primary['url'] ); ?>" class="anna-btn anna-btn--primary anna-btn--lg">
						<?php echo esc_html( $cta_primary['text'] ); ?>
					</a>
					<a href="<?php echo esc_url( $cta_second['url'] ); ?>" class="anna-btn anna-btn--secondary anna-btn--lg">
						<?php echo esc_html( $cta_second['text'] ); ?>
					</a>
				</div>

				<div class="anna-hero__trust">
					<svg class="anna-hero__trust-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
					<span><?php echo esc_html( anna_get_option( 'hero_trust_text', __( 'Trusted by 500+ clients worldwide', 'anna-baylis' ) ) ); ?></span>
				</div>
			</div>

			<!-- Visual -->


		</div>
	</div>

</section>
