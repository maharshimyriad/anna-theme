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

$oasis = get_query_var( 'anna_mhs_page_content', array() );
if ( empty( $oasis ) ) {
	$oasis = anna_get_oasis_page_content();
}

$has_image = ! empty( $oasis['hero_image_id'] );
?>

<section
	class="anna-mhs-page-hero<?php echo $has_image ? ' anna-mhs-page-hero--has-image' : ''; ?>"
	<?php if ( $has_image ) : ?>
		style="background-image:url('<?php echo esc_url( anna_responsive_image_url( absint( $oasis['hero_image_id'] ), 'full' ) ); ?>');"
	<?php endif; ?>
>
<!-- 	<div class="anna-mhs-page-hero__overlay" aria-hidden="true"></div> -->
	<div class="anna-container anna-container--max">
		<div class="anna-mhs-page-hero__content">
			<?php if ( ! empty( $oasis['hero_breadcrumb'] ) ) : ?>
				<p class="anna-mhs-page-hero__breadcrumb"><?php echo esc_html( $oasis['hero_breadcrumb'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $oasis['hero_heading'] ) ) : ?>
				<h1 class="anna-mhs-page-hero__heading"><?php echo esc_html( $oasis['hero_heading'] ); ?></h1>
			<?php endif; ?>

			<?php if ( ! empty( $oasis['hero_subheading'] ) ) : ?>
				<p class="anna-mhs-page-hero__description"><?php echo esc_html( $oasis['hero_subheading'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $oasis['hero_body'] ) ) : ?>
				<div class="anna-mhs-page-hero__copy"><?php echo wp_kses_post( wpautop( (string) $oasis['hero_body'] ) ); ?></div>
			<?php endif; ?>

			<?php if ( ! empty( $oasis['hero_button_text'] ) && ! empty( $oasis['hero_button_url'] ) ) : ?>
				<div class="anna-mhs-page-hero__cta">
					<a class="anna-btn anna-mhs-page-hero__btn" href="<?php echo esc_url( $oasis['hero_button_url'] ); ?>">
						<?php echo esc_html( $oasis['hero_button_text'] ); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
