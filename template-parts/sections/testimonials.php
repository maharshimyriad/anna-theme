<?php
/**
 * Template part: Testimonials Section
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$testimonials = anna_get_homepage_testimonials( 8 );
$heading      = anna_get_option( 'testimonials_heading', 'What My Clients Say' );

// Static fallback testimonials
$static = array(
	array(
		'quote'  => 'Anna\'s coaching completely transformed my perspective on what\'s possible. I went from feeling stuck to launching a business I\'m truly passionate about.',
		'name'   => 'Sarah Mitchell',
		'role'   => 'Entrepreneur',
		'rating' => 5,
	),
	array(
		'quote'  => 'Working with Anna gave me the clarity and confidence I needed to make a major career transition. Her approach is warm yet incredibly effective.',
		'name'   => 'James Thompson',
		'role'   => 'Marketing Director',
		'rating' => 5,
	),
	array(
		'quote'  => 'The wellness programme was life-changing. I now have sustainable habits and a mindset that supports both my ambitions and my wellbeing.',
		'name'   => 'Emily Chen',
		'role'   => 'Senior Consultant',
		'rating' => 5,
	),
	array(
		'quote'  => 'Anna has a rare gift for asking exactly the right questions at exactly the right time. Each session left me with profound insights and actionable steps.',
		'name'   => 'David Okafor',
		'role'   => 'Tech Lead',
		'rating' => 5,
	),
	array(
		'quote'  => 'I was sceptical about coaching, but Anna\'s evidence-based approach won me over immediately. The results speak for themselves.',
		'name'   => 'Rachel Davies',
		'role'   => 'Finance Manager',
		'rating' => 5,
	),
	array(
		'quote'  => 'The group workshop was an incredibly powerful experience. The connections made and insights gained continue to benefit me months later.',
		'name'   => 'Michael Ross',
		'role'   => 'Creative Director',
		'rating' => 5,
	),
);
?>

<section class="anna-testimonials-section anna-section anna-section--lg" id="testimonials" aria-labelledby="testimonials-heading">
	<div class="anna-container anna-container--wide">

		<div class="anna-testimonials-section__header anna-reveal">
			<div>
				<span class="anna-eyebrow"><?php echo esc_html( anna_get_option( 'testimonials_eyebrow', __( 'Testimonials', 'anna-baylis' ) ) ); ?></span>
				<h2 class="anna-testimonials-section__heading" id="testimonials-heading">
					<?php echo esc_html( $heading ); ?>
				</h2>
			</div>

			<div class="anna-testimonials-section__controls">
				<button class="anna-carousel-btn anna-carousel-btn--prev" id="testimonial-prev" aria-label="<?php esc_attr_e( 'Previous testimonial', 'anna-baylis' ); ?>" aria-controls="testimonial-track">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
				</button>
				<button class="anna-carousel-btn anna-carousel-btn--next" id="testimonial-next" aria-label="<?php esc_attr_e( 'Next testimonial', 'anna-baylis' ); ?>" aria-controls="testimonial-track">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
				</button>
			</div>
		</div>

		<div class="anna-testimonials-track" aria-live="polite" aria-atomic="false">
			<div class="anna-testimonials-inner" id="testimonial-track" role="list">

				<?php if ( $testimonials->have_posts() ) : ?>
					<?php
					while ( $testimonials->have_posts() ) :
						$testimonials->the_post();
						$rating  = absint( get_post_meta( get_the_ID(), '_anna_testimonial_rating', true ) ) ?: 5;
						$role    = get_post_meta( get_the_ID(), '_anna_testimonial_role', true );
						$company = get_post_meta( get_the_ID(), '_anna_testimonial_company', true );
						$role_display = $role . ( $company ? ', ' . $company : '' );
						?>
						<figure class="anna-testimonial" role="listitem">
							<div class="anna-testimonial__rating">
								<?php echo anna_star_rating( $rating ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped internally ?>
							</div>
							<blockquote class="anna-testimonial__quote">
								<p><?php echo esc_html( get_the_content() ); ?></p>
							</blockquote>
							<figcaption class="anna-testimonial__author">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail( 'anna-avatar', array( 'class' => 'anna-testimonial__avatar', 'loading' => 'lazy' ) ); ?>
								<?php else : ?>
									<span class="anna-testimonial__avatar anna-testimonial__avatar--placeholder" aria-hidden="true">
										<?php echo esc_html( mb_substr( get_the_title(), 0, 1 ) ); ?>
									</span>
								<?php endif; ?>
								<div>
									<cite class="anna-testimonial__name"><?php the_title(); ?></cite>
									<?php if ( $role_display ) : ?>
										<span class="anna-testimonial__role"><?php echo esc_html( $role_display ); ?></span>
									<?php endif; ?>
								</div>
							</figcaption>
						</figure>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>

				<?php else : ?>
					<?php foreach ( $static as $testimonial ) : ?>
						<figure class="anna-testimonial" role="listitem">
							<div class="anna-testimonial__rating">
								<?php echo anna_star_rating( $testimonial['rating'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
							<blockquote class="anna-testimonial__quote">
								<p><?php echo esc_html( $testimonial['quote'] ); ?></p>
							</blockquote>
							<figcaption class="anna-testimonial__author">
								<span class="anna-testimonial__avatar anna-testimonial__avatar--placeholder" aria-hidden="true">
									<?php echo esc_html( mb_substr( $testimonial['name'], 0, 1 ) ); ?>
								</span>
								<div>
									<cite class="anna-testimonial__name"><?php echo esc_html( $testimonial['name'] ); ?></cite>
									<span class="anna-testimonial__role"><?php echo esc_html( $testimonial['role'] ); ?></span>
								</div>
							</figcaption>
						</figure>
					<?php endforeach; ?>
				<?php endif; ?>

			</div>
		</div>

		<!-- Pagination dots generated by JS -->
		<div class="anna-carousel-dots" id="testimonial-dots" role="tablist" aria-label="<?php esc_attr_e( 'Testimonial pagination', 'anna-baylis' ); ?>"></div>

	</div>
</section>
