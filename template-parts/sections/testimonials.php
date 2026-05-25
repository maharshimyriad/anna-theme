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

$testimonials = anna_get_homepage_testimonials( 3 );
$section_data = anna_get_testimonials_section_content();
$heading      = $section_data['heading'];
$heading_main = $heading;
$heading_sub  = '';

if ( false !== stripos( $heading, ' Google reviews' ) ) {
	$heading_main = trim( str_ireplace( 'Google reviews', '', $heading ) );
	$heading_sub  = 'Google reviews';
}

$static       = array(
	array(
		'quote'  => 'Working with Anna has been profoundly healing. She has helped me build my own inner support system by reconnecting with my younger self and learning how to meet myself with love, compassion and understanding.',
		'name'   => 'Rebecca Browne',
		'role'   => '3 months ago',
		'rating' => 5,
	),
	array(
		'quote'  => 'Anna has an extraordinary ability to help you distil your core values and actually live them, while uncovering parts of yourself you did not even know existed. She is a guide and a healer.',
		'name'   => 'Mel',
		'role'   => '6 months ago',
		'rating' => 5,
	),
	array(
		'quote'  => 'Anna has a remarkable ability to create a safe, supportive space where I felt comfortable exploring the different parts that make up who I am. Every session was both grounding and empowering.',
		'name'   => 'Deane Voladimos',
		'role'   => '29 days ago',
		'rating' => 5,
	),
	);
?>

<section class="anna-testimonials-section anna-section anna-section--lg" id="testimonials" aria-labelledby="testimonials-heading">
	<div class="anna-container anna-container--wide">
		<div class="anna-testimonials-section__header anna-reveal">
			<div>
				<?php if ( $section_data['eyebrow'] ) : ?>
					<span class="anna-eyebrow"><?php echo esc_html( $section_data['eyebrow'] ); ?></span>
				<?php endif; ?>
				<h2 class="anna-testimonials-section__heading" id="testimonials-heading">
					<span class="anna-testimonials-section__heading-main"><?php echo esc_html( $heading_main ); ?></span>
					<?php if ( $heading_sub ) : ?>
						<span class="anna-testimonials-section__heading-sub"><?php echo esc_html( $heading_sub ); ?></span>
					<?php endif; ?>
				</h2>
				<?php if ( $section_data['summary'] ) : ?>
					<p class="anna-testimonials-section__summary"><?php echo esc_html( $section_data['summary'] ); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<div class="anna-testimonials-inner anna-testimonials-inner--static" role="list">
			<?php if ( $testimonials->have_posts() ) : ?>
				<?php
				while ( $testimonials->have_posts() ) :
					$testimonials->the_post();
					$rating = absint( get_post_meta( get_the_ID(), '_anna_testimonial_rating', true ) ) ?: 5;
					$role   = get_post_meta( get_the_ID(), '_anna_testimonial_role', true );
					?>
					<figure class="anna-testimonial" role="listitem">
						<div class="anna-testimonial__rating"><?php echo anna_star_rating( $rating ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
						<blockquote class="anna-testimonial__quote"><p><?php echo esc_html( get_the_content() ); ?></p></blockquote>
						<figcaption class="anna-testimonial__author">
							<cite class="anna-testimonial__name"><?php the_title(); ?></cite>
							<?php if ( $role ) : ?><span class="anna-testimonial__role"><?php echo esc_html( $role ); ?></span><?php endif; ?>
						</figcaption>
					</figure>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<?php foreach ( $static as $testimonial ) : ?>
					<figure class="anna-testimonial" role="listitem">
						<div class="anna-testimonial__rating"><?php echo anna_star_rating( $testimonial['rating'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
						<blockquote class="anna-testimonial__quote"><p><?php echo esc_html( $testimonial['quote'] ); ?></p></blockquote>
						<figcaption class="anna-testimonial__author">
							<cite class="anna-testimonial__name"><?php echo esc_html( $testimonial['name'] ); ?></cite>
							<span class="anna-testimonial__role"><?php echo esc_html( $testimonial['role'] ); ?></span>
						</figcaption>
					</figure>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>

		<?php if ( $section_data['cta_text'] ) : ?>
			<div class="anna-testimonials-section__footer anna-reveal">
				<a href="<?php echo esc_url( $section_data['cta_url'] ); ?>" class="anna-btn anna-btn--ghost anna-btn--lg anna-testimonials-section__link">
					<?php echo esc_html( $section_data['cta_text'] ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>
