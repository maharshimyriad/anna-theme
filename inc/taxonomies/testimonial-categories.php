<?php
/**
 * Taxonomy: Testimonial Categories
 * @package Anna_Baylis
 * @since   1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function anna_register_testimonial_categories() {
	register_taxonomy( 'anna_testimonial_cat', 'anna_testimonial', array(
		'labels' => array(
			'name' => __( 'Testimonial Categories', 'anna-baylis' ),
			'singular_name' => __( 'Testimonial Category', 'anna-baylis' ),
		),
		'public' => false, 'hierarchical' => true, 'show_ui' => true, 'show_in_rest' => true,
		'show_admin_column' => true,
	) );
}
add_action( 'init', 'anna_register_testimonial_categories' );
