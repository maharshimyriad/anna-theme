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

<section
	class="anna-hero-section anna-speaking-page-hero<?php echo $has_image ? ' anna-speaking-page-hero--has-image' : ''; ?>"
	<?php if ( $has_image ) : ?>
		style="background-image:url('<?php echo esc_url( anna_responsive_image_url( absint( $speaking['hero_image_id'] ), 'full' ) ); ?>');"
	<?php endif; ?>
>
	<div class="anna-speaking-page-hero__overlay" aria-hidden="true"></div>
	<div class="anna-container anna-container--max">
		<div class="anna-speaking-page-hero__content anna-reveal">
			<?php if ( ! empty( $speaking['hero_eyebrow'] ) ) : ?>
				<p class="anna-speaking-page-hero__eyebrow"><?php echo esc_html( $speaking['hero_eyebrow'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $speaking['hero_heading'] ) ) : ?>
				<h1 class="anna-speaking-page-hero__heading">
	<?php echo nl2br( esc_html( (string) $speaking['hero_heading'] ) ); ?>
</h1>
				<h1 class="anna-speaking-page-hero__heading_span">Not from a textbook.
</h1>
			<?php endif; ?>

			<?php if ( ! empty( $speaking['hero_body'] ) ) : ?>
				<p class="anna-speaking-page-hero__description"><?php echo esc_html( $speaking['hero_body'] ); ?></p>
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

	<?php if ( ! empty( $stats ) ) : ?>
		<div class="anna-speaking-page-hero__stats anna-reveal">
			<div class="anna-container anna-container--max">
				<ul class="anna-speaking-page-hero__stats-list anna-stagger" role="list">
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
</section>
