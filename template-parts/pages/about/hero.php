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
	class="anna-about-page-hero anna-hero-section"
>
	<?php if ( ! empty( $about['hero_image_id'] ) ) :
		$about_img_id  = absint( $about['hero_image_id'] );
		$about_img_url = anna_responsive_image_url( $about_img_id, 'full' );
		$about_srcset  = wp_get_attachment_image_srcset( $about_img_id, 'full' );
		$about_alt     = get_post_meta( $about_img_id, '_wp_attachment_image_alt', true );
	?>
		<img
			class="anna-hero__bg-image"
			src="<?php echo esc_url( $about_img_url ); ?>"
			<?php if ( $about_srcset ) : ?>srcset="<?php echo esc_attr( $about_srcset ); ?>" sizes="100vw"<?php endif; ?>
			alt="<?php echo esc_attr( $about_alt ); ?>"
			loading="eager"
			fetchpriority="high"
			decoding="async"
		>
	<?php endif; ?>
	<div class="anna-container">
		<div class="anna-about-page-hero__content anna-reveal">
			<?php if ( ! empty( $about['hero_eyebrow'] ) ) : ?>
				<p class="anna-about-page-hero__overline"><?php echo esc_html( $about['hero_eyebrow'] ); ?></p>
			<?php endif; ?>
			<h1 class="anna-about-page-hero__heading anna-hero__heading"><?php echo wp_kses_post( nl2br( (string) $about['hero_heading'] ) ); ?></h1>
			<?php if ( ! empty( $about['hero_subheading'] ) ) : ?>
				<p class="anna-about-page-hero__subheading anna-hero__heading"><?php echo esc_html( $about['hero_subheading'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $about['hero_tags'] ) ) : ?>
				<ul class="anna-about-page-hero__tags anna-stagger" role="list">
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
