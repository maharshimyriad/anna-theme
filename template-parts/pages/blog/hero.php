<?php
/**
 * Blog page: hero/header section.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$blog = get_query_var( 'anna_blog_page_content', array() );
if ( empty( $blog ) ) {
	$blog = anna_get_blog_page_content();
}
?>

<section class="anna-blog-page-hero">
	<div class="anna-container anna-container--max">
		<div class="anna-blog-page-hero__content anna-reveal">
			<?php if ( ! empty( $blog['hero_heading'] ) ) : ?>
				<h1 class="anna-blog-page-hero__heading"><?php echo esc_html( $blog['hero_heading'] ); ?></h1>
			<?php endif; ?>
			<?php if ( ! empty( $blog['hero_description'] ) ) : ?>
				<p class="anna-blog-page-hero__description"><?php echo esc_html( $blog['hero_description'] ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
