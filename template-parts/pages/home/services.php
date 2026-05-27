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

$static_services = array(
	array(
		'number'  => '01',
		'title'   => '1-1 Life Coaching',
		'excerpt' => 'Deep, personalised work using a bottom-up approach that accesses the subconscious through the body. For men, women and couples. In person in Melbourne or online worldwide.',
		'link'    => 'Find out more',
	),
	array(
		'number'  => '02',
		'title'   => 'Oasis Community',
		'excerpt' => 'A women\'s community for sustainable health and wellbeing. Ongoing live guidance, daily practices, and a space to come back to built on the principles that create lasting change.',
		'link'    => 'Find out more',
	),
	array(
		'number'  => '03',
		'title'   => 'Speaking and Workshops',
		'excerpt' => 'Keynotes and interactive sessions for conferences, corporate events and women\'s gatherings. Drawing on Olympic experience, deep coaching expertise and lived transformation.',
		'link'    => 'Enquire about speaking',
	),
);
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
			<?php foreach ( $static_services as $service ) : ?>
				<li class="anna-reveal--scale">
					<article class="anna-service-card anna-service-card--feature">
						<span class="anna-service-card__number"><?php echo esc_html( $service['number'] ); ?></span>
						<h3 class="anna-service-card__title"><?php echo esc_html( $service['title'] ); ?></h3>
						<p class="anna-service-card__excerpt"><?php echo esc_html( $service['excerpt'] ); ?></p>
						<a href="#" class="anna-service-card__link"><?php echo esc_html( $service['link'] ); ?></a>
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
