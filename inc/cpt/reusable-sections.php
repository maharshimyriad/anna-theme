<?php
/**
 * CPT: Reusable Sections
 * @package Anna_Baylis
 * @since   1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function anna_register_reusable_sections_cpt() {
	register_post_type( 'anna_section', array(
		'labels' => array(
			'name' => __( 'Reusable Sections', 'anna-baylis' ),
			'singular_name' => __( 'Reusable Section', 'anna-baylis' ),
			'add_new_item' => __( 'Add New Section', 'anna-baylis' ),
			'edit_item' => __( 'Edit Section', 'anna-baylis' ),
			'all_items' => __( 'All Sections', 'anna-baylis' ),
		),
		'public' => false, 'show_ui' => true, 'show_in_rest' => true,
		'supports' => array( 'title', 'editor', 'page-attributes' ),
		'menu_icon' => 'dashicons-layout', 'menu_position' => 32,
	) );
}
add_action( 'init', 'anna_register_reusable_sections_cpt' );
