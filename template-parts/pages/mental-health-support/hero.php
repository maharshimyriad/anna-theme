<?php
/**
 * Mental health support page hero.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mhs = get_query_var( 'anna_mhs_page_content', array() );
if ( empty( $mhs ) ) {
	$mhs = anna_get_mhs_page_content();
}

$has_image = ! empty( $mhs['hero_image_id'] );
?>

<section
	class="anna-mhs-page-hero<?php echo $has_image ? ' anna-mhs-page-hero--has-image' : ''; ?>"
	<?php if ( $has_image ) : ?>
		style="background-image:url('<?php echo esc_url( anna_responsive_image_url( absint( $mhs['hero_image_id'] ), 'full' ) ); ?>');"
	<?php endif; ?>
>
	<div class="anna-mhs-page-hero__overlay" aria-hidden="true"></div>
	<div class="anna-container anna-container--max">
		<div class="anna-mhs-page-hero__content anna-reveal">
			<?php if ( ! empty( $mhs['hero_eyebrow'] ) ) : ?>
				<p class="anna-mhs-page-hero__eyebrow"><?php echo esc_html( $mhs['hero_eyebrow'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $mhs['hero_heading'] ) ) : ?>
				<h1 class="anna-mhs-page-hero__heading"><?php echo esc_html( $mhs['hero_heading'] ); ?></h1>
			<?php endif; ?>
		</div>
	</div>
</section>
