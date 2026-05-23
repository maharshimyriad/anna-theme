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
 * Enqueue front-end styles.
 */
function anna_enqueue_styles() {
	$ver = ANNA_VERSION;

	// ── Design System ───────────────────────────────────────────────────────
	wp_enqueue_style(
		'anna-variables',
		ANNA_CSS . '/variables.css',
		array(),
		$ver
	);

	wp_enqueue_style(
		'anna-reset',
		ANNA_CSS . '/reset.css',
		array( 'anna-variables' ),
		$ver
	);

	wp_enqueue_style(
		'anna-base',
		ANNA_CSS . '/base.css',
		array( 'anna-reset' ),
		$ver
	);

	wp_enqueue_style(
		'anna-layout',
		ANNA_CSS . '/layout.css',
		array( 'anna-base' ),
		$ver
	);

	wp_enqueue_style(
		'anna-utilities',
		ANNA_CSS . '/utilities.css',
		array( 'anna-layout' ),
		$ver
	);

	wp_enqueue_style(
		'anna-animations',
		ANNA_CSS . '/animations.css',
		array( 'anna-utilities' ),
		$ver
	);

	// ── Components ──────────────────────────────────────────────────────────
	$components = array( 'buttons', 'cards', 'badges', 'navigation', 'forms', 'testimonials', 'media' );
	$prev_dep   = 'anna-animations';

	foreach ( $components as $component ) {
		$handle = 'anna-' . $component;
		wp_enqueue_style( $handle, ANNA_CSS . '/components/' . $component . '.css', array( $prev_dep ), $ver );
		$prev_dep = $handle;
	}

	// ── Sections ────────────────────────────────────────────────────────────
	$sections = array( 'header', 'hero', 'intro', 'recognition', 'services', 'about', 'testimonials-section', 'cta', 'footer' );

	foreach ( $sections as $section ) {
		$handle = 'anna-section-' . $section;
		wp_enqueue_style( $handle, ANNA_CSS . '/sections/' . $section . '.css', array( $prev_dep ), $ver );
		$prev_dep = $handle;
	}

	// ── Google Fonts ─────────────────────────────────────────────────────────
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
	$ver = ANNA_VERSION;

	// ── GSAP (CDN) ─────────────────────────────────────────────────────────
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

		// ── GSAP Animation Modules ─────────────────────────────────────────
		wp_enqueue_script(
			'anna-parallax',
			ANNA_JS . '/gsap/parallax.js',
			array( 'gsap-scroll-trigger' ),
			$ver,
			array( 'strategy' => 'defer' )
		);

		wp_enqueue_script(
			'anna-scroll-triggers',
			ANNA_JS . '/gsap/scroll-triggers.js',
			array( 'gsap-scroll-trigger' ),
			$ver,
			array( 'strategy' => 'defer' )
		);

		wp_enqueue_script(
			'anna-hero-anim',
			ANNA_JS . '/gsap/hero.js',
			array( 'gsap-scroll-trigger' ),
			$ver,
			array( 'strategy' => 'defer' )
		);

		wp_enqueue_script(
			'anna-animations',
			ANNA_JS . '/gsap/animations.js',
			array( 'anna-hero-anim', 'anna-scroll-triggers', 'anna-parallax' ),
			$ver,
			array( 'strategy' => 'defer' )
		);
	}

	// ── Components JS ───────────────────────────────────────────────────────
	wp_enqueue_script(
		'anna-header-js',
		ANNA_JS . '/components/header.js',
		array(),
		$ver,
		array( 'strategy' => 'defer' )
	);

	wp_enqueue_script(
		'anna-mobile-menu',
		ANNA_JS . '/components/mobile-menu.js',
		array( 'anna-header-js' ),
		$ver,
		array( 'strategy' => 'defer' )
	);

	wp_enqueue_script(
		'anna-testimonials-js',
		ANNA_JS . '/components/testimonials.js',
		array(),
		$ver,
		array( 'strategy' => 'defer' )
	);

	wp_enqueue_script(
		'anna-stats-counter',
		ANNA_JS . '/components/stats-counter.js',
		array(),
		$ver,
		array( 'strategy' => 'defer' )
	);

	wp_enqueue_script(
		'anna-scroll-reveal',
		ANNA_JS . '/components/scroll-reveal.js',
		array(),
		$ver,
		array( 'strategy' => 'defer' )
	);

	// ── Main Theme JS (Entry point) ─────────────────────────────────────────
	$deps = array( 'anna-header-js', 'anna-mobile-menu', 'anna-testimonials-js', 'anna-stats-counter', 'anna-scroll-reveal' );
	if ( $animations_enabled ) {
		$deps[] = 'anna-animations';
	}

	wp_enqueue_script(
		'anna-theme',
		ANNA_JS . '/theme.js',
		$deps,
		$ver,
		array( 'strategy' => 'defer' )
	);

	// ── Pass data to JS ─────────────────────────────────────────────────────
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
			'reducedMotion'     => false, // overridden by JS prefers-reduced-motion check
		)
	);
}
add_action( 'wp_enqueue_scripts', 'anna_enqueue_scripts' );

/**
 * Enqueue admin styles and scripts.
 */
function anna_admin_enqueue( $hook ) {
	// Only load on our theme settings pages.
	if ( strpos( $hook, 'anna-' ) === false ) {
		return;
	}

	wp_enqueue_style(
		'anna-admin-settings',
		ANNA_CSS . '/admin/admin-settings.css',
		array(),
		ANNA_VERSION
	);

	wp_enqueue_style( 'wp-color-picker' );

	wp_enqueue_script(
		'anna-admin-settings',
		ANNA_JS . '/admin/settings-admin.js',
		array( 'wp-color-picker', 'jquery', 'jquery-ui-sortable' ),
		ANNA_VERSION,
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
