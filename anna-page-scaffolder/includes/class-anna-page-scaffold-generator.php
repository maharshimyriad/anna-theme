<?php
/**
 * Generates theme files for scaffolded pages.
 *
 * @package Anna_Page_Scaffolder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Page scaffold file generator.
 */
final class Anna_Page_Scaffold_Generator {

	/**
	 * @var string
	 */
	private $theme_dir;

	/**
	 * @param string $theme_dir Absolute theme directory.
	 */
	public function __construct( $theme_dir = '' ) {
		$this->theme_dir = $theme_dir ? $theme_dir : get_template_directory();
	}

	/**
	 * @param string $slug  URL slug.
	 * @param string $title Page title.
	 * @param string $code  PHP code prefix.
	 * @return array{success:bool,message:string,config?:array,files?:array}
	 */
	public function generate( $slug, $title, $code ) {
		$slug  = sanitize_title( $slug );
		$code  = sanitize_key( $code );
		$title = sanitize_text_field( $title );

		if ( ! $slug || ! $code || ! $title ) {
			return array( 'success' => false, 'message' => __( 'Slug, code prefix, and title are required.', 'anna-baylis' ) );
		}

		if ( ! preg_match( '/^[a-z][a-z0-9_]*$/', $code ) ) {
			return array( 'success' => false, 'message' => __( 'Code prefix must start with a letter and use only lowercase letters, numbers, and underscores.', 'anna-baylis' ) );
		}

		foreach ( anna_get_scaffolded_pages() as $existing ) {
			if ( ( $existing['slug'] ?? '' ) === $slug || ( $existing['code'] ?? '' ) === $code ) {
				return array( 'success' => false, 'message' => __( 'A scaffolded page with this slug or code already exists.', 'anna-baylis' ) );
			}
		}

		$reserved = array( 'coaching', 'oasis', 'speaking', 'mhs', 'move', 'about', 'home' );
		if ( in_array( $code, $reserved, true ) || in_array( $slug, array( 'coaching', 'oasis', 'speaking', 'about' ), true ) ) {
			return array( 'success' => false, 'message' => __( 'This slug or code is reserved by an existing theme page.', 'anna-baylis' ) );
		}

		$config = array(
			'slug'          => $slug,
			'title'         => $title,
			'code'          => $code,
			'tab_id'        => $code . '_page',
			'tab_label'     => $title . ' ' . __( 'Page', 'anna-baylis' ),
			'option_prefix' => $code . '_pg_',
			'template_file' => 'page-' . $slug . '.php',
			'css_slug'      => $slug,
			'css_class'     => 'anna-' . $slug . '-page',
			'query_var'     => 'anna_' . $code . '_page_content',
			'created'       => time(),
		);

		$files  = array();
		$writes = array(
			$this->theme_dir . '/page-' . $slug . '.php' => $this->build_page_template( $config ),
			$this->theme_dir . '/template-parts/pages/' . $slug . '/index.php' => $this->build_index_partial( $config ),
			$this->theme_dir . '/assets/css/pages/' . $slug . '.css' => $this->build_css( $config ),
			$this->theme_dir . '/inc/' . $code . '-helpers.php' => $this->build_helpers( $config ),
			$this->theme_dir . '/inc/admin/' . $code . '-settings-fields.php' => $this->build_settings_fields( $config ),
		);

		foreach ( $writes as $path => $content ) {
			if ( file_exists( $path ) ) {
				return array(
					'success' => false,
					'message' => sprintf(
						/* translators: %s: file path */
						__( 'File already exists: %s. Remove it first or use a different slug.', 'anna-baylis' ),
						$path
					),
				);
			}

			$dir = dirname( $path );
			if ( ! is_dir( $dir ) && ! wp_mkdir_p( $dir ) ) {
				return array( 'success' => false, 'message' => __( 'Could not create directory.', 'anna-baylis' ) . ' ' . $dir );
			}

			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
			if ( false === file_put_contents( $path, $content ) ) {
				return array( 'success' => false, 'message' => __( 'Failed to write file.', 'anna-baylis' ) . ' ' . $path );
			}

			$files[] = str_replace( $this->theme_dir . '/', '', $path );
		}

		$pages   = anna_get_scaffolded_pages();
		$pages[] = $config;
		update_option( 'anna_scaffolded_pages', $pages, false );

		if ( function_exists( 'anna_bootstrap_scaffolded_pages' ) ) {
			anna_bootstrap_scaffolded_pages();
		}

		return array(
			'success' => true,
			'message' => __( 'Page scaffold created successfully.', 'anna-baylis' ),
			'config'  => $config,
			'files'   => $files,
		);
	}

	/**
	 * @param array<string, mixed> $config Config.
	 * @return string
	 */
	private function build_page_template( $config ) {
		$title     = $config['title'];
		$slug      = $config['slug'];
		$css_class = $config['css_class'];

		return <<<PHP
<?php
/**
 * Template Name: {$title} Page
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main-content" class="anna-main {$css_class}-main" role="main">
	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/pages/{$slug}/index' );
	endwhile;
	?>
</main>

<?php
get_footer();

PHP;
	}

	/**
	 * @param array<string, mixed> $config Config.
	 * @return string
	 */
	private function build_index_partial( $config ) {
		$title = $config['title'];
		$slug  = $config['slug'];

		return <<<PHP
<?php
/**
 * Template part: {$title} page section loader.
 *
 * Add your section includes here, e.g.:
 *   get_template_part( 'template-parts/pages/{$slug}/hero' );
 *   get_template_part( 'template-parts/pages/{$slug}/intro' );
 *   get_template_part( 'template-parts/pages/{$slug}/cta' );
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

PHP;
	}

	/**
	 * @param array<string, mixed> $config Config.
	 * @return string
	 */
	private function build_css( $config ) {
		$css  = $config['css_class'];
		$slug = $config['slug'];
		$title = $config['title'];

		return <<<CSS
/**
 * {$title} page styles (scaffolded).
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

.{$css}-main {
  background: var(--color-white);
}

CSS;
	}


	/**
	 * @param array<string, mixed> $config Config.
	 * @return string
	 */
	private function build_helpers( $config ) {
		$code  = $config['code'];
		$title = $config['title'];

		return <<<PHP
<?php
/**
 * {$title} page helpers (scaffolded).
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string, mixed>
 */
function anna_get_{$code}_default_content() {
	return array();
}

/**
 * @return array<string, mixed>
 */
function anna_get_{$code}_theme_option_defaults() {
	return array();
}

/**
 * @return array<string, string>
 */
function anna_get_{$code}_page_option_map() {
	return array();
}

/**
 * @return array<string, mixed>
 */
function anna_get_{$code}_page_content() {
	return apply_filters( 'anna_{$code}_page_content', array() );
}

PHP;
	}

	/**
	 * @param array<string, mixed> $config Config.
	 * @return string
	 */
	private function build_settings_fields( $config ) {
		$code  = $config['code'];
		$title = $config['title'];

		return <<<PHP
<?php
/**
 * {$title} page theme settings fields (scaffolded).
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render {$title} settings tab.
 */
function anna_render_{$code}_page_settings_fields() {
	// Add theme settings fields for this page here.
}

PHP;
	}
}
