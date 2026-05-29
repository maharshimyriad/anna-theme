<?php
/**
 * About page "I would love to connect" section.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$about = get_query_var( 'anna_about_page_content', array() );
if ( empty( $about ) ) {
	$about = anna_get_about_page_content();
}
?>

<section class="anna-about-page-section anna-about-page-connect">
	<div class="anna-container">
		<div class="anna-about-page-connect__inner">
			<?php if ( ! empty( $about['connect_eyebrow'] ) ) : ?>
				<p class="anna-about-page-connect__eyebrow"><?php echo esc_html( $about['connect_eyebrow'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $about['connect_heading'] ) ) : ?>
				<h2 class="anna-about-page-connect__heading"><?php echo esc_html( $about['connect_heading'] ); ?></h2>
			<?php endif; ?>

			<?php if ( ! empty( $about['connect_button_text'] ) && ! empty( $about['connect_button_url'] ) ) : ?>
				<div class="anna-about-page-connect__cta">
					<a class="anna-btn anna-btn--primary" href="<?php echo esc_url( $about['connect_button_url'] ); ?>">
						<?php echo esc_html( $about['connect_button_text'] ); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

