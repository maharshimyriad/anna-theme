<?php
/**
 * About page life-now section.
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

<section class="anna-about-page-section anna-about-page-life">
	<div class="anna-container">
		<div class="anna-about-page-life__grid">
			<?php if ( ! empty( $about['life_image_id'] ) ) : ?>
				<figure class="anna-about-page-life__media">
					<?php anna_responsive_image( absint( $about['life_image_id'] ), 'full', 'anna-img-cover' ); ?>
				</figure>
			<?php endif; ?>
			<div class="anna-about-page-life__content">
				<?php if ( ! empty( $about['life_eyebrow'] ) ) : ?>
					<span class="anna-about-page__eyebrow"><?php echo esc_html( $about['life_eyebrow'] ); ?></span>
				<?php endif; ?>
				<h2 class="anna-about-page__heading"><?php echo esc_html( $about['life_heading'] ?? '' ); ?></h2>
				<div class="anna-about-page__copy"><?php echo wp_kses_post( wpautop( (string) ( $about['life_body'] ?? '' ) ) ); ?></div>
			</div>
		</div>
	</div>
</section>
