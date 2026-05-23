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

$services = anna_get_homepage_services( 6 );
$heading  = anna_get_option( 'services_heading', 'How I Can Help You' );
$desc     = anna_get_option( 'services_description', 'Tailored programs designed to help you thrive in every dimension of your life.' );

// Fallback static services when CPT is empty.
$static_services = array(
	array(
		'icon'    => 'coaching',
		'title'   => '1:1 Coaching',
		'excerpt' => 'Deep, personalised coaching sessions designed around your unique goals, challenges, and aspirations.',
	),
	array(
		'icon'    => 'wellness',
		'title'   => 'Wellness Programs',
		'excerpt' => 'Holistic programmes integrating mind, body, and spirit for sustainable wellbeing and peak performance.',
	),
	array(
		'icon'    => 'mindset',
		'title'   => 'Mindset Mastery',
		'excerpt' => 'Transform limiting beliefs into empowering narratives that fuel confidence and decisive action.',
	),
	array(
		'icon'    => 'growth',
		'title'   => 'Career Growth',
		'excerpt' => 'Strategic career coaching that aligns professional success with personal fulfilment and purpose.',
	),
	array(
		'icon'    => 'workshop',
		'title'   => 'Group Workshops',
		'excerpt' => 'Immersive group experiences that build community, accountability, and collective transformation.',
	),
	array(
		'icon'    => 'community',
		'title'   => 'Community Circle',
		'excerpt' => 'An ongoing support community for sustained growth, connection, and shared learning.',
	),
);
?>

<section class="anna-services anna-section anna-section--lg" id="services" aria-labelledby="services-heading">
	<div class="anna-container">

		<header class="anna-services__header anna-reveal">
			<span class="anna-eyebrow"><?php echo esc_html( anna_get_option( 'services_eyebrow', __( 'Services', 'anna-baylis' ) ) ); ?></span>
			<h2 class="anna-services__heading" id="services-heading">
				<?php echo esc_html( $heading ); ?>
			</h2>
			<p class="anna-services__description">
				<?php echo esc_html( $desc ); ?>
			</p>
		</header>

		<ul class="anna-services__grid anna-stagger" role="list">

			<?php if ( $services->have_posts() ) : ?>
				<?php
				while ( $services->have_posts() ) :
					$services->the_post();
					$icon = get_post_meta( get_the_ID(), '_anna_service_icon', true );
					?>
					<li>
						<article class="anna-card anna-card--service anna-service-card">
							<div class="anna-service-card__icon-wrap" aria-hidden="true">
								<?php anna_service_icon( $icon ? $icon : 'default' ); ?>
							</div>
							<h3 class="anna-service-card__title"><?php the_title(); ?></h3>
							<p class="anna-service-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
							<a href="<?php the_permalink(); ?>" class="anna-service-card__link">
								<?php esc_html_e( 'Learn more', 'anna-baylis' ); ?>
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
							</a>
						</article>
					</li>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>

			<?php else : ?>
				<?php foreach ( $static_services as $service ) : ?>
					<li>
						<article class="anna-card anna-card--service anna-service-card">
							<div class="anna-service-card__icon-wrap" aria-hidden="true">
								<?php anna_service_icon( $service['icon'] ); ?>
							</div>
							<h3 class="anna-service-card__title"><?php echo esc_html( $service['title'] ); ?></h3>
							<p class="anna-service-card__excerpt"><?php echo esc_html( $service['excerpt'] ); ?></p>
							<a href="#" class="anna-service-card__link">
								<?php esc_html_e( 'Learn more', 'anna-baylis' ); ?>
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
							</a>
						</article>
					</li>
				<?php endforeach; ?>
			<?php endif; ?>

		</ul>

		<footer class="anna-services__footer anna-reveal">
			<?php
			$services_cta = anna_get_option( 'services_cta_text', __( 'View All Services', 'anna-baylis' ) );
			$services_url = anna_get_option( 'services_cta_url', '#' );
			?>
			<a href="<?php echo esc_url( $services_url ); ?>" class="anna-btn anna-btn--secondary anna-btn--lg">
				<?php echo esc_html( $services_cta ); ?>
			</a>
		</footer>

	</div>
</section>
