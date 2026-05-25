<?php
/**
 * Template part: Final CTA Section
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = anna_get_option( 'cta_heading', '' );
$desc    = anna_get_option( 'cta_description', '' );
$cta_p   = anna_get_cta( 'primary' );
$cta_s   = anna_get_cta( 'secondary' );
?>

<section class="anna-cta anna-section anna-section--xl" id="final-cta" aria-labelledby="cta-heading">
	<div class="anna-container">
		<div class="anna-cta__inner anna-reveal">
			<?php if ( anna_get_option( 'cta_eyebrow', '' ) ) : ?>
				<span class="anna-eyebrow"><?php echo esc_html( anna_get_option( 'cta_eyebrow', '' ) ); ?></span>
			<?php endif; ?>

			<h2 class="anna-cta__heading" id="cta-heading"><?php echo wp_kses_post( $heading ); ?></h2>

			<?php if ( $desc ) : ?>
				<p class="anna-cta__description"><?php echo esc_html( $desc ); ?></p>
			<?php endif; ?>

			<?php if ( anna_get_option( 'cta_trust', '' ) ) : ?>
				<p class="anna-cta__trust"><?php echo esc_html( anna_get_option( 'cta_trust', '' ) ); ?></p>
			<?php endif; ?>

			<div class="anna-cta__actions">
				<a href="<?php echo esc_url( $cta_p['url'] ); ?>" class="anna-btn anna-btn--primary anna-btn--lg"><?php echo esc_html( $cta_p['text'] ); ?></a>
				<?php if ( ! empty( $cta_s['text'] ) ) : ?>
					<a href="<?php echo esc_url( $cta_s['url'] ); ?>" class="anna-btn anna-btn--ghost anna-btn--lg"><?php echo esc_html( $cta_s['text'] ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
