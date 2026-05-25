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

$heading     = anna_get_option( 'hero_heading', 'You know what to do. <br>You\'re just not doing it.' );
$description = anna_get_option( 'hero_description', '' );
$cta_primary = anna_get_cta( 'primary' );
$cta_second  = anna_get_cta( 'secondary' );
$hero_image  = anna_get_option( 'hero_image_id', '' );
$stats       = anna_get_stats();

if ( empty( $cta_second['text'] ) ) {
	$cta_second['text'] = __( 'Learn about my approach', 'anna-baylis' );
}

if ( empty( $cta_second['url'] ) || '#' === $cta_second['url'] ) {
	$cta_second['url'] = '#intro';
}
?>

<section
	class="anna-hero anna-section"
	id="hero"
	aria-labelledby="hero-heading"
	<?php if ( $hero_image ) : ?>
		style="background-image:url('<?php echo esc_url( anna_responsive_image_url( $hero_image, 'full' ) ); ?>');"
	<?php endif; ?>
>
	<div class="anna-container anna-container--wide">
		<div class="anna-hero__inner">
			<div class="anna-hero__content anna-reveal">
				<?php if ( anna_get_option( 'hero_eyebrow', '' ) ) : ?>
					<span class="anna-hero__overline">
						<?php echo esc_html( anna_get_option( 'hero_eyebrow', '' ) ); ?>
					</span>
				<?php endif; ?>

				<h1 class="anna-hero__heading" id="hero-heading"><?php echo wp_kses_post( $heading ); ?></h1>

				<?php if ( $description ) : ?>
					<p class="anna-hero__description"><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>

				<div class="anna-hero__ctas">
					<a href="<?php echo esc_url( $cta_primary['url'] ); ?>" class="anna-btn anna-btn--hero-primary anna-btn--lg">
						<?php echo esc_html( $cta_primary['text'] ); ?>
					</a>
					<a href="<?php echo esc_url( $cta_second['url'] ); ?>" class="anna-btn anna-btn--hero-link anna-btn--lg">
						<?php echo esc_html( $cta_second['text'] ); ?>
						<span class="anna-btn__icon" aria-hidden="true">&rarr;</span>
					</a>
				</div>

				<?php if ( ! empty( $stats ) ) : ?>
					<ul class="anna-hero__stats" role="list">
						<?php foreach ( $stats as $index => $stat ) : ?>
							<li class="anna-hero__stat anna-hero__stat--<?php echo esc_attr( $index + 1 ); ?>">
								<span class="anna-hero__stat-value"><?php echo esc_html( $stat['value'] ); ?></span>
								<span class="anna-hero__stat-label"><?php echo esc_html( $stat['label'] ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( anna_get_option( 'hero_trust_text', '' ) ) : ?>
					<p class="anna-hero__trust"><?php echo esc_html( anna_get_option( 'hero_trust_text', '' ) ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
