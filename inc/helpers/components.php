<?php
/**
 * Reusable component helpers
 *
 * @package Anna_Baylis
 */

/**
 * Render a standardized button
 *
 * @param string $text Button text
 * @param string $url Button URL
 * @param string $style Button style (primary, secondary, outline)
 * @param array $classes Additional CSS classes
 */
function anna_render_button($text, $url, $style = 'primary', $classes = array()) {
	$class_list = array('btn', 'btn--' . $style);
	if ( ! empty( $classes ) ) {
		$class_list = array_merge($class_list, $classes);
	}
	
	$class_string = esc_attr( implode(' ', $class_list) );
	$url = esc_url( $url );
	$text = esc_html( $text );

	echo '<a href="' . $url . '" class="' . $class_string . '">' . $text . '</a>';
}

/**
 * Render a section title
 */
function anna_render_section_title($title, $subtitle = '', $tag = 'h2', $classes = array()) {
	$class_list = array('section-title');
	if ( ! empty( $classes ) ) {
		$class_list = array_merge($class_list, $classes);
	}
	$class_string = esc_attr( implode(' ', $class_list) );
	
	echo '<header class="section-header">';
	if ( $subtitle ) {
		echo '<span class="section-subtitle">' . esc_html($subtitle) . '</span>';
	}
	echo '<' . esc_attr($tag) . ' class="' . $class_string . '">' . esc_html($title) . '</' . esc_attr($tag) . '>';
	echo '</header>';
}
