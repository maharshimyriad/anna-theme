<?php
/**
 * Asset enqueueing.
 *
 * All scripts and styles are loaded here with proper versioning,
 * dependency management, and defer/async attributes.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build an asset version from the file modified time.
 *
 * @param string $relative_path Asset path relative to the theme root.
 * @return string
 */
function anna_asset_version( $relative_path ) {
	$path = ANNA_DIR . '/' . ltrim( $relative_path, '/' );

	if ( file_exists( $path ) ) {
		return (string) filemtime( $path );
	}

	return ANNA_VERSION;
}

/**
 * Enqueue front-end styles.
 */
function anna_enqueue_styles() {
	wp_enqueue_style(
		'anna-variables',
		ANNA_CSS . '/variables.css',
		array(),
		anna_asset_version( 'assets/css/variables.css' )
	);

	wp_enqueue_style(
		'anna-reset',
		ANNA_CSS . '/reset.css',
		array( 'anna-variables' ),
		anna_asset_version( 'assets/css/reset.css' )
	);

	wp_enqueue_style(
		'anna-base',
		ANNA_CSS . '/base.css',
		array( 'anna-reset' ),
		anna_asset_version( 'assets/css/base.css' )
	);

	wp_enqueue_style(
		'anna-layout',
		ANNA_CSS . '/layout.css',
		array( 'anna-base' ),
		anna_asset_version( 'assets/css/layout.css' )
	);

	wp_enqueue_style(
		'anna-utilities',
		ANNA_CSS . '/utilities.css',
		array( 'anna-layout' ),
		anna_asset_version( 'assets/css/utilities.css' )
	);

	wp_enqueue_style(
		'anna-animations',
		ANNA_CSS . '/animations.css',
		array( 'anna-utilities' ),
		anna_asset_version( 'assets/css/animations.css' )
	);

	$components = array( 'buttons', 'cards', 'badges', 'navigation', 'forms', 'testimonials', 'media' );
	$prev_dep   = 'anna-animations';

	foreach ( $components as $component ) {
		$handle = 'anna-' . $component;
		$file   = 'assets/css/components/' . $component . '.css';

		wp_enqueue_style(
			$handle,
			ANNA_CSS . '/components/' . $component . '.css',
			array( $prev_dep ),
			anna_asset_version( $file )
		);

		$prev_dep = $handle;
	}

	$sections = array( 'header', 'footer' );

	foreach ( $sections as $section ) {
		$handle = 'anna-section-' . $section;
		$file   = 'assets/css/sections/' . $section . '.css';

		wp_enqueue_style(
			$handle,
			ANNA_CSS . '/sections/' . $section . '.css',
			array( $prev_dep ),
			anna_asset_version( $file )
		);

		$prev_dep = $handle;
	}

	if ( is_front_page() ) {
		wp_enqueue_style(
			'anna-page-home',
			ANNA_CSS . '/pages/home.css',
			array( $prev_dep ),
			anna_asset_version( 'assets/css/pages/home.css' )
		);

		$prev_dep = 'anna-page-home';
	}

	if ( is_page_template( 'page-about.php' ) || is_page( 'about' ) ) {
		wp_enqueue_style(
			'anna-page-about',
			ANNA_CSS . '/pages/about.css',
			array( $prev_dep ),
			anna_asset_version( 'assets/css/pages/about.css' )
		);

		$prev_dep = 'anna-page-about';
	}

	if ( is_page_template( 'page-coaching.php' ) || is_page( 'coaching' ) ) {
		wp_enqueue_style(
			'anna-page-coaching',
			ANNA_CSS . '/pages/coaching.css',
			array( $prev_dep ),
			anna_asset_version( 'assets/css/pages/coaching.css' )
		);

		$prev_dep = 'anna-page-coaching';
	}

	wp_enqueue_style(
		'anna-google-fonts',
		'https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&family=Mulish:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,500&display=swap',
		array(),
		null
	);
}
add_action( 'wp_enqueue_scripts', 'anna_enqueue_styles' );

/**
 * Enqueue front-end scripts.
 */
function anna_enqueue_scripts() {
	$animations_enabled = anna_get_option( 'animations_enabled', true );

	if ( $animations_enabled ) {
		wp_enqueue_script(
			'gsap-core',
			'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js',
			array(),
			'3.12.5',
			array( 'strategy' => 'defer' )
		);

		wp_enqueue_script(
			'gsap-scroll-trigger',
			'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js',
			array( 'gsap-core' ),
			'3.12.5',
			array( 'strategy' => 'defer' )
		);

		wp_enqueue_script(
			'anna-parallax',
			ANNA_JS . '/gsap/parallax.js',
			array( 'gsap-scroll-trigger' ),
			anna_asset_version( 'assets/js/gsap/parallax.js' ),
			array( 'strategy' => 'defer' )
		);

		wp_enqueue_script(
			'anna-scroll-triggers',
			ANNA_JS . '/gsap/scroll-triggers.js',
			array( 'gsap-scroll-trigger' ),
			anna_asset_version( 'assets/js/gsap/scroll-triggers.js' ),
			array( 'strategy' => 'defer' )
		);

		wp_enqueue_script(
			'anna-hero-anim',
			ANNA_JS . '/gsap/hero.js',
			array( 'gsap-scroll-trigger' ),
			anna_asset_version( 'assets/js/gsap/hero.js' ),
			array( 'strategy' => 'defer' )
		);

		wp_enqueue_script(
			'anna-animations',
			ANNA_JS . '/gsap/animations.js',
			array( 'anna-hero-anim', 'anna-scroll-triggers', 'anna-parallax' ),
			anna_asset_version( 'assets/js/gsap/animations.js' ),
			array( 'strategy' => 'defer' )
		);
	}

	wp_enqueue_script(
		'anna-header-js',
		ANNA_JS . '/components/header.js',
		array(),
		anna_asset_version( 'assets/js/components/header.js' ),
		array( 'strategy' => 'defer' )
	);

	wp_enqueue_script(
		'anna-mobile-menu',
		ANNA_JS . '/components/mobile-menu.js',
		array( 'anna-header-js' ),
		anna_asset_version( 'assets/js/components/mobile-menu.js' ),
		array( 'strategy' => 'defer' )
	);

	wp_enqueue_script(
		'anna-stats-counter',
		ANNA_JS . '/components/stats-counter.js',
		array(),
		anna_asset_version( 'assets/js/components/stats-counter.js' ),
		array( 'strategy' => 'defer' )
	);

	wp_enqueue_script(
		'anna-scroll-reveal',
		ANNA_JS . '/components/scroll-reveal.js',
		array(),
		anna_asset_version( 'assets/js/components/scroll-reveal.js' ),
		array( 'strategy' => 'defer' )
	);

	if ( is_page_template( 'page-coaching.php' ) || is_page( 'coaching' ) ) {
		wp_enqueue_script(
			'anna-coaching-faq',
			ANNA_JS . '/pages/coaching-faq.js',
			array(),
			anna_asset_version( 'assets/js/pages/coaching-faq.js' ),
			array( 'strategy' => 'defer' )
		);
	}

	$deps = array( 'anna-header-js', 'anna-mobile-menu', 'anna-stats-counter', 'anna-scroll-reveal' );
	if ( is_page_template( 'page-coaching.php' ) || is_page( 'coaching' ) ) {
		$deps[] = 'anna-coaching-faq';
	}
	if ( $animations_enabled ) {
		$deps[] = 'anna-animations';
	}

	wp_enqueue_script(
		'anna-theme',
		ANNA_JS . '/theme.js',
		$deps,
		anna_asset_version( 'assets/js/theme.js' ),
		array( 'strategy' => 'defer' )
	);

	wp_localize_script(
		'anna-theme',
		'annaTheme',
		array(
			'ajaxUrl'           => admin_url( 'admin-ajax.php' ),
			'nonce'             => wp_create_nonce( 'anna_nonce' ),
			'animationsEnabled' => $animations_enabled,
			'animationSpeed'    => anna_get_option( 'animation_speed', 'normal' ),
			'themeUri'          => ANNA_URI,
			'isHome'            => is_front_page(),
			'reducedMotion'     => false,
		)
	);
}
add_action( 'wp_enqueue_scripts', 'anna_enqueue_scripts' );

/**
 * Enqueue admin styles and scripts.
 *
 * @param string $hook Current admin page hook.
 */
function anna_admin_enqueue( $hook ) {
	if ( 'toplevel_page_anna-theme-settings' !== $hook ) {
		return;
	}

	wp_enqueue_media();

	wp_enqueue_style(
		'anna-admin-settings',
		ANNA_CSS . '/admin/admin-settings.css',
		array(),
		anna_asset_version( 'assets/css/admin/admin-settings.css' )
	);

	wp_enqueue_style( 'wp-color-picker' );

	wp_enqueue_script(
		'anna-admin-settings',
		ANNA_JS . '/admin/settings-admin.js',
		array( 'wp-color-picker', 'jquery', 'jquery-ui-sortable' ),
		anna_asset_version( 'assets/js/admin/settings-admin.js' ),
		true
	);
}
add_action( 'admin_enqueue_scripts', 'anna_admin_enqueue' );

/**
 * Add preconnect for Google Fonts performance.
 */
function anna_preconnect_fonts() {
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action( 'wp_head', 'anna_preconnect_fonts', 1 );
