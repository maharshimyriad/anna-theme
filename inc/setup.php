<?php
/**
 * Theme setup functions
 *
 * @package anna-theme
 */

if ( ! function_exists( 'anna_theme_setup' ) ) {
	function anna_theme_setup() {
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );

		register_nav_menus(
			array(
				'primary' => esc_html__( 'Primary Menu', 'anna-theme' ),
				'footer'  => esc_html__( 'Footer Menu', 'anna-theme' ),
			)
		);

		add_theme_support(
			'html5',
			array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
		);

		add_theme_support(
			'custom-logo',
			array( 'height' => 250, 'width' => 250, 'flex-width' => true, 'flex-height' => true )
		);
	}
}
add_action( 'after_setup_theme', 'anna_theme_setup' );
