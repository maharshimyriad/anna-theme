<?php
/**
 * Oasis waitlist CTA section.
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

<section id="oasis-waitlist" class="anna-oasis-page-section anna-oasis-page-waitlist">
	<div class="anna-container anna-container--max anna-oasis-page-waitlist__inner">
		<?php if ( ! empty( $oasis['waitlist_eyebrow'] ) ) : ?>
			<p class="anna-oasis-page-waitlist__eyebrow"><?php echo esc_html( $oasis['waitlist_eyebrow'] ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $oasis['waitlist_heading'] ) ) : ?>
			<h2 class="anna-oasis-page-waitlist__heading"><?php echo esc_html( $oasis['waitlist_heading'] ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $oasis['waitlist_button_text'] ) && ! empty( $oasis['waitlist_button_url'] ) ) : ?>
			<div class="anna-oasis-page-waitlist__cta">
				<a class="anna-oasis-page-waitlist__btn" href="<?php echo esc_url( $oasis['waitlist_button_url'] ); ?>">
					<?php echo esc_html( $oasis['waitlist_button_text'] ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>
