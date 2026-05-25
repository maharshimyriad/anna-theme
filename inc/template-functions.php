<?php
/**
 * Template functions.
 *
 * Functions that modify or extend template behaviour.
 * Called by action hooks inside templates.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Outputs the skip-to-content link.
 * Must be the very first item inside <body>.
 */
function anna_skip_link() {
	echo '<a class="anna-skip-link" href="#main-content">' . esc_html__( 'Skip to main content', 'anna-baylis' ) . '</a>';
}
add_action( 'wp_body_open', 'anna_skip_link' );

/**
 * Outputs an inline <style> block that injects all
 * admin-controlled theme options as CSS custom properties,
 * overriding the defaults set in variables.css.
 *
 * Hooked on wp_head so it sits after variables.css loads.
 */
function anna_dynamic_css_vars() {
	$opts = get_option( 'anna_theme_options', array() );
	if ( empty( $opts ) ) {
		return;
	}

	$map = array(
		'color_primary'      => '--color-primary',
		'color_accent'       => '--color-accent',
		'color_bg_soft'      => '--color-bg-soft',
		'color_text'         => '--color-text',
		'color_heading'      => '--color-heading',
		'font_heading'       => '--font-heading',
		'font_body'          => '--font-body',
		'font_size_base'     => '--text-base',
		'border_radius_btn'  => '--btn-radius',
		'container_max'      => '--container-max',
		'section_padding_md' => '--section-padding-md',
		'animation_speed'    => '--anim-duration-base',
	);

	$css = ':root{';
	foreach ( $map as $option_key => $css_var ) {
		if ( ! empty( $opts[ $option_key ] ) ) {
			$css .= esc_attr( $css_var ) . ':' . esc_attr( $opts[ $option_key ] ) . ';';
		}
	}
	$css .= '}';

	echo '<style id="anna-dynamic-vars">' . $css . '</style>' . "\n";
}
add_action( 'wp_head', 'anna_dynamic_css_vars', 20 );

/**
 * Adds `data-scroll-header` attribute to body for JS targeting.
 *
 * @param array $classes Body classes.
 * @return array
 */
function anna_body_classes( $classes ) {
	if ( is_front_page() ) {
		$classes[] = 'anna-is-homepage';
	}

	if ( is_singular() ) {
		$classes[] = 'anna-is-singular';
	}

	$header_style = anna_get_option( 'header_style', 'transparent' );
	$classes[]    = 'anna-header--' . sanitize_html_class( $header_style );

	return $classes;
}
add_filter( 'body_class', 'anna_body_classes' );

/**
 * Resolve the preferred site logo attachment ID.
 *
 * @return int
 */
function anna_get_site_logo_id() {
	$theme_logo_id = absint( anna_get_option( 'site_logo_id', 0 ) );

	if ( $theme_logo_id ) {
		return $theme_logo_id;
	}

	return absint( get_theme_mod( 'custom_logo' ) );
}

/**
 * Outputs the correct logo markup — custom logo or site title.
 *
 * @param string $context 'header' or 'footer'.
 */
function anna_site_logo( $context = 'header' ) {
	$custom_logo_id = anna_get_site_logo_id();

	if ( $custom_logo_id ) {
		$logo_src = wp_get_attachment_image_src( $custom_logo_id, 'full' );
		if ( $logo_src ) {
			printf(
				'<a href="%1$s" class="anna-logo anna-logo--%4$s" rel="home" aria-label="%2$s"><img src="%3$s" alt="%2$s" class="anna-logo__img" loading="eager" decoding="async" width="%5$s" height="%6$s"></a>',
				esc_url( home_url( '/' ) ),
				esc_attr( get_bloginfo( 'name' ) ),
				esc_url( $logo_src[0] ),
				esc_attr( $context ),
				esc_attr( $logo_src[1] ),
				esc_attr( $logo_src[2] )
			);
			return;
		}
	}

	// Fallback: site title as text logo.
	printf(
		'<a href="%1$s" class="anna-logo anna-logo--text anna-logo--%3$s" rel="home">%2$s</a>',
		esc_url( home_url( '/' ) ),
		esc_html( get_bloginfo( 'name' ) ),
		esc_attr( $context )
	);
}

/**
 * Determine if the current page should use a transparent header.
 *
 * @return bool
 */
function anna_has_transparent_header() {
	$header_style = anna_get_option( 'header_style', 'transparent' );
	return 'transparent' === $header_style && ( is_front_page() || is_page_template( 'template-full-width.php' ) );
}

/**
 * Get structured homepage stats data.
 *
 * @return array
 */
function anna_get_stats() {
	$defaults = anna_get_default_options();

	return apply_filters(
		'anna_homepage_stats',
		array(
			array(
				'value'  => anna_get_option( 'stat_1_value', $defaults['stat_1_value'] ),
				'label'  => anna_get_option( 'stat_1_label', $defaults['stat_1_label'] ),
			),
			array(
				'value'  => anna_get_option( 'stat_2_value', $defaults['stat_2_value'] ),
				'label'  => anna_get_option( 'stat_2_label', $defaults['stat_2_label'] ),
			),
			array(
				'value'  => anna_get_option( 'stat_3_value', $defaults['stat_3_value'] ),
				'label'  => anna_get_option( 'stat_3_label', $defaults['stat_3_label'] ),
			),
		)
	);
}

/**
 * Get services for homepage display (latest N, from CPT).
 *
 * @param  int $count Number of services to retrieve.
 * @return WP_Query
 */
function anna_get_homepage_services( $count = 6 ) {
	return new WP_Query(
		array(
			'post_type'      => 'anna_service',
			'posts_per_page' => absint( $count ),
			'post_status'    => 'publish',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'no_found_rows'  => true,
		)
	);
}

/**
 * Get testimonials for homepage display.
 *
 * @param  int $count Number of testimonials.
 * @return WP_Query
 */
function anna_get_homepage_testimonials( $count = 8 ) {
	return new WP_Query(
		array(
			'post_type'      => 'anna_testimonial',
			'posts_per_page' => absint( $count ),
			'post_status'    => 'publish',
			'meta_query'     => array(
				array(
					'key'     => '_anna_testimonial_featured',
					'value'   => '1',
					'compare' => '=',
				),
			),
			'orderby'        => 'rand',
			'no_found_rows'  => true,
		)
	);
}

/**
 * Flush rewrite rules on theme activation.
 */
function anna_rewrite_flush() {
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'anna_rewrite_flush' );
