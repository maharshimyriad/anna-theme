<?php
/**
 * The template for displaying the front page
 *
 * @package anna-theme
 */

get_header(); ?>

<main id="main-content" class="site-main" role="main">
	<section class="section-hero" aria-labelledby="hero-heading" data-gsap="fade-up">
		<div class="container hero-inner">
			<div class="hero-content">
				<p class="hero-subtitle"><?php esc_html_e( 'Real change. From the inside out.', 'anna-theme' ); ?></p>
				<h1 id="hero-heading" class="hero-title">
					<?php esc_html_e( 'You know what to do. You\'re just not doing it.', 'anna-theme' ); ?>
				</h1>
				<p class="hero-description">
					<?php esc_html_e( 'Many people understand why change would be beneficial and create the conditions for change that sound logical, but fail to take action. Through understanding what has been running underneath...', 'anna-theme' ); ?>
				</p>
				<div class="hero-actions">
					<a href="#discovery" class="btn btn--primary"><?php esc_html_e( 'Book a Discovery Call', 'anna-theme' ); ?></a>
					<a href="#approach" class="btn btn--outline"><?php esc_html_e( 'Learn about my approach', 'anna-theme' ); ?></a>
				</div>
			</div>
		</div>
	</section>
</main>

<?php get_footer(); ?>
