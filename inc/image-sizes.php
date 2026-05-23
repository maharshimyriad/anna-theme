<?php
/**
 * Custom image sizes.
 *
 * Registers all theme-specific image sizes for responsive,
 * art-directed image handling throughout the theme.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register custom image sizes.
 */
function anna_register_image_sizes() {
	// Hero images — large, split layout.
	add_image_size( 'anna-hero', 900, 1100, true );
	add_image_size( 'anna-hero-md', 600, 750, true );

	// About section portrait.
	add_image_size( 'anna-portrait', 600, 750, true );
	add_image_size( 'anna-portrait-sm', 400, 500, true );

	// Service cards.
	add_image_size( 'anna-card', 560, 420, true );
	add_image_size( 'anna-card-sm', 400, 300, true );

	// Testimonial avatars.
	add_image_size( 'anna-avatar', 80, 80, true );
	add_image_size( 'anna-avatar-lg', 120, 120, true );

	// Wide editorial images.
	add_image_size( 'anna-wide', 1440, 600, true );

	// Square thumbnail.
	add_image_size( 'anna-square', 400, 400, true );
}
add_action( 'after_setup_theme', 'anna_register_image_sizes' );

/**
 * Add custom image sizes to the media library selector.
 *
 * @param  array $sizes Existing size names.
 * @return array
 */
function anna_add_image_sizes_to_editor( $sizes ) {
	return array_merge(
		$sizes,
		array(
			'anna-hero'       => __( 'Anna — Hero', 'anna-baylis' ),
			'anna-portrait'   => __( 'Anna — Portrait', 'anna-baylis' ),
			'anna-card'       => __( 'Anna — Card', 'anna-baylis' ),
			'anna-avatar'     => __( 'Anna — Avatar', 'anna-baylis' ),
			'anna-wide'       => __( 'Anna — Wide Editorial', 'anna-baylis' ),
			'anna-square'     => __( 'Anna — Square', 'anna-baylis' ),
		)
	);
}
add_filter( 'image_size_names_choose', 'anna_add_image_sizes_to_editor' );
