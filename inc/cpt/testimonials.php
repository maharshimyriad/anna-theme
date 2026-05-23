<?php
/**
 * CPT: Testimonials
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function anna_register_testimonials_cpt() {
	$labels = array(
		'name'                  => __( 'Testimonials', 'anna-baylis' ),
		'singular_name'         => __( 'Testimonial', 'anna-baylis' ),
		'add_new'               => __( 'Add Testimonial', 'anna-baylis' ),
		'add_new_item'          => __( 'Add New Testimonial', 'anna-baylis' ),
		'edit_item'             => __( 'Edit Testimonial', 'anna-baylis' ),
		'new_item'              => __( 'New Testimonial', 'anna-baylis' ),
		'view_item'             => __( 'View Testimonial', 'anna-baylis' ),
		'search_items'          => __( 'Search Testimonials', 'anna-baylis' ),
		'not_found'             => __( 'No testimonials found', 'anna-baylis' ),
		'not_found_in_trash'    => __( 'No testimonials found in trash', 'anna-baylis' ),
		'all_items'             => __( 'All Testimonials', 'anna-baylis' ),
		'featured_image'        => __( 'Client Photo', 'anna-baylis' ),
		'set_featured_image'    => __( 'Set client photo', 'anna-baylis' ),
		'remove_featured_image' => __( 'Remove client photo', 'anna-baylis' ),
	);

	register_post_type( 'anna_testimonial', array(
		'labels'       => $labels,
		'public'       => false,
		'show_ui'      => true,
		'show_in_rest' => true,
		'has_archive'  => false,
		'rewrite'      => false,
		'supports'     => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		'menu_icon'    => 'dashicons-format-quote',
		'menu_position' => 25,
	) );
}
add_action( 'init', 'anna_register_testimonials_cpt' );
