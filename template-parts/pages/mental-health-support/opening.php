<?php
/**
 * Mental health support opening section.
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

<section class="anna-mhs-page-section anna-mhs-page-opening">
	<div class="anna-container anna-container--max">
		<div class="anna-mhs-page-opening__grid">
			<div class="anna-mhs-page-opening__content">
				<?php if ( ! empty( $mhs['opening_heading'] ) ) : ?>
					<h2 class="anna-mhs-page__heading"><?php echo esc_html( $mhs['opening_heading'] ); ?></h2>
				<?php endif; ?>

				<?php if ( ! empty( $mhs['opening_body'] ) ) : ?>
					<div class="anna-mhs-page__copy"><?php echo wp_kses_post( anna_render_mhs_body( $mhs['opening_body'] ) ); ?></div>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $mhs['opening_image_id'] ) ) : ?>
				<figure class="anna-mhs-page-opening__media">
					<?php anna_responsive_image( absint( $mhs['opening_image_id'] ), 'large', 'anna-img-cover' ); ?>
				</figure>
			<?php endif; ?>
		</div>
	</div>
</section>
