<?php
/**
 * Registry for scaffolded Anna theme pages.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<int, array<string, mixed>>
 */
function anna_get_scaffolded_pages() {
	$pages = get_option( 'anna_scaffolded_pages', array() );
	return is_array( $pages ) ? $pages : array();
}

/**
 * @param string $code Page code prefix.
 * @return array<string, mixed>|null
 */
function anna_get_scaffolded_page( $code ) {
	$code = sanitize_key( $code );
	foreach ( anna_get_scaffolded_pages() as $page ) {
		if ( ( $page['code'] ?? '' ) === $code ) {
			return $page;
		}
	}
	return null;
}

/**
 * Load helper and admin field files for scaffolded pages.
 */
function anna_bootstrap_scaffolded_pages() {
	foreach ( anna_get_scaffolded_pages() as $page ) {
		$code = $page['code'] ?? '';
		if ( ! $code ) {
			continue;
		}

		$helpers = ANNA_DIR . '/inc/' . $code . '-helpers.php';
		if ( is_readable( $helpers ) ) {
			require_once $helpers;
		}

		$settings = ANNA_DIR . '/inc/admin/' . $code . '-settings-fields.php';
		if ( is_readable( $settings ) ) {
			require_once $settings;
		}
	}
}
add_action( 'after_setup_theme', 'anna_bootstrap_scaffolded_pages', 5 );

/**
 * Merge scaffolded tabs into Anna Theme settings.
 *
 * @param array<string, string> $tabs Existing tabs.
 * @return array<string, string>
 */
// function anna_filter_scaffolded_settings_tabs( $tabs ) {
// 	foreach ( anna_get_scaffolded_pages() as $page ) {
// 		$tab_id = $page['tab_id'] ?? '';
// 		$label  = $page['tab_label'] ?? '';
// 		if ( $tab_id && $label ) {
// 			$tabs[ $tab_id ] = $label;
// 		}
// 	}
// 	return $tabs;
// }
// add_filter( 'anna_settings_tabs', 'anna_filter_scaffolded_settings_tabs' );

/**
 * Merge scaffolded default options.
 *
 * @param array<string, mixed> $defaults Theme defaults.
 * @return array<string, mixed>
 */
function anna_filter_scaffolded_default_options( $defaults ) {
	foreach ( anna_get_scaffolded_pages() as $page ) {
		$fn = 'anna_get_' . ( $page['code'] ?? '' ) . '_theme_option_defaults';
		if ( is_string( $page['code'] ?? '' ) && function_exists( $fn ) ) {
			$defaults = array_merge( $defaults, $fn() );
		}
	}
	return $defaults;
}
add_filter( 'anna_default_options', 'anna_filter_scaffolded_default_options' );

/**
 * Register sanitize tab keys for scaffolded pages.
 *
 * @param array<string, array<int, string>> $tab_keys Tab field keys.
 * @return array<string, array<int, string>>
 */
function anna_filter_scaffolded_settings_tab_keys( $tab_keys ) {
	foreach ( anna_get_scaffolded_pages() as $page ) {
		$tab_id = $page['tab_id'] ?? '';
		$fn     = 'anna_get_' . ( $page['code'] ?? '' ) . '_theme_option_defaults';
		if ( $tab_id && function_exists( $fn ) ) {
			$tab_keys[ $tab_id ] = array_keys( $fn() );
		}
	}
	return $tab_keys;
}
add_filter( 'anna_settings_tab_keys', 'anna_filter_scaffolded_settings_tab_keys' );

/**
 * Seed and ensure scaffolded WordPress pages exist.
 */
function anna_scaffolded_pages_admin_init() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	foreach ( anna_get_scaffolded_pages() as $page ) {
		anna_seed_scaffolded_page_theme_defaults( $page );
	}
}
add_action( 'admin_init', 'anna_scaffolded_pages_admin_init', 20 );

/**
 * @param string $tab_id Settings tab ID.
 * @return bool
 */
function anna_is_scaffold_settings_tab( $tab_id ) {
	foreach ( anna_get_scaffolded_pages() as $page ) {
		if ( ( $page['tab_id'] ?? '' ) === $tab_id ) {
			return true;
		}
	}
	return false;
}

/**
 * @param string $tab_id Settings tab ID.
 */
function anna_render_scaffold_settings_tab( $tab_id ) {
	foreach ( anna_get_scaffolded_pages() as $page ) {
		if ( ( $page['tab_id'] ?? '' ) !== $tab_id ) {
			continue;
		}
		$code      = $page['code'] ?? '';
		$render_fn = 'anna_render_' . $code . '_page_settings_fields';
		if ( $code && function_exists( $render_fn ) ) {
			$render_fn();
		}
		break;
	}
}

/**
 * @param array<string, mixed> $page Page config.
 */
function anna_seed_scaffolded_page_theme_defaults( $page ) {
	$prefix = $page['option_prefix'] ?? '';
	if ( ! $prefix || ! function_exists( 'anna_get_default_options' ) ) {
		return;
	}

	$defaults = anna_get_default_options();
	$options  = get_option( 'anna_theme_options', array() );
	if ( ! is_array( $options ) ) {
		$options = array();
	}

	$page_keys = array_keys(
		array_filter(
			$defaults,
			static function ( $key ) use ( $prefix ) {
				return str_starts_with( (string) $key, $prefix );
			},
			ARRAY_FILTER_USE_KEY
		)
	);

	$changed = false;
	foreach ( $page_keys as $key ) {
		$has_value = false;
		if ( isset( $options[ $key ] ) ) {
			$has_value = is_array( $options[ $key ] ) ? ! empty( $options[ $key ] ) : '' !== trim( (string) $options[ $key ] );
		}
		$default_has_value = isset( $defaults[ $key ] ) && ( is_array( $defaults[ $key ] ) ? ! empty( $defaults[ $key ] ) : '' !== (string) $defaults[ $key ] );

		if ( ! $has_value && $default_has_value ) {
			$options[ $key ] = $defaults[ $key ];
			$changed         = true;
		}
	}

	if ( $changed ) {
		update_option( 'anna_theme_options', $options );
	}
}
