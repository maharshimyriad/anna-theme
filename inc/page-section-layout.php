<?php
/**
 * Flexible page section layout (render + storage).
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param int|null $post_id Post ID.
 * @return array<string, mixed>|null
 */
function anna_get_flexible_page_config( $post_id = null ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	if ( ! $post_id ) {
		return null;
	}

	$post = get_post( $post_id );
	if ( ! $post || 'page' !== $post->post_type ) {
		return null;
	}

	$template = get_page_template_slug( $post_id );
	if ( ! $template || ! str_starts_with( $template, 'page-' ) || ! str_ends_with( $template, '.php' ) ) {
		return null;
	}

	$slug = str_replace( array( 'page-', '.php' ), '', $template );

	if ( function_exists( 'anna_get_scaffolded_pages' ) ) {
		foreach ( anna_get_scaffolded_pages() as $page ) {
			if ( ( $page['template_file'] ?? '' ) === $template || ( $page['slug'] ?? '' ) === $post->post_name ) {
				return $page;
			}
		}
	}

	$code = sanitize_key( str_replace( '-', '_', $slug ) );
	if ( ! $code ) {
		return null;
	}

	$template_file = ANNA_DIR . '/' . $template;
	if ( ! file_exists( $template_file ) ) {
		return null;
	}

	$parts_dir  = ANNA_DIR . '/template-parts/pages/' . $slug;
	$helpers    = ANNA_DIR . '/inc/' . $code . '-helpers.php';
	$has_parts  = is_dir( $parts_dir );
	$has_helper = file_exists( $helpers );

	if ( ! $has_parts && ! $has_helper ) {
		return null;
	}

	return array(
		'slug'          => $slug,
		'title'         => get_the_title( $post_id ),
		'code'          => $code,
		'template_file' => $template,
		'css_slug'      => $slug,
		'css_class'     => 'anna-' . $slug . '-page',
		'query_var'     => 'anna_' . $code . '_page_content',
	);
}

/**
 * @param int|null $post_id Post ID.
 * @return bool
 */
function anna_is_flexible_page_template( $post_id = null ) {
	return null !== anna_get_flexible_page_config( $post_id );
}

/**
 * Default layout from scaffold registry config.
 *
 * @param array<string, mixed> $config Page config.
 * @return array<int, array{type:string,id:string}>
 */
function anna_get_default_section_layout_from_config( $config ) {
	$layout = array();

	if ( ! empty( $config['section_layout'] ) && is_array( $config['section_layout'] ) ) {
		return $config['section_layout'];
	}

	foreach ( $config['sections'] ?? array() as $section ) {
		$layout[] = array(
			'type' => $section['type'] ?? '',
			'id'   => $section['id'] ?? '',
		);
	}

	if ( empty( $layout ) && ! empty( $config['section_files'] ) ) {
		foreach ( $config['section_files'] as $file ) {
			$file = (string) $file;
			if ( 'hero' === $file ) {
				$layout[] = array( 'type' => 'hero', 'id' => 'hero' );
			} elseif ( 'cta' === $file ) {
				$layout[] = array( 'type' => 'cta', 'id' => 'cta' );
			} else {
				$layout[] = array( 'type' => 'text-image', 'id' => sanitize_key( $file ) );
			}
		}
	}

	return $layout;
}

/**
 * @param int|null $post_id Post ID.
 * @return array<int, array{type:string,id:string}>
 */
function anna_get_page_section_layout( $post_id = null ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	$config  = anna_get_flexible_page_config( $post_id );

	if ( ! $config ) {
		return array();
	}

	$stored = get_post_meta( $post_id, '_anna_page_section_layout', true );
	if ( is_array( $stored ) && ! empty( $stored ) ) {
		$layout = array();
		foreach ( $stored as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$type = sanitize_text_field( $row['type'] ?? '' );
			$id   = sanitize_key( $row['id'] ?? '' );
			if ( $type && $id ) {
				$layout[] = array( 'type' => $type, 'id' => $id );
			}
		}
		if ( ! empty( $layout ) ) {
			return $layout;
		}
	}

	return anna_get_default_section_layout_from_config( $config );
}

/**
 * @param int|null $post_id Post ID.
 * @return array<int, array<string, mixed>>
 */
function anna_get_page_sections_for_post( $post_id = null ) {
	return anna_sections_from_layout( anna_get_page_section_layout( $post_id ) );
}

/**
 * Render all sections for the current flexible page.
 *
 * @param int|null $post_id Post ID.
 */
function anna_render_flexible_page_sections( $post_id = null ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	$config  = anna_get_flexible_page_config( $post_id );

	if ( ! $config ) {
		return;
	}

	$code    = $config['code'] ?? '';
	$slug    = $config['slug'] ?? '';
	$fn      = 'anna_get_' . $code . '_page_content';
	$content = function_exists( $fn ) ? $fn() : array();

	set_query_var( $config['query_var'] ?? ( 'anna_' . $code . '_page_content' ), $content );
	set_query_var( 'anna_flexible_page_config', $config );

	$layout = anna_get_page_section_layout( $post_id );

	foreach ( $layout as $row ) {
		anna_render_flexible_page_section( $row, $config, $content );
	}
}

/**
 * @param array<string, string>  $row     Layout row.
 * @param array<string, mixed>   $config  Page config.
 * @param array<string, mixed>   $content Page content.
 */
function anna_render_flexible_page_section( $row, $config, $content ) {
	$type = $row['type'] ?? '';
	$id   = $row['id'] ?? '';
	$slug = $config['slug'] ?? '';

	if ( ! $type || ! $id || ! $slug ) {
		return;
	}

	set_query_var( 'anna_section_id', $id );
	set_query_var( 'anna_section_type', $type );

	$shared = 'template-parts/sections/flexible/' . $type;
	if ( locate_template( $shared . '.php' ) ) {
		get_template_part( 'template-parts/sections/flexible/' . $type );
		return;
	}

	$legacy = 'template-parts/pages/' . $slug . '/' . ( 'text-image' === $type ? $id : $type );
	if ( locate_template( $legacy . '.php' ) ) {
		get_template_part( $legacy );
	}
}

/**
 * Normalize layout from POST.
 *
 * @param mixed $raw Raw input.
 * @return array<int, array{type:string,id:string}>
 */
function anna_normalize_page_section_layout( $raw ) {
	if ( ! is_array( $raw ) ) {
		return array();
	}

	$types  = anna_get_page_section_types();
	$layout = array();
	$blocks = 0;

	foreach ( $raw as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$type = sanitize_text_field( $row['type'] ?? '' );
		if ( ! isset( $types[ $type ] ) ) {
			continue;
		}

		$id = sanitize_key( $row['id'] ?? '' );
		if ( 'text-image' === $type ) {
			if ( ! $id ) {
				++$blocks;
				$id = 'block' . $blocks;
			}
		} else {
			$id = $types[ $type ]['template'];
		}

		$layout[] = array( 'type' => $type, 'id' => $id );
	}

	return $layout;
}
