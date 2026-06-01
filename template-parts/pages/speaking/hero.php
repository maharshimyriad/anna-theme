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

$has_bg_image = ! empty( $speaking['hero_image_id'] );
$bg_url       = $has_bg_image ? anna_responsive_image_url( absint( $speaking['hero_image_id'] ), 'full' ) : ANNA_IMAGES . '/speaking-hero-fallback.png';
?>

<section class="anna-speaking-hero" style="background-image:url('<?php echo esc_url( $bg_url ); ?>');">
	<div class="anna-speaking-hero__overlay" aria-hidden="true"></div>
	<div class="anna-container anna-container--max">
		<div class="anna-speaking-hero__content">
			<?php if ( ! empty( $speaking['hero_eyebrow'] ) ) : ?>
				<p class="anna-speaking-eyebrow anna-speaking-hero__eyebrow"><?php echo esc_html( $speaking['hero_eyebrow'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $speaking['hero_heading'] ) ) : ?>
				<h1 class="anna-speaking-hero__heading"><?php echo wp_kses_post( nl2br( (string) $speaking['hero_heading'] ) ); ?></h1>
			<?php endif; ?>

			<?php if ( ! empty( $speaking['hero_description'] ) ) : ?>
				<p class="anna-speaking-hero__description"><?php echo esc_html( $speaking['hero_description'] ); ?></p>
			<?php endif; ?>

			<div class="anna-speaking-hero__actions">
				<?php if ( ! empty( $speaking['hero_button_text'] ) && ! empty( $speaking['hero_button_url'] ) ) : ?>
					<a class="anna-speaking-btn anna-speaking-btn--light" href="<?php echo esc_url( $speaking['hero_button_url'] ); ?>">
						<?php echo esc_html( $speaking['hero_button_text'] ); ?>
					</a>
				<?php endif; ?>
				<?php if ( ! empty( $speaking['hero_secondary_text'] ) && ! empty( $speaking['hero_secondary_url'] ) ) : ?>
					<a class="anna-speaking-hero__link" href="<?php echo esc_url( $speaking['hero_secondary_url'] ); ?>">
						<?php echo esc_html( $speaking['hero_secondary_text'] ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<?php if ( ! empty( $speaking['hero_stats'] ) ) : ?>
		<div class="anna-speaking-hero__stats" aria-label="<?php esc_attr_e( 'Speaking credentials', 'anna-baylis' ); ?>">
			<div class="anna-container anna-container--max">
				<ul class="anna-speaking-hero__stats-list">
					<?php foreach ( $speaking['hero_stats'] as $stat ) : ?>
						<li class="anna-speaking-hero__stat">
							<strong><?php echo esc_html( $stat['value'] ?? '' ); ?></strong>
							<span><?php echo esc_html( $stat['label'] ?? '' ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	<?php endif; ?>
</section>
