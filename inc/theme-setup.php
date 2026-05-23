<?php
/**
 * Theme setup.
 *
 * Registers all core WordPress features, nav menus, and widget areas.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function anna_setup() {
	// Load theme translations.
	load_theme_textdomain( 'anna-baylis', ANNA_DIR . '/languages' );

	// Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	// Enable post thumbnails on posts and pages.
	add_theme_support( 'post-thumbnails' );

	// Enable custom logo.
	add_theme_support(
		'custom-logo',
		array(
			'height'               => 80,
			'width'                => 240,
			'flex-height'          => true,
			'flex-width'           => true,
			'header-text'          => array( 'site-title', 'site-description' ),
			'unlink-homepage-logo' => false,
		)
	);

	// Switch default core markup to valid HTML5.
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
			'navigation-widgets',
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Add support for full and wide align images.
	add_theme_support( 'align-wide' );

	// Add support for responsive embedded content.
	add_theme_support( 'responsive-embeds' );

	// Add support for editor styles.
	add_theme_support( 'editor-styles' );

	// Add support for block editor color palette.
	add_theme_support(
		'editor-color-palette',
		array(
			array(
				'name'  => __( 'Primary Deep Green', 'anna-baylis' ),
				'slug'  => 'primary',
				'color' => '#007063',
			),
			array(
				'name'  => __( 'Accent Green', 'anna-baylis' ),
				'slug'  => 'accent',
				'color' => '#4CA591',
			),
			array(
				'name'  => __( 'Soft Background', 'anna-baylis' ),
				'slug'  => 'soft-bg',
				'color' => '#F2F6F2',
			),
			array(
				'name'  => __( 'White', 'anna-baylis' ),
				'slug'  => 'white',
				'color' => '#FFFFFF',
			),
		)
	);

	// Register navigation menus.
	register_nav_menus(
		array(
			'primary'  => __( 'Primary Navigation', 'anna-baylis' ),
			'footer'   => __( 'Footer Navigation', 'anna-baylis' ),
			'mobile'   => __( 'Mobile Navigation', 'anna-baylis' ),
			'footer-2' => __( 'Footer Column 2 Navigation', 'anna-baylis' ),
		)
	);
}
add_action( 'after_setup_theme', 'anna_setup' );

/**
 * Register widget areas (sidebars).
 */
function anna_widgets_init() {
	// Footer Widget Area — Column 1.
	register_sidebar(
		array(
			'name'          => __( 'Footer — Column 1', 'anna-baylis' ),
			'id'            => 'footer-1',
			'description'   => __( 'Widgets in the first footer column.', 'anna-baylis' ),
			'before_widget' => '<div id="%1$s" class="anna-footer__widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="anna-footer__widget-title">',
			'after_title'   => '</h3>',
		)
	);

	// Footer Widget Area — Column 2.
	register_sidebar(
		array(
			'name'          => __( 'Footer — Column 2', 'anna-baylis' ),
			'id'            => 'footer-2',
			'description'   => __( 'Widgets in the second footer column.', 'anna-baylis' ),
			'before_widget' => '<div id="%1$s" class="anna-footer__widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="anna-footer__widget-title">',
			'after_title'   => '</h3>',
		)
	);

	// Footer Widget Area — Column 3.
	register_sidebar(
		array(
			'name'          => __( 'Footer — Column 3', 'anna-baylis' ),
			'id'            => 'footer-3',
			'description'   => __( 'Widgets in the third footer column.', 'anna-baylis' ),
			'before_widget' => '<div id="%1$s" class="anna-footer__widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="anna-footer__widget-title">',
			'after_title'   => '</h3>',
		)
	);

	// Footer Widget Area — Column 4 (Newsletter).
	register_sidebar(
		array(
			'name'          => __( 'Footer — Newsletter Column', 'anna-baylis' ),
			'id'            => 'footer-newsletter',
			'description'   => __( 'Newsletter signup area in the footer.', 'anna-baylis' ),
			'before_widget' => '<div id="%1$s" class="anna-footer__widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="anna-footer__widget-title">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'anna_widgets_init' );

/**
 * Set content width in pixels, based on the theme's design and stylesheet.
 *
 * @global int $content_width
 */
function anna_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'anna_content_width', 900 );
}
add_action( 'after_setup_theme', 'anna_content_width', 0 );
