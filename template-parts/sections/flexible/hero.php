<?php
/**
 * Shared flexible page hero section.
 *
 * @package Anna_Baylis
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$config  = get_query_var( 'anna_flexible_page_config', array() );
$content = get_query_var( $config['query_var'] ?? 'anna_page_content', array() );
$css     = $config['css_class'] ?? 'anna-page';

$has_image = ! empty( $content['hero_image_id'] );
?>

<section
	class="<?php echo esc_attr( $css ); ?>-hero anna-reveal<?php echo $has_image ? ' ' . esc_attr( $css ) . '-hero--has-image' : ''; ?>"
	<?php if ( $has_image ) : ?>
		style="background-image:url('<?php echo esc_url( anna_responsive_image_url( absint( $content['hero_image_id'] ), 'full' ) ); ?>');"
	<?php endif; ?>
>
	<div class="<?php echo esc_attr( $css ); ?>-hero__overlay" aria-hidden="true"></div>
	<div class="anna-container anna-container--max">
		<div class="<?php echo esc_attr( $css ); ?>-hero__content">
			<?php if ( ! empty( $content['hero_eyebrow'] ) ) : ?>
				<p class="<?php echo esc_attr( $css ); ?>-hero__eyebrow"><?php echo esc_html( $content['hero_eyebrow'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $content['hero_heading'] ) ) : ?>
				<h1 class="<?php echo esc_attr( $css ); ?>-hero__heading"><?php echo esc_html( $content['hero_heading'] ); ?></h1>
			<?php endif; ?>

			<?php if ( ! empty( $content['hero_body'] ) ) : ?>
				<p class="<?php echo esc_attr( $css ); ?>-hero__body"><?php echo esc_html( $content['hero_body'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $content['hero_button_text'] ) && ! empty( $content['hero_button_url'] ) ) : ?>
				<a class="anna-btn <?php echo esc_attr( $css ); ?>-hero__btn" href="<?php echo esc_url( $content['hero_button_url'] ); ?>">
					<?php echo esc_html( $content['hero_button_text'] ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
