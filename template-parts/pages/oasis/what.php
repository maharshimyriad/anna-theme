<?php
/**
 * Oasis "What Oasis is" section.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$oasis = get_query_var( 'anna_oasis_page_content', array() );
if ( empty( $oasis ) ) {
	$oasis = anna_get_oasis_page_content();
}
?>

<section class="anna-oasis-page-section anna-oasis-page-what">
	<div class="anna-container anna-container--max anna-oasis-page-what__inner">
		<?php if ( ! empty( $oasis['what_eyebrow'] ) ) : ?>
			<p class="anna-oasis-page-what__eyebrow"><?php echo esc_html( $oasis['what_eyebrow'] ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $oasis['what_heading'] ) ) : ?>
			<h2 class="anna-oasis-page-what__heading"><?php echo esc_html( $oasis['what_heading'] ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $oasis['what_body'] ) ) : ?>
			<div class="anna-oasis-page-what__body"><?php echo wp_kses_post( wpautop( (string) $oasis['what_body'] ) ); ?></div>
		<?php endif; ?>

		<?php if ( ! empty( $oasis['what_footer_line'] ) ) : ?>
			<p class="anna-oasis-page-what__footer"><?php echo esc_html( $oasis['what_footer_line'] ); ?></p>
		<?php endif; ?>
	</div>
</section>
