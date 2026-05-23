<?php
/**
 * Theme helpers and utility functions
 *
 * @package anna-theme
 */

/**
 * Get SVG Icon
 *
 * @param string $icon_name Name of the SVG file without extension.
 * @return string HTML output for the SVG.
 */
function anna_get_svg( $icon_name ) {
	$svg_path = ANNA_THEME_DIR . 'assets/images/icons/' . $icon_name . '.svg';
	if ( file_exists( $svg_path ) ) {
		return file_get_contents( $svg_path );
	}
	return '';
}

/**
 * Get dynamic theme color from settings or fallback.
 * 
 * @param string $setting_name The setting key.
 * @param string $default The default color hex.
 */
function anna_get_theme_color( $setting_name, $default ) {
	$options = get_option( 'anna_theme_colors' );
	if ( isset( $options[ $setting_name ] ) && ! empty( $options[ $setting_name ] ) ) {
		return $options[ $setting_name ];
	}
	return $default;
}
