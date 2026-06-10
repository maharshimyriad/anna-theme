<?php
/**
 * Template part: Services Section
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$section_data  = anna_get_services_section_content();
$heading       = $section_data['heading'];
$desc          = $section_data['description'];
$services      = $section_data['cards'];
?>

<section class="anna-services anna-section anna-section--lg" id="services" aria-labelledby="services-heading">
	<div class="anna-container">
		<header class="anna-services__header anna-reveal">
			<?php if ( $section_data['eyebrow'] ) : ?>
				<span class="anna-eyebrow"><?php echo esc_html( $section_data['eyebrow'] ); ?></span>
			<?php endif; ?>
			<h2 class="anna-services__heading" id="services-heading"><?php echo esc_html( $heading ); ?></h2>
			<?php if ( $desc ) : ?>
				<p class="anna-services__description"><?php echo esc_html( $desc ); ?></p>
			<?php endif; ?>
		</header>

		<ul class="anna-services__grid" role="list">
			<?php foreach ( $services as $service ) : ?>
				<li class="anna-reveal--scale">
					<article class="anna-service-card anna-service-card--feature">
						<span class="anna-service-card__number"><?php echo esc_html( $service['number'] ); ?></span>
						<h3 class="anna-service-card__title"><?php echo esc_html( $service['title'] ); ?></h3>
						<p class="anna-service-card__excerpt"><?php echo esc_html( $service['excerpt'] ); ?></p>
						<?php if ( ! empty( $service['link'] ) ) : ?>
							<a href="<?php echo esc_url( $service['url'] ?: '#' ); ?>" class="anna-service-card__link"><?php echo esc_html( $service['link'] ); ?></a>
						<?php endif; ?>
					</article>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ( $section_data['cta_text'] ) : ?>
			<footer class="anna-services__footer anna-reveal">
				<a href="<?php echo esc_url( $section_data['cta_url'] ); ?>" class="anna-btn anna-btn--secondary anna-btn--lg">
					<?php echo esc_html( $section_data['cta_text'] ); ?>
				</a>
			</footer>
		<?php endif; ?>
	</div>
</section>
