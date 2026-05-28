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
				<p class="anna-about-page-hero__overline"><?php echo esc_html( $about['hero_eyebrow'] ); ?></p>
			<?php endif; ?>
			<h1 class="anna-about-page-hero__heading"><?php echo wp_kses_post( nl2br( (string) $about['hero_heading'] ) ); ?></h1>
			<?php if ( ! empty( $about['hero_subheading'] ) ) : ?>
				<p class="anna-about-page-hero__subheading"><?php echo esc_html( $about['hero_subheading'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $about['hero_description'] ) ) : ?>
				<p class="anna-about-page-hero__description"><?php echo esc_html( $about['hero_description'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $about['hero_tags'] ) ) : ?>
				<ul class="anna-about-page-hero__tags" role="list">
					<?php foreach ( (array) $about['hero_tags'] as $tag ) : ?>
						<?php if ( '' === trim( (string) $tag ) ) : ?>
							<?php continue; ?>
						<?php endif; ?>
						<li class="anna-about-page-hero__tag"><?php echo esc_html( $tag ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</section>
