<?php
/**
 * Oasis "Where Oasis began" section.
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

<section class="anna-oasis-page-section anna-oasis-page-begun">
	<div class="anna-container anna-container--max">
		<?php if ( ! empty( $oasis['begun_eyebrow'] ) ) : ?>
			<p class="anna-oasis-page-begun__eyebrow"><?php echo esc_html( $oasis['begun_eyebrow'] ); ?></p>
		<?php endif; ?>

		<div class="anna-oasis-page-begun__grid">
			<div class="anna-oasis-page-begun__media-col">
				<?php if ( ! empty( $oasis['begun_image_id'] ) ) : ?>
					<figure class="anna-oasis-page-begun__figure">
						<?php echo wp_get_attachment_image( absint( $oasis['begun_image_id'] ), 'large', false, array( 'class' => 'anna-oasis-page-begun__image' ) ); ?>
					</figure>
				<?php endif; ?>

				<?php if ( ! empty( $oasis['begun_callout_label'] ) || ! empty( $oasis['begun_callout_body'] ) ) : ?>
					<aside class="anna-oasis-page-begun__callout">
						<?php if ( ! empty( $oasis['begun_callout_label'] ) ) : ?>
							<p class="anna-oasis-page-begun__callout-label"><?php echo esc_html( $oasis['begun_callout_label'] ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $oasis['begun_callout_body'] ) ) : ?>
							<p class="anna-oasis-page-begun__callout-body"><?php echo esc_html( $oasis['begun_callout_body'] ); ?></p>
						<?php endif; ?>
					</aside>
				<?php endif; ?>
			</div>

			<div class="anna-oasis-page-begun__content">
				<?php if ( ! empty( $oasis['begun_heading'] ) ) : ?>
					<h2 class="anna-oasis-page-begun__heading"><?php echo esc_html( $oasis['begun_heading'] ); ?></h2>
				<?php endif; ?>

				<?php if ( ! empty( $oasis['begun_subheading'] ) ) : ?>
					<p class="anna-oasis-page-begun__subheading"><?php echo esc_html( $oasis['begun_subheading'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $oasis['begun_body'] ) ) : ?>
					<div class="anna-oasis-page-begun__copy"><?php echo wp_kses_post( wpautop( (string) $oasis['begun_body'] ) ); ?></div>
				<?php endif; ?>

				<?php if ( ! empty( $oasis['begun_quote'] ) ) : ?>
					<blockquote class="anna-oasis-page-begun__quote">
						<p><?php echo esc_html( $oasis['begun_quote'] ); ?></p>
					</blockquote>
				<?php endif; ?>

				<?php if ( ! empty( $oasis['begun_closing'] ) ) : ?>
					<div class="anna-oasis-page-begun__closing"><?php echo wp_kses_post( wpautop( (string) $oasis['begun_closing'] ) ); ?></div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
