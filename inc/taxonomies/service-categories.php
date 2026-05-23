<?php
/**
 * Taxonomy: Service Categories
 * @package Anna_Baylis
 * @since   1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function anna_register_service_categories() {
	register_taxonomy( 'anna_service_cat', 'anna_service', array(
		'labels' => array(
			'name' => __( 'Service Categories', 'anna-baylis' ),
			'singular_name' => __( 'Service Category', 'anna-baylis' ),
			'add_new_item' => __( 'Add Service Category', 'anna-baylis' ),
		),
		'public' => true, 'hierarchical' => true, 'show_in_rest' => true,
		'show_admin_column' => true,
		'rewrite' => array( 'slug' => 'service-category', 'with_front' => false ),
	) );
}
add_action( 'init', 'anna_register_service_categories' );
