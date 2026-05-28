<?php
/**
 * About page coach section.
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

<section class="anna-about-page-section anna-about-page-band">
	<div class="anna-container">
		<h2 class="anna-about-page__heading anna-about-page__heading--inline"><?php echo wp_kses_post( nl2br( (string) ( $about['coach_heading'] ?? '' ) ) ); ?></h2>
		<div class="anna-about-page-two-col">
			<div class="anna-about-page__copy"><?php echo wp_kses_post( wpautop( (string) ( $about['coach_left_body'] ?? '' ) ) ); ?></div>
			<div>
				<div class="anna-about-page__copy"><?php echo wp_kses_post( wpautop( (string) ( $about['coach_right_body'] ?? '' ) ) ); ?></div>
				<?php if ( ! empty( $about['coach_quote'] ) ) : ?>
					<blockquote class="anna-about-page__quote"><?php echo esc_html( $about['coach_quote'] ); ?></blockquote>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
