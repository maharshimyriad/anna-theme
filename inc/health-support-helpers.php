<?php
/**
 * Health Support page helpers.
 *
 * @package Anna_Baylis
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get Health Support page content.
 *
 * @return array<string, mixed>
 */
function anna_get_health_support_page_content() {
	$post_id = get_the_ID();
	if ( function_exists( 'anna_content_get_health_support_page_content' ) && $post_id ) {
		return anna_content_get_health_support_page_content( $post_id );
	}

	// Fallback if plugin is inactive
	$map = array(
		'hero_eyebrow'       => 'hs_pg_hero_eyebrow',
		'hero_heading'       => 'hs_pg_hero_heading',
		'hero_image_id'      => 'hs_pg_hero_image_id',
		'opening_heading'    => 'hs_pg_opening_heading',
		'opening_body'       => 'hs_pg_opening_body',
		'opening_image_id'   => 'hs_pg_opening_image_id',
		'programs_heading'   => 'hs_pg_programs_heading',
		'programs_body'      => 'hs_pg_programs_body',
		'inner_heading'      => 'hs_pg_inner_heading',
		'inner_body'         => 'hs_pg_inner_body',
		'inner_image_id'     => 'hs_pg_inner_image_id',
		'work_heading'       => 'hs_pg_work_heading',
		'work_body'          => 'hs_pg_work_body',
		'practice_heading'   => 'hs_pg_practice_heading',
		'practice_body'      => 'hs_pg_practice_body',
		'practice_link_text' => 'hs_pg_practice_link_text',
		'practice_link_url'  => 'hs_pg_practice_link_url',
		'cta_heading'        => 'hs_pg_cta_heading',
		'cta_body'           => 'hs_pg_cta_body',
		'cta_btn_1_text'     => 'hs_pg_cta_btn_1_text',
		'cta_btn_1_url'      => 'hs_pg_cta_btn_1_url',
		'cta_btn_2_text'     => 'hs_pg_cta_btn_2_text',
		'cta_btn_2_url'      => 'hs_pg_cta_btn_2_url',
		'cta_btn_3_text'     => 'hs_pg_cta_btn_3_text',
		'cta_btn_3_url'      => 'hs_pg_cta_btn_3_url',
	);

	$out = array();
	foreach ( $map as $plugin_key => $theme_key ) {
		$value = anna_get_option( $theme_key, '' );
		if ( str_ends_with( $plugin_key, '_image_id' ) ) {
			$out[ $plugin_key ] = absint( $value );
		} else {
			$out[ $plugin_key ] = $value;
		}
	}

	return $out;
}
