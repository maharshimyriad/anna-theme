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

<section class="anna-hero anna-section" id="hero" aria-labelledby="hero-heading">

	<!-- Decorative background orbs -->
	<div class="anna-hero__bg" aria-hidden="true">
		<div class="anna-hero__orb anna-hero__orb--1"></div>
		<div class="anna-hero__orb anna-hero__orb--2"></div>
		<div class="anna-hero__orb anna-hero__orb--3"></div>
	</div>

	<div class="anna-container anna-container--wide">
		<div class="anna-hero__inner">

			<!-- Content -->
			<div class="anna-hero__content anna-reveal">
				<span class="anna-eyebrow anna-hero__overline">
					<?php echo esc_html( anna_get_option( 'hero_eyebrow', __( 'Coaching & Wellness', 'anna-baylis' ) ) ); ?>
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
			<div class="anna-hero__visual anna-reveal--right">
				<figure class="anna-image-composition">
					<div class="anna-image-composition__primary">
						<?php
						if ( $hero_image ) {
							anna_responsive_image( $hero_image, 'anna-hero', 'anna-img-cover', false );
						} else {
							// Placeholder with background color
							echo '<div style="width:100%;height:100%;background:linear-gradient(135deg, var(--color-bg-muted), var(--color-bg-soft));display:flex;align-items:center;justify-content:center;"><span style="font-size:var(--text-xl);color:var(--color-text-light);">Hero Image</span></div>';
						}
						?>
					</div>
					<div class="anna-image-composition__shape" aria-hidden="true"></div>
				</figure>

				<?php if ( ! empty( $stats ) ) : ?>
					<dl class="anna-hero__stats" aria-label="<?php esc_attr_e( 'Key statistics', 'anna-baylis' ); ?>">
						<?php foreach ( $stats as $index => $stat ) : ?>
							<div class="anna-hero__stat anna-hero__stat--<?php echo esc_attr( $index + 1 ); ?> anna-reveal--scale" data-stat-value="<?php echo esc_attr( $stat['value'] ); ?>">
								<dt class="anna-hero__stat-label"><?php echo esc_html( $stat['label'] ); ?></dt>
								<dd class="anna-hero__stat-value"><?php echo esc_html( $stat['value'] ); ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>
			</div>

		</div>
	</div>

	<!-- Scroll indicator -->
	<button class="anna-hero__scroll" aria-label="<?php esc_attr_e( 'Scroll down', 'anna-baylis' ); ?>" onclick="document.getElementById('intro')?.scrollIntoView({behavior:'smooth'})">
		<span><?php esc_html_e( 'Scroll', 'anna-baylis' ); ?></span>
		<span class="anna-hero__scroll-line" aria-hidden="true"></span>
	</button>

</section>
