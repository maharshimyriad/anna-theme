<?php
/**
 * Mental health support how I work section.
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

<section class="anna-mhs-page-section anna-mhs-page-section--cream anna-mhs-page-work">
	<div class="anna-container anna-container--max">
		<div class="anna-mhs-page-work__content anna-reveal">
			<?php if ( ! empty( $mhs['work_heading'] ) ) : ?>
				<h2 class="anna-mhs-page__heading"><?php echo esc_html( $mhs['work_heading'] ); ?></h2>
			<?php endif; ?>

			<?php if ( ! empty( $mhs['work_body'] ) ) : ?>
				<div class="anna-mhs-page__copy"><?php echo wp_kses_post( anna_render_mhs_body( $mhs['work_body'] ) ); ?></div>
			<?php endif; ?>
		</div>
	</div>
</section>
