<?php
/**
 * Oasis page hero.
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

$has_image = ! empty( $oasis['hero_image_id'] );
?>

<div class="anna-oasis-hero-wrapper">

	<section
		class="anna-hero-section anna-oasis-page-hero<?php echo $has_image ? ' anna-oasis-page-hero--has-image' : ''; ?>"
		<?php if ( $has_image ) : ?>
			style="background-image:url('<?php echo esc_url( anna_responsive_image_url( absint( $oasis['hero_image_id'] ), 'full' ) ); ?>');"
		<?php endif; ?>
	>
		<div class="anna-container anna-container--max">
			<div class="anna-oasis-page-hero__content anna-reveal">

				<?php if ( ! empty( $oasis['hero_breadcrumb'] ) ) : ?>
					<p class="anna-oasis-page-hero__breadcrumb"><?php echo esc_html( $oasis['hero_breadcrumb'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $oasis['hero_heading'] ) ) : ?>
					<h1 class="anna-oasis-page-hero__heading"><?php echo esc_html( $oasis['hero_heading'] ); ?></h1>
				<?php endif; ?>

				<?php if ( ! empty( $oasis['hero_subheading'] ) ) : ?>
					<p class="anna-oasis-page-hero__description anna-oasis-page-hero__description--overlay"><?php echo esc_html( $oasis['hero_subheading'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $oasis['hero_body'] ) ) : ?>
					<!-- Body copy: visible on desktop only (inside hero image) -->
					<div class="anna-oasis-page-hero__copy anna-oasis-page-hero__copy--overlay">
						<?php echo wp_kses_post( wpautop( (string) $oasis['hero_body'] ) ); ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $oasis['hero_button_text'] ) && ! empty( $oasis['hero_button_url'] ) ) : ?>
					<div class="anna-oasis-page-hero__cta">
						<a class="anna-btn anna-oasis-page-hero__btn" href="<?php echo esc_url( $oasis['hero_button_url'] ); ?>">
							<?php echo esc_html( $oasis['hero_button_text'] ); ?>
						</a>
					</div>
				<?php endif; ?>

			</div>
		</div>
	</section>

	<?php if ( ! empty( $oasis['hero_body'] ) ) : ?>
		<!-- Body copy strip: visible on mobile only (below hero image) -->
		<div class="anna-oasis-hero__body-strip">
			<div class="anna-container">
				<div class="anna-oasis-hero__body-strip__copy">
					<?php echo wp_kses_post( wpautop( (string) $oasis['hero_body'] ) ); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

</div><!-- /.anna-oasis-hero-wrapper -->
