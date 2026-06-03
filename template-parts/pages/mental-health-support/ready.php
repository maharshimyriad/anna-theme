<?php
/**
 * Mental health support CTA section.
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
?>

<section class="anna-mhs-page-section anna-mhs-page-ready">
	<div class="anna-container anna-container--max anna-mhs-page-ready__inner">
		<?php if ( ! empty( $mhs['ready_heading'] ) ) : ?>
			<h2 class="anna-mhs-page-ready__heading"><?php echo esc_html( $mhs['ready_heading'] ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $mhs['ready_subheading'] ) ) : ?>
			<p class="anna-mhs-page-ready__subheading"><?php echo esc_html( $mhs['ready_subheading'] ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $mhs['ready_body'] ) ) : ?>
			<p class="anna-mhs-page-ready__body"><?php echo esc_html( $mhs['ready_body'] ); ?></p>
		<?php endif; ?>

		<div class="anna-mhs-page-ready__actions">
			<?php if ( ! empty( $mhs['ready_button_primary_text'] ) && ! empty( $mhs['ready_button_primary_url'] ) ) : ?>
				<a class="anna-btn anna-mhs-page-ready__btn anna-mhs-page-ready__btn--primary" href="<?php echo esc_url( $mhs['ready_button_primary_url'] ); ?>">
					<?php echo esc_html( $mhs['ready_button_primary_text'] ); ?>
				</a>
			<?php endif; ?>

			<?php if ( ! empty( $mhs['ready_button_secondary_text'] ) && ! empty( $mhs['ready_button_secondary_url'] ) ) : ?>
				<a class="anna-btn anna-mhs-page-ready__btn anna-mhs-page-ready__btn--outline" href="<?php echo esc_url( $mhs['ready_button_secondary_url'] ); ?>">
					<?php echo esc_html( $mhs['ready_button_secondary_text'] ); ?>
				</a>
			<?php endif; ?>

			<?php if ( ! empty( $mhs['ready_button_tertiary_text'] ) && ! empty( $mhs['ready_button_tertiary_url'] ) ) : ?>
				<a class="anna-btn anna-mhs-page-ready__btn anna-mhs-page-ready__btn--outline" href="<?php echo esc_url( $mhs['ready_button_tertiary_url'] ); ?>">
					<?php echo esc_html( $mhs['ready_button_tertiary_text'] ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
