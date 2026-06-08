<?php
/**
 * Blog page helpers.
 *
 * Content is stored in page post meta (_anna_content_blog_page)
 * managed by the Anna Content Manager plugin.
 * Falls back to design defaults when no meta has been saved.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hard-coded design defaults — shown until an admin edits the page.
 *
 * @return array<string, mixed>
 */
function anna_get_blog_default_content() {

	$categories = array(
		array(
			'slug'  => '',
			'label' => sprintf(
				'All Posts (%d)',
				wp_count_posts( 'post' )->publish
			),
		),
	);

	foreach ( get_categories( array( 'hide_empty' => true ) ) as $category ) {
		$categories[] = array(
			'slug'  => $category->slug,
			'label' => sprintf(
				'%s (%d)',
				$category->name,
				$category->count
			),
		);
	}

	return array(
		'hero_heading'     => 'Blog & Insights',
		'hero_description' => 'Explore articles on personal growth, mindfulness, wellness, and the journey toward becoming your best self.',
		'section_heading'  => 'Latest Articles',
		'section_subtext'  => 'Real insights for real change',
		'categories'       => $categories,
		'posts_per_page'   => 6,
	);
}

/**
 * Get blog page content: page meta overrides defaults.
 *
 * @return array<string, mixed>
 */
function anna_get_blog_page_content() {
	$defaults = anna_get_blog_default_content();
	$post_id  = anna_get_current_page_content_id();

	if ( ! $post_id ) {
		return $defaults;
	}

	$saved = get_post_meta( $post_id, '_anna_content_blog_page', true );
	if ( ! is_array( $saved ) || empty( $saved ) ) {
		return $defaults;
	}

	$content = array();
	foreach ( $defaults as $key => $default_value ) {
		// Categories are always from code — not user-editable via simple text.
		if ( 'categories' === $key ) {
			$content['categories'] = $default_value;
			continue;
		}
		$trimmed = trim( (string) ( $saved[ $key ] ?? '' ) );
		if ( anna_is_intentionally_blank( $trimmed ) ) {
			$content[ $key ] = '';
		} elseif ( '' !== $trimmed ) {
			$content[ $key ] = $saved[ $key ];
		} else {
			$content[ $key ] = $default_value;
		}
	}

	return $content;
}

/**
 * Estimate reading time for post content.
 *
 * @param  string $content Post content (raw).
 * @param  int    $wpm     Words per minute. Default 200.
 * @return string Localised string like "4 min read", or empty string.
 */
function anna_estimate_read_time( $content, $wpm = 200 ) {
	if ( empty( $content ) ) {
		return '';
	}

	$text      = wp_strip_all_tags( $content );
	$word_count = (int) str_word_count( $text );

	if ( $word_count < 1 ) {
		return '';
	}

	$minutes = max( 1, (int) ceil( $word_count / $wpm ) );

	return sprintf(
		/* translators: %d = number of minutes */
		_n( '%d min read', '%d min read', $minutes, 'anna-baylis' ),
		$minutes
	);
}
