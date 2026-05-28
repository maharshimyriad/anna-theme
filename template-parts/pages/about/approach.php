<?php
/**
 * About page approach section.
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

<section class="anna-about-page-section anna-about-page-approach">
	<div class="anna-container">
		<?php if ( ! empty( $about['approach_eyebrow'] ) ) : ?>
			<span class="anna-about-page__eyebrow"><?php echo esc_html( $about['approach_eyebrow'] ); ?></span>
		<?php endif; ?>
		<h2 class="anna-about-page__heading"><?php echo wp_kses_post( nl2br( (string) ( $about['approach_heading'] ?? '' ) ) ); ?></h2>
		<?php if ( ! empty( $about['approach_intro'] ) ) : ?>
			<p class="anna-about-page__intro"><?php echo esc_html( $about['approach_intro'] ); ?></p>
		<?php endif; ?>
		<div class="anna-about-page-two-col">
			<div class="anna-about-page__copy"><?php echo wp_kses_post( wpautop( (string) ( $about['approach_left_body'] ?? '' ) ) ); ?></div>
			<div class="anna-about-page__copy"><?php echo wp_kses_post( wpautop( (string) ( $about['approach_right_body'] ?? '' ) ) ); ?></div>
		</div>
	</div>
</section>
