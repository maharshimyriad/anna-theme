<?php
/**
 * About page coach section.
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

<section class="anna-about-page-section anna-about-page-coach">
	<div class="anna-container">
		<div class="anna-about-page-coach__grid">
			<div class="anna-about-page-coach__content anna-reveal--left">
				<?php if ( ! empty( $about['coach_eyebrow'] ) ) : ?>
					<p class="anna-about-page-coach__eyebrow"><?php echo esc_html( $about['coach_eyebrow'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $about['coach_title'] ) ) : ?>
					<h2 class="anna-about-page-coach__heading"><?php echo esc_html( $about['coach_title'] ); ?></h2>
				<?php endif; ?>

				<?php if ( ! empty( $about['coach_body'] ) ) : ?>
					<div class="anna-about-page-coach__copy"><?php echo wp_kses_post( wpautop( (string) $about['coach_body'] ) ); ?></div>
				<?php endif; ?>

				<?php if ( ! empty( $about['coach_button_text'] ) && ! empty( $about['coach_button_url'] ) ) : ?>
					<div class="anna-about-page-coach__cta">
						<a class="anna-btn anna-btn--primary" href="<?php echo esc_url( $about['coach_button_url'] ); ?>">
							<?php echo esc_html( $about['coach_button_text'] ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $about['coach_image_id'] ) ) : ?>
				<figure class="anna-about-page-coach__media anna-reveal--right">
					<?php anna_responsive_image( absint( $about['coach_image_id'] ), 'large', 'anna-img-cover' ); ?>
				</figure>
			<?php endif; ?>
		</div>
	</div>
</section>
