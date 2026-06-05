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
	return array(
		'hero_heading'     => 'Blog & Insights',
		'hero_description' => 'Explore articles on personal growth, mindfulness, wellness, and the journey toward becoming your best self.',
		'section_heading'  => 'Latest Articles',
		'section_subtext'  => 'Real insights for real change',
		'categories'       => array(
			array( 'slug' => '',                'label' => 'All Posts' ),
			array( 'slug' => 'mindfulness',     'label' => 'Mindfulness' ),
			array( 'slug' => 'personal-growth', 'label' => 'Personal Growth' ),
			array( 'slug' => 'wellness',        'label' => 'Wellness' ),
			array( 'slug' => 'life-coaching',   'label' => 'Life Coaching' ),
		),
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
