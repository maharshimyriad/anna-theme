<?php
/**
 * Enqueue scripts and styles.
 *
 * @package anna-theme
 */

function anna_theme_scripts() {
	wp_enqueue_style( 'anna-theme-style', get_stylesheet_uri(), array(), ANNA_THEME_VERSION );
	wp_enqueue_style( 'anna-google-fonts', 'https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&family=Mulish:wght@300;400;500;600;700&display=swap', array(), null );
	wp_enqueue_script( 'gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js', array(), '3.12.2', true );
	wp_enqueue_script( 'gsap-scrolltrigger', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js', array('gsap'), '3.12.2', true );
	wp_enqueue_script( 'anna-theme-main', ANNA_THEME_URI . 'assets/js/main.js', array('gsap', 'gsap-scrolltrigger'), ANNA_THEME_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'anna_theme_scripts' );

function anna_theme_defer_scripts( $tag, $handle ) {
	$defer_scripts = array( 'gsap', 'gsap-scrolltrigger', 'anna-theme-main' );
	if ( in_array( $handle, $defer_scripts, true ) ) {
		return str_replace( ' src', ' defer="defer" src', $tag );
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'anna_theme_defer_scripts', 10, 2 );
