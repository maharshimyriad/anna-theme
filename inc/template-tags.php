<?php
/**
 * Template tags.
 *
 * Custom output helpers used in templates.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Print the section header component.
 *
 * @param array $args {
 *     @type string $eyebrow     Overline text.
 *     @type string $heading     Main heading.
 *     @type string $description Subtitle / description.
 *     @type string $align       'left', 'center'.
 *     @type string $heading_tag HTML tag for heading.
 * }
 */
function anna_section_header( $args = array() ) {
	$defaults = array(
		'eyebrow'     => '',
		'heading'     => '',
		'description' => '',
		'align'       => 'center',
		'heading_tag' => 'h2',
	);

	$args = wp_parse_args( $args, $defaults );
	$tag  = in_array( $args['heading_tag'], array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ), true ) ? $args['heading_tag'] : 'h2';

	$class = 'anna-section-header';
	if ( 'center' === $args['align'] ) {
		$class .= ' anna-text-center';
	}
	?>
	<header class="<?php echo esc_attr( $class ); ?>">
		<?php if ( $args['eyebrow'] ) : ?>
			<span class="anna-eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></span>
		<?php endif; ?>

		<?php if ( $args['heading'] ) : ?>
			<<?php echo esc_attr( $tag ); ?> class="anna-heading anna-heading--2">
				<?php echo wp_kses_post( $args['heading'] ); ?>
			</<?php echo esc_attr( $tag ); ?>>
		<?php endif; ?>

		<?php if ( $args['description'] ) : ?>
			<p class="anna-section-header__description"><?php echo esc_html( $args['description'] ); ?></p>
		<?php endif; ?>
	</header>
	<?php
}

/**
 * Render a responsive image with proper srcset/sizes.
 *
 * @param int    $attachment_id Attachment ID.
 * @param string $size          Image size.
 * @param string $class         CSS class.
 * @param bool   $lazy          Whether to lazy-load.
 */
function anna_responsive_image( $attachment_id, $size = 'large', $class = '', $lazy = true ) {
	if ( ! $attachment_id ) {
		return;
	}

	$attrs = array(
		'class'   => $class,
		'loading' => $lazy ? 'lazy' : 'eager',
		'decoding' => 'async',
	);

	echo wp_get_attachment_image( $attachment_id, $size, false, $attrs );
}

/**
 * Outputs a service icon SVG by slug.
 *
 * @param string $icon Icon slug identifier.
 */
function anna_service_icon( $icon = 'default' ) {
	$icons = array(
		'coaching' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>',
		'wellness' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>',
		'mindset'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>',
		'growth'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>',
		'workshop' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/></svg>',
		'community' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>',
		'default'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
	);

	$svg = $icons[ $icon ] ?? $icons['default'];
	echo wp_kses( $svg, anna_allowed_svg_tags() );
}
