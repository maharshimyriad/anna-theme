<?php
/**
 * About page rock-bottom section.
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

<section class="anna-about-page-section anna-about-page-rock">
	<div class="anna-container">
		<h2 class="anna-about-page__heading"><?php echo esc_html( $about['rock_heading'] ?? '' ); ?></h2>
		<div class="anna-about-page-two-col">
			<div class="anna-about-page__copy"><?php echo wp_kses_post( wpautop( (string) ( $about['rock_left_body'] ?? '' ) ) ); ?></div>
			<div class="anna-about-page__copy"><?php echo wp_kses_post( wpautop( (string) ( $about['rock_right_body'] ?? '' ) ) ); ?></div>
		</div>
	</div>
</section>
