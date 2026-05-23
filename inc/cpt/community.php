<?php
/**
 * CPT: Community Programs
 * @package Anna_Baylis
 * @since   1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function anna_register_community_cpt() {
	register_post_type( 'anna_community', array(
		'labels' => array(
			'name' => __( 'Community Programs', 'anna-baylis' ),
			'singular_name' => __( 'Community Program', 'anna-baylis' ),
			'add_new_item' => __( 'Add New Program', 'anna-baylis' ),
			'edit_item' => __( 'Edit Program', 'anna-baylis' ),
			'all_items' => __( 'All Programs', 'anna-baylis' ),
		),
		'public' => true, 'show_in_rest' => true, 'has_archive' => true,
		'rewrite' => array( 'slug' => 'community', 'with_front' => false ),
		'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
		'menu_icon' => 'dashicons-groups', 'menu_position' => 30,
	) );
}
add_action( 'init', 'anna_register_community_cpt' );
