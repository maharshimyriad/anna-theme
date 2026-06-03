<?php
/**
 * Mental health support daily practice section.
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

<section class="anna-mhs-page-section anna-mhs-page-section--dark anna-mhs-page-practice">
	<div class="anna-container anna-container--max">
		<div class="anna-mhs-page-practice__content">
			<?php if ( ! empty( $mhs['practice_heading'] ) ) : ?>
				<h2 class="anna-mhs-page-practice__heading"><?php echo esc_html( $mhs['practice_heading'] ); ?></h2>
			<?php endif; ?>

			<?php if ( ! empty( $mhs['practice_body'] ) ) : ?>
				<div class="anna-mhs-page-practice__copy"><?php echo wp_kses_post( anna_render_mhs_body( $mhs['practice_body'] ) ); ?></div>
			<?php endif; ?>

			<?php if ( ! empty( $mhs['practice_link_text'] ) && ! empty( $mhs['practice_link_url'] ) ) : ?>
				<p class="anna-mhs-page-practice__link-wrap">
					<a class="anna-mhs-page-practice__link" href="<?php echo esc_url( $mhs['practice_link_url'] ); ?>">
						<?php echo esc_html( $mhs['practice_link_text'] ); ?>
					</a>
				</p>
			<?php endif; ?>
		</div>
	</div>
</section>
