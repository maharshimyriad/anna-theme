<?php
/**
 * Shared flexible CTA section.
 *
 * @package Anna_Baylis
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$config  = get_query_var( 'anna_flexible_page_config', array() );
$content = get_query_var( $config['query_var'] ?? 'anna_page_content', array() );
$css     = $config['css_class'] ?? 'anna-page';
?>

<section class="<?php echo esc_attr( $css ); ?>-section <?php echo esc_attr( $css ); ?>-section--cream <?php echo esc_attr( $css ); ?>-cta anna-reveal">
	<div class="anna-container anna-container--max <?php echo esc_attr( $css ); ?>-cta__inner">
		<?php if ( ! empty( $content['cta_heading'] ) ) : ?>
			<h2 class="<?php echo esc_attr( $css ); ?>-cta__heading"><?php echo esc_html( $content['cta_heading'] ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $content['cta_subheading'] ) ) : ?>
			<p class="<?php echo esc_attr( $css ); ?>-cta__subheading"><?php echo esc_html( $content['cta_subheading'] ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $content['cta_body'] ) ) : ?>
			<p class="<?php echo esc_attr( $css ); ?>-cta__body"><?php echo esc_html( $content['cta_body'] ); ?></p>
		<?php endif; ?>

		<div class="<?php echo esc_attr( $css ); ?>-cta__actions">
			<?php if ( ! empty( $content['cta_button_primary_text'] ) && ! empty( $content['cta_button_primary_url'] ) ) : ?>
				<a class="anna-btn <?php echo esc_attr( $css ); ?>-cta__btn <?php echo esc_attr( $css ); ?>-cta__btn--primary" href="<?php echo esc_url( $content['cta_button_primary_url'] ); ?>">
					<?php echo esc_html( $content['cta_button_primary_text'] ); ?>
				</a>
			<?php endif; ?>

			<?php if ( ! empty( $content['cta_button_secondary_text'] ) && ! empty( $content['cta_button_secondary_url'] ) ) : ?>
				<a class="anna-btn <?php echo esc_attr( $css ); ?>-cta__btn <?php echo esc_attr( $css ); ?>-cta__btn--outline" href="<?php echo esc_url( $content['cta_button_secondary_url'] ); ?>">
					<?php echo esc_html( $content['cta_button_secondary_text'] ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
