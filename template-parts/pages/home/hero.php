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

$hero_content = anna_get_homepage_hero_content();
$heading      = $hero_content['heading'];
$description  = $hero_content['description'];
$cta_primary  = $hero_content['primary_cta'];
$cta_second   = $hero_content['secondary_cta'];
$hero_image   = $hero_content['image_id'];
$stats        = $hero_content['stats'];
$hero_eyebrow = $hero_content['eyebrow'];
$hero_trust   = $hero_content['trust_text'];

if ( empty( $cta_second['url'] ) || '#' === $cta_second['url'] ) {
	$cta_second['url'] = '#intro';
}
?>

<div class="anna-hero-wrapper">

	<section
		class="anna-hero-section anna-section"
		id="hero"
		aria-labelledby="hero-heading"
		<?php if ( $hero_image ) : ?>
			style="background-image:url('<?php echo esc_url( anna_responsive_image_url( $hero_image, 'full' ) ); ?>');"
		<?php endif; ?>
	>
		<div class="anna-container anna-container--max">
			<div class="anna-hero__inner">
				<div class="anna-hero__content anna-reveal">

					<?php if ( $hero_eyebrow ) : ?>
						<span class="anna-hero__overline">
							<?php echo esc_html( $hero_eyebrow ); ?>
						</span>
					<?php endif; ?>

					<h1 class="anna-hero__heading" id="hero-heading"><?php echo wp_kses_post( $heading ); ?></h1>

					<?php if ( $description ) : ?>
						<!-- Description: visible on desktop only (inside hero) -->
						<p class="anna-hero__description anna-hero__description--overlay"><?php echo esc_html( $description ); ?></p>
					<?php endif; ?>

					<div class="anna-hero__ctas">
						<a href="<?php echo esc_url( $cta_primary['url'] ); ?>" class="anna-btn anna-btn--hero-primary anna-btn--lg">
							<?php echo esc_html( $cta_primary['text'] ); ?>
						</a>
						<?php if ( ! empty( $cta_second['text'] ) ) : ?>
							<a href="<?php echo esc_url( $cta_second['url'] ); ?>" class="anna-btn anna-btn--hero-link anna-btn--lg anna-hero__cta-secondary">
								<?php echo esc_html( $cta_second['text'] ); ?>
							</a>
						<?php endif; ?>
					</div>

					<?php if ( ! empty( $stats ) ) : ?>
						<!-- Stats: visible on desktop only (inside hero image) -->
						<ul class="anna-hero__stats anna-hero__stats--overlay" role="list" aria-label="<?php esc_attr_e( 'Key achievements', 'anna-baylis' ); ?>">
							<?php foreach ( $stats as $index => $stat ) : ?>
								<li class="anna-hero__stat anna-hero__stat--<?php echo esc_attr( $index + 1 ); ?>">
									<span class="anna-hero__stat-value"><?php echo esc_html( $stat['value'] ); ?></span>
									<span class="anna-hero__stat-label"><?php echo esc_html( $stat['label'] ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( $hero_trust ) : ?>
						<p class="anna-hero__trust"><?php echo esc_html( $hero_trust ); ?></p>
					<?php endif; ?>

				</div>
			</div>
		</div>
	</section>

	<?php if ( ! empty( $stats ) ) : ?>
		<!-- Stats strip + description: visible on mobile only (below hero image) -->
		<div class="anna-hero__stats-strip" aria-label="<?php esc_attr_e( 'Key achievements', 'anna-baylis' ); ?>">
			<div class="anna-container">
				<ul class="anna-hero__stats-strip__list" role="list">
					<?php foreach ( $stats as $index => $stat ) : ?>
						<li class="anna-hero__stats-strip__item">
							<span class="anna-hero__stats-strip__value"><?php echo esc_html( $stat['value'] ); ?></span>
							<span class="anna-hero__stats-strip__label"><?php echo esc_html( $stat['label'] ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>

				<?php if ( $description ) : ?>
					<p class="anna-hero__stats-strip__description"><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

</div><!-- /.anna-hero-wrapper -->
