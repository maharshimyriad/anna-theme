<?php
/**
 * CPT: FAQ
 * @package Anna_Baylis
 * @since   1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function anna_register_faq_cpt() {
	register_post_type( 'anna_faq', array(
		'labels' => array(
			'name' => __( 'FAQs', 'anna-baylis' ),
			'singular_name' => __( 'FAQ', 'anna-baylis' ),
			'add_new_item' => __( 'Add New FAQ', 'anna-baylis' ),
			'edit_item' => __( 'Edit FAQ', 'anna-baylis' ),
			'all_items' => __( 'All FAQs', 'anna-baylis' ),
		),
		'public' => false, 'show_ui' => true, 'show_in_rest' => true,
		'supports' => array( 'title', 'editor', 'page-attributes' ),
		'menu_icon' => 'dashicons-editor-help', 'menu_position' => 31,
	) );
}
add_action( 'init', 'anna_register_faq_cpt' );
