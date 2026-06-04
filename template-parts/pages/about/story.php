<?php
/**
 * About page story section.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$about = get_query_var( 'anna_about_page_content', array() );
if ( empty( $about ) ) {
	$about = anna_get_about_page_content();
}
?>

<section class="anna-about-page-section anna-about-page-story">
	<div class="anna-container">
		<div class="anna-about-page-story__grid">

			<div class="anna-about-page-story__content anna-reveal--left">
				<?php if ( ! empty( $about['story_eyebrow'] ) ) : ?>
					<span class="anna-about-page__eyebrow"><?php echo esc_html( $about['story_eyebrow'] ); ?></span>
				<?php endif; ?>
				<h2 class="anna-about-page__heading"><?php echo wp_kses_post( nl2br( (string) $about['story_heading'] ) ); ?></h2>
				<div class="anna-about-page__copy"><?php echo wp_kses_post( wpautop( (string) $about['story_body'] ) ); ?></div>
				<div class="anna-about-page-rock">
				<h2 class="anna-about-page__heading"><?php echo esc_html( $about['rock_heading'] ?? '' ); ?></h2>
					
				<div class=""> 
					<div class="anna-about-page__copy">
						<?php echo wp_kses_post( wpautop( (string) ( $about['rock_left_body'] ?? '' ) ) ); ?>
					</div> 
					</div>

				</div>
			</div>
						<?php if ( ! empty( $about['story_image_id'] ) ) : ?>
				<figure class="anna-about-page-story__media anna-reveal--right">
					<?php anna_responsive_image( absint( $about['story_image_id'] ), 'large', 'anna-img-cover' ); ?>
				</figure>
			<?php endif; ?>
		</div>
	</div>
</section>
