<?php
/**
 * Taxonomy: FAQ Categories
 * @package Anna_Baylis
 * @since   1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function anna_register_faq_categories() {
	register_taxonomy( 'anna_faq_cat', 'anna_faq', array(
		'labels' => array(
			'name' => __( 'FAQ Categories', 'anna-baylis' ),
			'singular_name' => __( 'FAQ Category', 'anna-baylis' ),
		),
		'public' => false, 'hierarchical' => true, 'show_ui' => true, 'show_in_rest' => true,
		'show_admin_column' => true,
	) );
}
add_action( 'init', 'anna_register_faq_categories' );
