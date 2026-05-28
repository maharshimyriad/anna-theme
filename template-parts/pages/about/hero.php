<?php
/**
 * About page hero section.
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

<section
	class="anna-about-page-hero"
	<?php if ( ! empty( $about['hero_image_id'] ) ) : ?>
		style="background-image:url('<?php echo esc_url( anna_responsive_image_url( absint( $about['hero_image_id'] ), 'full' ) ); ?>');"
	<?php endif; ?>
>
	<div class="anna-about-page-hero__overlay"></div>
	<div class="anna-container">
		<div class="anna-about-page-hero__content">
			<?php if ( ! empty( $about['hero_eyebrow'] ) ) : ?>
				<span class="anna-about-page__eyebrow anna-about-page__eyebrow--light"><?php echo esc_html( $about['hero_eyebrow'] ); ?></span>
			<?php endif; ?>
			<h1 class="anna-about-page-hero__heading"><?php echo wp_kses_post( nl2br( (string) $about['hero_heading'] ) ); ?></h1>
			<?php if ( ! empty( $about['hero_subheading'] ) ) : ?>
				<p class="anna-about-page-hero__subheading"><?php echo esc_html( $about['hero_subheading'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $about['hero_description'] ) ) : ?>
				<p class="anna-about-page-hero__description"><?php echo esc_html( $about['hero_description'] ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
