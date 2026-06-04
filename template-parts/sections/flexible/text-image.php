<?php
/**
 * Shared flexible text + image section.
 *
 * @package Anna_Baylis
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$config    = get_query_var( 'anna_flexible_page_config', array() );
$content   = get_query_var( $config['query_var'] ?? 'anna_page_content', array() );
$css       = $config['css_class'] ?? 'anna-page';
$prefix    = sanitize_key( get_query_var( 'anna_section_id', 'block1' ) );
$image_id  = absint( $content[ $prefix . '_image_id' ] ?? 0 );
$position  = (string) ( $content[ $prefix . '_image_position' ] ?? 'right' );
$modifier  = 'left' === $position ? ' ' . esc_attr( $css ) . '-split--image-left' : ' ' . esc_attr( $css ) . '-split--image-right';
?>

<section class="<?php echo esc_attr( $css ); ?>-section <?php echo esc_attr( $css ); ?>-split<?php echo esc_attr( $modifier ); ?> anna-reveal">
	<div class="anna-container anna-container--max <?php echo esc_attr( $css ); ?>-split__inner">
		<div class="<?php echo esc_attr( $css ); ?>-split__content anna-reveal--left">
			<?php if ( ! empty( $content[ $prefix . '_heading' ] ) ) : ?>
				<h2 class="<?php echo esc_attr( $css ); ?>__heading"><?php echo esc_html( $content[ $prefix . '_heading' ] ); ?></h2>
			<?php endif; ?>

			<?php if ( ! empty( $content[ $prefix . '_body' ] ) ) : ?>
				<div class="<?php echo esc_attr( $css ); ?>__copy"><?php echo wp_kses_post( wpautop( (string) $content[ $prefix . '_body' ] ) ); ?></div>
			<?php endif; ?>
		</div>

		<?php if ( $image_id ) : ?>
			<figure class="<?php echo esc_attr( $css ); ?>-split__media anna-reveal--right">
				<?php echo wp_get_attachment_image( $image_id, 'large', false, array( 'class' => $css . '-split__image' ) ); ?>
			</figure>
		<?php endif; ?>
	</div>
</section>
