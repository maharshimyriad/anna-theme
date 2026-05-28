<?php
/**
 * About page qualifications section.
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

<section class="anna-about-page-section anna-about-page-qualifications">
	<div class="anna-container">
		<h2 class="anna-about-page__heading"><?php echo esc_html( $about['qual_heading'] ?? '' ); ?></h2>
		<?php if ( ! empty( $about['qual_intro'] ) ) : ?>
			<p class="anna-about-page__intro"><?php echo esc_html( $about['qual_intro'] ); ?></p>
		<?php endif; ?>
		<?php if ( ! empty( $about['qual_items'] ) ) : ?>
			<ul class="anna-about-page-qualifications__list" role="list">
				<?php foreach ( (array) $about['qual_items'] as $item ) : ?>
					<li><?php echo esc_html( $item ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
