<?php
/**
 * Reusable page section definitions (hero, text+image, CTA).
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string, array<string, mixed>>
 */
function anna_get_page_section_types() {
	$types = array(
		'hero'       => array(
			'label'    => __( 'Hero', 'anna-baylis' ),
			'template' => 'hero',
			'fields'   => array(
				'hero_eyebrow'     => array( 'type' => 'text', 'label' => 'Eyebrow', 'default' => 'Welcome' ),
				'hero_heading'     => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'Your page headline goes here.' ),
				'hero_body'        => array( 'type' => 'textarea', 'label' => 'Description', 'default' => 'Add a short supporting line for this page.' ),
				'hero_image_id'    => array( 'type' => 'media', 'label' => 'Background Image', 'default' => 0 ),
				'hero_button_text' => array( 'type' => 'text', 'label' => 'Button Text', 'default' => 'Get in Touch' ),
				'hero_button_url'  => array( 'type' => 'url', 'label' => 'Button URL', 'default' => '#contact' ),
			),
		),
		'text-image' => array(
			'label'    => __( 'Text + Image (two column)', 'anna-baylis' ),
			'template' => 'text-image',
			'fields'   => array(
				'{id}_heading'        => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'Section heading' ),
				'{id}_body'           => array( 'type' => 'textarea', 'label' => 'Body', 'default' => "Replace this placeholder copy with your message.\n\nUse a blank line between paragraphs." ),
				'{id}_image_id'       => array( 'type' => 'media', 'label' => 'Image', 'default' => 0 ),
				'{id}_image_position' => array( 'type' => 'select', 'label' => 'Image Position', 'default' => 'right', 'choices' => array( 'left' => 'Left', 'right' => 'Right' ) ),
			),
		),
		'cta'        => array(
			'label'    => __( 'CTA', 'anna-baylis' ),
			'template' => 'cta',
			'fields'   => array(
				'cta_heading'                 => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'Ready to take the next step?' ),
				'cta_subheading'              => array( 'type' => 'text', 'label' => 'Subheading', 'default' => 'We would love to hear from you.' ),
				'cta_body'                    => array( 'type' => 'textarea', 'label' => 'Body', 'default' => 'Book a call or send a message to get started.' ),
				'cta_button_primary_text'     => array( 'type' => 'text', 'label' => 'Primary Button Text', 'default' => 'Book a Discovery Call' ),
				'cta_button_primary_url'      => array( 'type' => 'url', 'label' => 'Primary Button URL', 'default' => '#contact' ),
				'cta_button_secondary_text'   => array( 'type' => 'text', 'label' => 'Secondary Button Text', 'default' => 'Learn More' ),
				'cta_button_secondary_url'    => array( 'type' => 'url', 'label' => 'Secondary Button URL', 'default' => '/' ),
			),
		),
	);

	return apply_filters( 'anna_page_section_types', $types );
}

/**
 * @param array<int, string>      $section_types Types in order.
 * @param array<int, string>|null $text_image_ids IDs for text-image blocks.
 * @return array<int, array<string, mixed>>
 */
function anna_resolve_page_sections( $section_types, $text_image_ids = null ) {
	$types    = anna_get_page_section_types();
	$resolved = array();
	$text_idx = 0;
	$text_ids = is_array( $text_image_ids ) && ! empty( $text_image_ids ) ? $text_image_ids : array();

	foreach ( $section_types as $type ) {
		if ( ! isset( $types[ $type ] ) ) {
			continue;
		}

		$section = $types[ $type ];
		$id      = $section['template'];

		if ( 'text-image' === $type ) {
			$id = sanitize_key( $text_ids[ $text_idx ] ?? ( 'block' . ( $text_idx + 1 ) ) );
			++$text_idx;
		}

		$fields = array();
		foreach ( $section['fields'] as $field_key => $field ) {
			$key            = str_replace( '{id}', $id, $field_key );
			$fields[ $key ] = $field;
		}

		$resolved[] = array(
			'type'     => $type,
			'id'       => $id,
			'template' => $section['template'],
			'label'    => $section['label'],
			'fields'   => $fields,
		);
	}

	return $resolved;
}

/**
 * @param array<int, array<string, mixed>> $sections Sections.
 * @param string                           $title    Page title.
 * @return array<string, mixed>
 */
function anna_build_page_section_default_content( $sections, $title = '' ) {
	$content = array();

	foreach ( $sections as $section ) {
		foreach ( $section['fields'] as $key => $field ) {
			$default = $field['default'] ?? '';
			if ( is_string( $default ) && $title ) {
				$default = str_replace( array( '{title}', '{page_title}' ), $title, $default );
			}
			if ( 'hero_heading' === $key && 'Your page headline goes here.' === $default && $title ) {
				$default = $title;
			}
			$content[ $key ] = $default;
		}
	}

	return $content;
}

/**
 * Build section field definitions from layout rows.
 *
 * @param array<int, array{type:string,id:string}> $layout Layout.
 * @return array<int, array<string, mixed>>
 */
function anna_sections_from_layout( $layout ) {
	$types    = anna_get_page_section_types();
	$sections = array();

	foreach ( $layout as $row ) {
		$type = $row['type'] ?? '';
		$id   = sanitize_key( $row['id'] ?? '' );
		if ( ! isset( $types[ $type ] ) || ! $id ) {
			continue;
		}

		$def = $types[ $type ];
		if ( 'text-image' !== $type && $id !== $def['template'] ) {
			$id = $def['template'];
		}

		$fields = array();
		foreach ( $def['fields'] as $field_key => $field ) {
			$key            = str_replace( '{id}', $id, $field_key );
			$fields[ $key ] = $field;
		}

		$sections[] = array(
			'type'     => $type,
			'id'       => $id,
			'template' => $def['template'],
			'label'    => $def['label'],
			'fields'   => $fields,
		);
	}

	return $sections;
}
