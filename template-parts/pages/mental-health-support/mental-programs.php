<?php
/**
 * Mental health support mental programs section.
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

<section class="anna-mhs-page-section anna-mhs-page-section--cream anna-mhs-page-programs">
	<div class="anna-container anna-container--max">
		<div class="anna-mhs-page-programs__content">
			<?php if ( ! empty( $mhs['programs_heading'] ) ) : ?>
				<h2 class="anna-mhs-page__heading"><?php echo esc_html( $mhs['programs_heading'] ); ?></h2>
			<?php endif; ?>

			<?php if ( ! empty( $mhs['programs_body'] ) ) : ?>
				<div class="anna-mhs-page__copy"><?php echo wp_kses_post( anna_render_mhs_body( $mhs['programs_body'] ) ); ?></div>
			<?php endif; ?>
		</div>
	</div>
</section>
