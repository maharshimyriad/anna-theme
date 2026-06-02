<?php
/**
 * Move page helpers.
 *
 * @package Anna_Baylis
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get Move page content.
 *
 * @return array<string, mixed>
 */
function anna_get_move_page_content() {
	$post_id = get_the_ID();
	if ( function_exists( 'anna_content_get_move_page_content' ) && $post_id ) {
		return anna_content_get_move_page_content( $post_id );
	}

	// Fallback if plugin is inactive
	$map = array(
		'hero_eyebrow'       => 'move_pg_hero_eyebrow',
		'hero_heading'       => 'move_pg_hero_heading',
		'hero_image_id'      => 'move_pg_hero_image_id',
		'evo_heading'        => 'move_pg_evo_heading',
		'evo_body'           => 'move_pg_evo_body',
		'gallery_heading'    => 'move_pg_gallery_heading',
		'gallery_image_1_id' => 'move_pg_gallery_image_1_id',
		'gallery_image_2_id' => 'move_pg_gallery_image_2_id',
		'gallery_image_3_id' => 'move_pg_gallery_image_3_id',
		'gallery_image_4_id' => 'move_pg_gallery_image_4_id',
		'gallery_image_5_id' => 'move_pg_gallery_image_5_id',
		'what_heading'       => 'move_pg_what_heading',
		'what_body'          => 'move_pg_what_body',
		'quote_1'            => 'move_pg_quote_1',
		'quote_2'            => 'move_pg_quote_2',
		'quote_3'            => 'move_pg_quote_3',
		'quote_4'            => 'move_pg_quote_4',
		'pillars_heading'    => 'move_pg_pillars_heading',
		'pillar_1_title'     => 'move_pg_pillar_1_title',
		'pillar_1_body'      => 'move_pg_pillar_1_body',
		'pillar_2_title'     => 'move_pg_pillar_2_title',
		'pillar_2_body'      => 'move_pg_pillar_2_body',
		'pillar_3_title'     => 'move_pg_pillar_3_title',
		'pillar_3_body'      => 'move_pg_pillar_3_body',
		'pillar_4_title'     => 'move_pg_pillar_4_title',
		'pillar_4_body'      => 'move_pg_pillar_4_body',
		'cta_heading'        => 'move_pg_cta_heading',
		'cta_body'           => 'move_pg_cta_body',
		'cta_btn_1_text'     => 'move_pg_cta_btn_1_text',
		'cta_btn_1_url'      => 'move_pg_cta_btn_1_url',
		'cta_btn_2_text'     => 'move_pg_cta_btn_2_text',
		'cta_btn_2_url'      => 'move_pg_cta_btn_2_url',
		'cta_btn_3_text'     => 'move_pg_cta_btn_3_text',
		'cta_btn_3_url'      => 'move_pg_cta_btn_3_url',
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
