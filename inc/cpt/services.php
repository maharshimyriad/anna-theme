<?php
/**
 * CPT: Services
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function anna_register_services_cpt() {
	$labels = array(
		'name'               => __( 'Services', 'anna-baylis' ),
		'singular_name'      => __( 'Service', 'anna-baylis' ),
		'add_new'            => __( 'Add Service', 'anna-baylis' ),
		'add_new_item'       => __( 'Add New Service', 'anna-baylis' ),
		'edit_item'          => __( 'Edit Service', 'anna-baylis' ),
		'new_item'           => __( 'New Service', 'anna-baylis' ),
		'view_item'          => __( 'View Service', 'anna-baylis' ),
		'search_items'       => __( 'Search Services', 'anna-baylis' ),
		'not_found'          => __( 'No services found', 'anna-baylis' ),
		'not_found_in_trash' => __( 'No services found in trash', 'anna-baylis' ),
		'all_items'          => __( 'All Services', 'anna-baylis' ),
	);

	register_post_type( 'anna_service', array(
		'labels'       => $labels,
		'public'       => true,
		'show_in_rest' => true,
		'has_archive'  => true,
		'rewrite'      => array( 'slug' => 'services', 'with_front' => false ),
		'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
		'menu_icon'    => 'dashicons-star-filled',
		'menu_position' => 26,
	) );
}
add_action( 'init', 'anna_register_services_cpt' );
