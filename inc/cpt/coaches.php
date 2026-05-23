<?php
/**
 * CPT: Coaches / Speakers
 * @package Anna_Baylis
 * @since   1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function anna_register_coaches_cpt() {
	register_post_type( 'anna_coach', array(
		'labels' => array(
			'name' => __( 'Coaches', 'anna-baylis' ),
			'singular_name' => __( 'Coach', 'anna-baylis' ),
			'add_new_item' => __( 'Add New Coach', 'anna-baylis' ),
			'edit_item' => __( 'Edit Coach', 'anna-baylis' ),
			'all_items' => __( 'All Coaches', 'anna-baylis' ),
		),
		'public' => true, 'show_in_rest' => true, 'has_archive' => true,
		'rewrite' => array( 'slug' => 'coaches', 'with_front' => false ),
		'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
		'menu_icon' => 'dashicons-businessperson', 'menu_position' => 28,
	) );
}
add_action( 'init', 'anna_register_coaches_cpt' );
