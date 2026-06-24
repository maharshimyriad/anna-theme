<?php
/**
 * Speaking page hero.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$speaking = get_query_var( 'anna_speaking_page_content', array() );
if ( empty( $speaking ) ) {
	$speaking = anna_get_speaking_page_content();
}

$has_image = ! empty( $speaking['hero_image_id'] );
$stats     = isset( $speaking['hero_stat_items'] ) && is_array( $speaking['hero_stat_items'] ) ? $speaking['hero_stat_items'] : array();
?>

<div class="anna-speaking-hero-wrapper">

	<section
		class="anna-hero-section anna-speaking-page-hero<?php echo $has_image ? ' anna-speaking-page-hero--has-image' : ''; ?>"
		aria-labelledby="speaking-hero-heading"
	>
		<?php if ( $has_image ) :
			$_img_id  = absint( $speaking['hero_image_id'] );
			$_srcset  = wp_get_attachment_image_srcset( $_img_id, 'full' );
			$_alt     = get_post_meta( $_img_id, '_wp_attachment_image_alt', true );
		?>
			<img
				class="anna-hero__bg-image"
				src="<?php echo esc_url( anna_responsive_image_url( $_img_id, 'full' ) ); ?>"
				<?php if ( $_srcset ) : ?>srcset="<?php echo esc_attr( $_srcset ); ?>" sizes="100vw"<?php endif; ?>
				alt="<?php echo esc_attr( $_alt ); ?>"
				loading="eager"
				fetchpriority="high"
				decoding="async"
			>
		<?php endif; ?>
		<div class="anna-speaking-page-hero__overlay" aria-hidden="true"></div>
		<div class="anna-container anna-container--max">
			<div class="anna-speaking-page-hero__content anna-reveal">

				<?php if ( ! empty( $speaking['hero_eyebrow'] ) ) : ?>
					<p class="anna-speaking-page-hero__eyebrow"><?php echo esc_html( $speaking['hero_eyebrow'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $speaking['hero_heading'] ) ) : ?>
					<h1 class="anna-speaking-page-hero__heading" id="speaking-hero-heading">
						<?php echo nl2br( esc_html( (string) $speaking['hero_heading'] ) ); ?>
					</h1>
					<h2 class="anna-speaking-page-hero__heading_span">Not from a textbook.</h2>
				<?php endif; ?>

				<!-- Description: desktop only (inside hero image) -->
				<?php if ( ! empty( $speaking['hero_body'] ) ) : ?>
					<p class="anna-speaking-page-hero__description anna-speaking-page-hero__description--overlay">
						<?php echo esc_html( $speaking['hero_body'] ); ?>
					</p>
				<?php endif; ?>

				<div class="anna-speaking-page-hero__actions">
					<?php if ( ! empty( $speaking['hero_button_text'] ) && ! empty( $speaking['hero_button_url'] ) ) : ?>
						<a class="anna-speaking-page-hero__btn anna-speaking-page-hero__btn--primary" href="<?php echo esc_url( $speaking['hero_button_url'] ); ?>">
							<?php echo esc_html( $speaking['hero_button_text'] ); ?>
						</a>
					<?php endif; ?>

					<?php if ( ! empty( $speaking['hero_secondary_text'] ) && ! empty( $speaking['hero_secondary_url'] ) ) : ?>
						<a class="anna-speaking-page-hero__link" href="<?php echo esc_url( $speaking['hero_secondary_url'] ); ?>">
							<?php echo esc_html( $speaking['hero_secondary_text'] ); ?>
						</a>
					<?php endif; ?>
				</div>

			</div>
		</div>
	</section>

	<!-- Below-image strip: visible on mobile only -->
	<div class="anna-speaking-hero__mobile-strip">
		<div class="anna-container">

			<?php if ( ! empty( $speaking['hero_body'] ) ) : ?>
				<!-- Description: mobile only (below hero image) -->
				<p class="anna-speaking-hero__mobile-strip__description">
					<?php echo esc_html( $speaking['hero_body'] ); ?>
				</p>
			<?php endif; ?>

			<?php if ( ! empty( $stats ) ) : ?>
				<!-- Stats: mobile only (below description) -->
				<ul class="anna-speaking-hero__mobile-strip__stats" role="list" aria-label="<?php esc_attr_e( 'Key achievements', 'anna-baylis' ); ?>">
					<?php
					foreach ( $stats as $stat ) :
						if ( ! is_array( $stat ) ) {
							continue;
						}
						$value = trim( (string) ( $stat['value'] ?? '' ) );
						$label = trim( (string) ( $stat['label'] ?? '' ) );
						if ( '' === $value && '' === $label ) {
							continue;
						}
						?>
						<li class="anna-speaking-hero__mobile-strip__stat">
							<?php if ( '' !== $value ) : ?>
								<strong class="anna-speaking-hero__mobile-strip__stat-value"><?php echo esc_html( $value ); ?></strong>
							<?php endif; ?>
							<?php if ( '' !== $label ) : ?>
								<span class="anna-speaking-hero__mobile-strip__stat-label"><?php echo esc_html( $label ); ?></span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

		</div>
	</div>

	<?php if ( ! empty( $stats ) ) : ?>
		<!-- Stats bar: visible on desktop only (dark bar at bottom of hero) -->
		<div class="anna-speaking-page-hero__stats anna-speaking-page-hero__stats--desktop anna-reveal">
			<div class="anna-container anna-container--max">
				<ul class="anna-speaking-page-hero__stats-list anna-stagger" role="list" aria-label="<?php esc_attr_e( 'Key achievements', 'anna-baylis' ); ?>">
					<?php foreach ( $stats as $stat ) : ?>
						<?php
						if ( ! is_array( $stat ) ) {
							continue;
						}
						$value = trim( (string) ( $stat['value'] ?? '' ) );
						$label = trim( (string) ( $stat['label'] ?? '' ) );
						if ( '' === $value && '' === $label ) {
							continue;
						}
						?>
						<li class="anna-speaking-page-hero__stat">
							<?php if ( '' !== $value ) : ?>
								<strong class="anna-speaking-page-hero__stat-value"><?php echo esc_html( $value ); ?></strong>
							<?php endif; ?>
							<?php if ( '' !== $label ) : ?>
								<span class="anna-speaking-page-hero__stat-label"><?php echo esc_html( $label ); ?></span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	<?php endif; ?>

</div><!-- /.anna-speaking-hero-wrapper -->
