<?php
/**
 * Section blueprints for Anna Page Scaffolder.
 *
 * @package Anna_Page_Scaffolder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string, array<string, mixed>>
 */
function anna_scaffold_get_section_types() {
	if ( function_exists( 'anna_get_page_section_types' ) ) {
		return anna_get_page_section_types();
	}

	return array(
		'hero' => array(
			'label'    => __( 'Hero', 'anna-baylis' ),
			'template' => 'hero',
			'fields'   => array(
				'hero_eyebrow'       => array( 'type' => 'text', 'label' => 'Eyebrow', 'default' => 'Welcome' ),
				'hero_heading'       => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'Your page headline goes here.' ),
				'hero_body'          => array( 'type' => 'textarea', 'label' => 'Description', 'default' => 'Add a short supporting line for this page.' ),
				'hero_image_id'      => array( 'type' => 'media', 'label' => 'Background Image', 'default' => 0 ),
				'hero_button_text'   => array( 'type' => 'text', 'label' => 'Button Text', 'default' => 'Get in Touch' ),
				'hero_button_url'    => array( 'type' => 'url', 'label' => 'Button URL', 'default' => '#contact' ),
			),
		),
		'text-image' => array(
			'label'    => __( 'Text + Image (two column)', 'anna-baylis' ),
			'template' => 'text-image',
			'fields'   => array(
				'{id}_heading'         => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'Section heading' ),
				'{id}_body'            => array( 'type' => 'textarea', 'label' => 'Body', 'default' => "Replace this placeholder copy with your message.\n\nUse a blank line between paragraphs." ),
				'{id}_image_id'        => array( 'type' => 'media', 'label' => 'Image', 'default' => 0 ),
				'{id}_image_position'  => array( 'type' => 'select', 'label' => 'Image Position', 'default' => 'right', 'choices' => array( 'left' => 'Left', 'right' => 'Right' ) ),
			),
		),
		'cta' => array(
			'label'    => __( 'CTA', 'anna-baylis' ),
			'template' => 'cta',
			'fields'   => array(
				'cta_heading'               => array( 'type' => 'text', 'label' => 'Heading', 'default' => 'Ready to take the next step?' ),
				'cta_subheading'            => array( 'type' => 'text', 'label' => 'Subheading', 'default' => 'We would love to hear from you.' ),
				'cta_body'                  => array( 'type' => 'textarea', 'label' => 'Body', 'default' => 'Book a call or send a message to get started.' ),
				'cta_button_primary_text'   => array( 'type' => 'text', 'label' => 'Primary Button Text', 'default' => 'Book a Discovery Call' ),
				'cta_button_primary_url'    => array( 'type' => 'url', 'label' => 'Primary Button URL', 'default' => '#contact' ),
				'cta_button_secondary_text' => array( 'type' => 'text', 'label' => 'Secondary Button Text', 'default' => 'Learn More' ),
				'cta_button_secondary_url'  => array( 'type' => 'url', 'label' => 'Secondary Button URL', 'default' => '/' ),
			),
		),
	);
}

/**
 * Build resolved section list from user selection.
 *
 * @param array<int, string>       $section_types Section type keys.
 * @param array<int, string>|null  $text_image_ids Optional IDs for text-image sections.
 * @return array<int, array<string, mixed>>
 */
function anna_scaffold_resolve_sections( $section_types, $text_image_ids = null ) {
	if ( function_exists( 'anna_resolve_page_sections' ) ) {
		return anna_resolve_page_sections( $section_types, $text_image_ids );
	}

	$types     = anna_scaffold_get_section_types();
	$resolved  = array();
	$text_idx  = 0;
	$text_ids  = is_array( $text_image_ids ) && ! empty( $text_image_ids ) ? $text_image_ids : array( 'intro' );

	foreach ( $section_types as $type ) {
		if ( ! isset( $types[ $type ] ) ) {
			continue;
		}

		$section = $types[ $type ];
		$id      = $section['template'];

		if ( 'text-image' === $type ) {
			$id = sanitize_key( $text_ids[ $text_idx ] ?? ( 'split' . ( $text_idx + 1 ) ) );
			++$text_idx;
		}

		$fields = array();
		foreach ( $section['fields'] as $field_key => $field ) {
			$key = str_replace( '{id}', $id, $field_key );
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
 * Flatten all field keys/defaults for a page config.
 *
 * @param array<int, array<string, mixed>> $sections Resolved sections.
 * @param string                           $title    Page title for placeholders.
 * @return array<string, mixed>
 */
function anna_scaffold_build_default_content( $sections, $title ) {
	if ( function_exists( 'anna_build_page_section_default_content' ) ) {
		return anna_build_page_section_default_content( $sections, $title );
	}

	$content = array();

	foreach ( $sections as $section ) {
		foreach ( $section['fields'] as $key => $field ) {
			$default = $field['default'] ?? '';
			if ( is_string( $default ) ) {
				$default = str_replace( array( '{title}', '{page_title}' ), $title, $default );
			}
			if ( 'hero_heading' === $key && 'Your page headline goes here.' === $default ) {
				$default = $title;
			}
			if ( 'hero_eyebrow' === $key && 'Welcome' === $default ) {
				$default = $title;
			}
			$content[ $key ] = $default;
		}
	}

	return $content;
}
