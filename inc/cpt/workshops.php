<?php
/**
 * CPT: Workshops
 * @package Anna_Baylis
 * @since   1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function anna_register_workshops_cpt() {
	register_post_type( 'anna_workshop', array(
		'labels' => array(
			'name' => __( 'Workshops', 'anna-baylis' ),
			'singular_name' => __( 'Workshop', 'anna-baylis' ),
			'add_new_item' => __( 'Add New Workshop', 'anna-baylis' ),
			'edit_item' => __( 'Edit Workshop', 'anna-baylis' ),
			'all_items' => __( 'All Workshops', 'anna-baylis' ),
		),
		'public' => true, 'show_in_rest' => true, 'has_archive' => true,
		'rewrite' => array( 'slug' => 'workshops', 'with_front' => false ),
		'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'custom-fields' ),
		'menu_icon' => 'dashicons-calendar-alt', 'menu_position' => 29,
	) );
}
add_action( 'init', 'anna_register_workshops_cpt' );
