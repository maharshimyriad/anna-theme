<?php
/**
 * Template part: Health Support page CTA.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = anna_get_option( 'hs_pg_cta_heading' );
$body    = anna_get_option( 'hs_pg_cta_body' );

$buttons = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$text = anna_get_option( 'hs_pg_cta_btn_' . $i . '_text' );
	$url  = anna_get_option( 'hs_pg_cta_btn_' . $i . '_url' );
	if ( $text && $url ) {
		$buttons[] = array( 'text' => $text, 'url' => $url );
	}
}
?>

<section class="anna-section anna-section--md anna-hs-cta">
	<div class="anna-container anna-container--narrow anna-text-center" data-reveal="fade-up">
		<?php if ( $heading ) : ?>
			<h2 class="anna-heading-2 anna-text-primary"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>
		
		<?php if ( $body ) : ?>
			<div class="anna-body-text anna-margin-bottom-lg">
				<?php echo wpautop( esc_html( $body ) ); ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $buttons ) ) : ?>
			<div class="anna-hs-cta__actions anna-flex anna-flex--center anna-flex--wrap anna-flex--gap-sm">
				<?php foreach ( $buttons as $index => $btn ) : ?>
					<a href="<?php echo esc_url( $btn['url'] ); ?>" class="anna-btn <?php echo 0 === $index ? 'anna-btn--primary' : 'anna-btn--outline'; ?>">
						<?php echo esc_html( $btn['text'] ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
