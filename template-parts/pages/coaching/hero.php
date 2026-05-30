<?php
/**
 * Coaching page hero section.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$coaching = get_query_var( 'anna_coaching_page_content', array() );
if ( empty( $coaching ) ) {
	$coaching = anna_get_coaching_page_content();
}
?>

<section
	class="anna-coaching-page-hero"
	<?php if ( ! empty( $coaching['hero_image_id'] ) ) : ?>
		style="background-image:url('<?php echo esc_url( anna_responsive_image_url( absint( $coaching['hero_image_id'] ), 'full' ) ); ?>');"
	<?php endif; ?>
>
	<div class="anna-coaching-page-hero__overlay" aria-hidden="true"></div>
	<div class="anna-container">
		<div class="anna-coaching-page-hero__content">
			<?php if ( ! empty( $coaching['hero_eyebrow'] ) ) : ?>
				<p class="anna-coaching-page-hero__eyebrow"><?php echo esc_html( $coaching['hero_eyebrow'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $coaching['hero_heading'] ) ) : ?>
				<h1 class="anna-coaching-page-hero__heading"><?php echo wp_kses_post( nl2br( (string) $coaching['hero_heading'] ) ); ?></h1>
			<?php endif; ?>

			<?php if ( ! empty( $coaching['hero_description'] ) ) : ?>
				<p class="anna-coaching-page-hero__description"><?php echo esc_html( $coaching['hero_description'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $coaching['hero_tags'] ) ) : ?>
				<ul class="anna-coaching-page-hero__tags" role="list">
					<?php foreach ( (array) $coaching['hero_tags'] as $tag ) : ?>
						<?php if ( '' === trim( (string) $tag ) ) : ?>
							<?php continue; ?>
						<?php endif; ?>
						<li class="anna-coaching-page-hero__tag"><?php echo esc_html( $tag ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( ! empty( $coaching['hero_button_text'] ) && ! empty( $coaching['hero_button_url'] ) ) : ?>
				<div class="anna-coaching-page-hero__cta">
					<a class="anna-btn anna-btn--primary" href="<?php echo esc_url( $coaching['hero_button_url'] ); ?>">
						<?php echo esc_html( $coaching['hero_button_text'] ); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
