<?php
/**
 * CPT: Reviews
 * @package Anna_Baylis
 * @since   1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function anna_register_reviews_cpt() {
	register_post_type( 'anna_review', array(
		'labels' => array(
			'name' => __( 'Reviews', 'anna-baylis' ),
			'singular_name' => __( 'Review', 'anna-baylis' ),
			'add_new_item' => __( 'Add New Review', 'anna-baylis' ),
			'edit_item' => __( 'Edit Review', 'anna-baylis' ),
			'all_items' => __( 'All Reviews', 'anna-baylis' ),
		),
		'public' => false, 'show_ui' => true, 'show_in_rest' => true,
		'supports' => array( 'title', 'editor', 'thumbnail' ),
		'menu_icon' => 'dashicons-star-half', 'menu_position' => 27,
	) );
}
add_action( 'init', 'anna_register_reviews_cpt' );
