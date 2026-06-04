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
	 * @param string               $slug           URL slug.
	 * @param string               $title          Page title.
	 * @param string               $code           PHP code prefix.
	 * @param array<int, string>   $section_types  Section types to include.
	 * @return array{success:bool,message:string,config?:array,files?:array}
	 */
	public function generate( $slug, $title, $code, $section_types ) {
		$slug = sanitize_title( $slug );
		$code = sanitize_key( $code );
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

		$sections = anna_scaffold_resolve_sections( $section_types );
		if ( empty( $sections ) ) {
			return array( 'success' => false, 'message' => __( 'Select at least one section.', 'anna-baylis' ) );
		}

		// $section_layout = array();
		// foreach ( $sections as $section ) {
		// 	$section_layout[] = array(
		// 		'type' => $section['type'],
		// 		'id'   => $section['id'],
		// 	);
		// }

		$config = array(
			'slug'            => $slug,
			'title'           => $title,
			'code'            => $code,
			'tab_id'          => $code . '_page',
			'tab_label'       => $title . ' ' . __( 'Page', 'anna-baylis' ),
			'option_prefix'   => $code . '_pg_',
			'template_file'   => 'page-' . $slug . '.php',
			'css_slug'        => $slug,
			'css_class'       => 'anna-' . $slug . '-page',
			'query_var'       => 'anna_' . $code . '_page_content',
			'sections'        => $sections,
			'section_layout'  => $section_layout,
			'section_files'   => array_map(
				static function ( $section ) {
					return $section['id'];
				},
				$sections
			),
			'created'         => time(),
		);

		$files = array();
		$writes = array(
			$this->theme_dir . '/page-' . $slug . '.php' => $this->build_page_template( $config ),
			$this->theme_dir . '/template-parts/pages/' . $slug . '/index.php' => $this->build_index_partial( $config ),
			$this->theme_dir . '/assets/css/pages/' . $slug . '.css' => $this->build_css( $config ),
			$this->theme_dir . '/inc/' . $code . '-helpers.php' => $this->build_helpers( $config ),
			$this->theme_dir . '/inc/admin/' . $code . '-settings-fields.php' => $this->build_settings_fields( $config ),
		);

		foreach ( $sections as $section ) {
			$file = $section['id'] . '.php';
			if ( 'text-image' === $section['type'] ) {
				$writes[ $this->theme_dir . '/template-parts/pages/' . $slug . '/' . $file ] = $this->build_text_image_partial( $config, $section );
			} elseif ( 'hero' === $section['type'] ) {
				$writes[ $this->theme_dir . '/template-parts/pages/' . $slug . '/hero.php' ] = $this->build_hero_partial( $config );
			} elseif ( 'cta' === $section['type'] ) {
				$writes[ $this->theme_dir . '/template-parts/pages/' . $slug . '/cta.php' ] = $this->build_cta_partial( $config );
			}
		}

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

		return <<<PHP
<?php
/**
 * Template part: {$title} page section loader.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_template_part( 'template-parts/pages/flexible-loader' );

PHP;
	}

	/**
	 * @param array<string, mixed> $config Config.
	 * @return string
	 */
	private function build_hero_partial( $config ) {
		$css   = $config['css_class'];
		$code  = $config['code'];
		$qvar  = $config['query_var'];
		$fn    = 'anna_get_' . $code . '_page_content';

		return <<<PHP
<?php
/**
 * Hero section.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

\$content = get_query_var( '{$qvar}', array() );
if ( empty( \$content ) ) {
	\$content = {$fn}();
}

\$has_image = ! empty( \$content['hero_image_id'] );
?>

<section
	class="{$css}-hero<?php echo \$has_image ? ' {$css}-hero--has-image' : ''; ?>"
	<?php if ( \$has_image ) : ?>
		style="background-image:url('<?php echo esc_url( anna_responsive_image_url( absint( \$content['hero_image_id'] ), 'full' ) ); ?>');"
	<?php endif; ?>
>
	<div class="{$css}-hero__overlay" aria-hidden="true"></div>
	<div class="anna-container anna-container--max">
		<div class="{$css}-hero__content">
			<?php if ( ! empty( \$content['hero_eyebrow'] ) ) : ?>
				<p class="{$css}-hero__eyebrow"><?php echo esc_html( \$content['hero_eyebrow'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( \$content['hero_heading'] ) ) : ?>
				<h1 class="{$css}-hero__heading"><?php echo esc_html( \$content['hero_heading'] ); ?></h1>
			<?php endif; ?>

			<?php if ( ! empty( \$content['hero_body'] ) ) : ?>
				<p class="{$css}-hero__body"><?php echo esc_html( \$content['hero_body'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( \$content['hero_button_text'] ) && ! empty( \$content['hero_button_url'] ) ) : ?>
				<a class="anna-btn {$css}-hero__btn" href="<?php echo esc_url( \$content['hero_button_url'] ); ?>">
					<?php echo esc_html( \$content['hero_button_text'] ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>

PHP;
	}

	/**
	 * @param array<string, mixed> $config  Config.
	 * @param array<string, mixed> $section Section.
	 * @return string
	 */
	private function build_text_image_partial( $config, $section ) {
		$css    = $config['css_class'];
		$code   = $config['code'];
		$qvar   = $config['query_var'];
		$fn     = 'anna_get_' . $code . '_page_content';
		$id     = $section['id'];
		$prefix = $id;

		return <<<PHP
<?php
/**
 * Text + image section ({$id}).
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

\$content = get_query_var( '{$qvar}', array() );
if ( empty( \$content ) ) {
	\$content = {$fn}();
}

\$image_id  = absint( \$content['{$prefix}_image_id'] ?? 0 );
\$position  = (string) ( \$content['{$prefix}_image_position'] ?? 'right' );
\$modifier  = 'left' === \$position ? ' {$css}-split--image-left' : ' {$css}-split--image-right';
?>

<section class="{$css}-section {$css}-split<?php echo esc_attr( \$modifier ); ?>">
	<div class="anna-container anna-container--max {$css}-split__inner">
		<div class="{$css}-split__content">
			<?php if ( ! empty( \$content['{$prefix}_heading'] ) ) : ?>
				<h2 class="{$css}__heading"><?php echo esc_html( \$content['{$prefix}_heading'] ); ?></h2>
			<?php endif; ?>

			<?php if ( ! empty( \$content['{$prefix}_body'] ) ) : ?>
				<div class="{$css}__copy"><?php echo wp_kses_post( wpautop( (string) \$content['{$prefix}_body'] ) ); ?></div>
			<?php endif; ?>
		</div>

		<?php if ( \$image_id ) : ?>
			<figure class="{$css}-split__media">
				<?php echo wp_get_attachment_image( \$image_id, 'large', false, array( 'class' => '{$css}-split__image' ) ); ?>
			</figure>
		<?php endif; ?>
	</div>
</section>

PHP;
	}

	/**
	 * @param array<string, mixed> $config Config.
	 * @return string
	 */
	private function build_cta_partial( $config ) {
		$css  = $config['css_class'];
		$code = $config['code'];
		$qvar = $config['query_var'];
		$fn   = 'anna_get_' . $code . '_page_content';

		return <<<PHP
<?php
/**
 * CTA section.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

\$content = get_query_var( '{$qvar}', array() );
if ( empty( \$content ) ) {
	\$content = {$fn}();
}
?>

<section class="{$css}-section {$css}-section--cream {$css}-cta">
	<div class="anna-container anna-container--max {$css}-cta__inner">
		<?php if ( ! empty( \$content['cta_heading'] ) ) : ?>
			<h2 class="{$css}-cta__heading"><?php echo esc_html( \$content['cta_heading'] ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( \$content['cta_subheading'] ) ) : ?>
			<p class="{$css}-cta__subheading"><?php echo esc_html( \$content['cta_subheading'] ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( \$content['cta_body'] ) ) : ?>
			<p class="{$css}-cta__body"><?php echo esc_html( \$content['cta_body'] ); ?></p>
		<?php endif; ?>

		<div class="{$css}-cta__actions">
			<?php if ( ! empty( \$content['cta_button_primary_text'] ) && ! empty( \$content['cta_button_primary_url'] ) ) : ?>
				<a class="anna-btn {$css}-cta__btn {$css}-cta__btn--primary" href="<?php echo esc_url( \$content['cta_button_primary_url'] ); ?>">
					<?php echo esc_html( \$content['cta_button_primary_text'] ); ?>
				</a>
			<?php endif; ?>

			<?php if ( ! empty( \$content['cta_button_secondary_text'] ) && ! empty( \$content['cta_button_secondary_url'] ) ) : ?>
				<a class="anna-btn {$css}-cta__btn {$css}-cta__btn--outline" href="<?php echo esc_url( \$content['cta_button_secondary_url'] ); ?>">
					<?php echo esc_html( \$content['cta_button_secondary_text'] ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>

PHP;
	}

	/**
	 * @param array<string, mixed> $config Config.
	 * @return string
	 */
	private function build_css( $config ) {
		$css = $config['css_class'];
		$slug = $config['slug'];

		return <<<CSS
/**
 * {$config['title']} page styles (scaffolded).
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

.{$css}-main {
  background: var(--color-white);
  --{$slug}-section-padding: clamp(4rem, 7vw, 6.5rem);
  --{$slug}-accent: var(--color-primary);
  --{$slug}-cream-bg: #f9f8f3;
  --{$slug}-text: #4a4a4a;
}

.{$css}-section {
  padding: var(--{$slug}-section-padding) 0;
}

.{$css}-section--cream {
  background: var(--{$slug}-cream-bg);
}

.{$css}__heading {
  margin: 0 0 var(--space-6);
  color: var(--{$slug}-accent);
  font-family: var(--font-heading);
  font-size: clamp(1.75rem, 1.5rem + 0.6vw, 2.5rem);
  line-height: 1.2;
}

.{$css}__copy {
  color: var(--{$slug}-text);
  font-size: clamp(1rem, 0.95rem + 0.1vw, 1.125rem);
  line-height: 1.75;
}

.{$css}__copy p {
  margin: 0 0 var(--space-5);
}

/* Hero */
.{$css}-hero {
  position: relative;
  min-height: min(75svh, 720px);
  display: flex;
  align-items: center;
  margin-top: var(--header-height);
  background: #1a3330;
  color: var(--color-white);
  background-size: cover;
  background-position: center;
}

.{$css}-hero__overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(90deg, rgba(0, 0, 0, 0.55) 0%, rgba(0, 0, 0, 0.2) 100%);
}

.{$css}-hero__content {
  position: relative;
  z-index: 1;
  max-width: 40rem;
  padding: clamp(3rem, 8vw, 5rem) 0;
}

.{$css}-hero__eyebrow {
  margin: 0 0 var(--space-4);
  font-size: var(--text-sm);
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.{$css}-hero__heading {
  margin: 0 0 var(--space-5);
  font-family: var(--font-heading);
  font-size: clamp(2.25rem, 1.75rem + 1.5vw, 3.5rem);
  line-height: 1.1;
}

.{$css}-hero__body {
  margin: 0 0 var(--space-6);
  font-size: clamp(1rem, 0.95rem + 0.15vw, 1.2rem);
  line-height: 1.6;
}

/* Split */
.{$css}-split__inner {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: clamp(var(--space-10), 5vw, var(--space-16));
  align-items: center;
}

.{$css}-split--image-left .{$css}-split__media {
  order: -1;
}

.{$css}-split__media {
  margin: 0;
}

.{$css}-split__image {
  width: 100%;
  height: auto;
  border-radius: var(--border-radius-lg, 12px);
  display: block;
}

/* CTA */
.{$css}-cta__inner {
  text-align: center;
  max-width: 42rem;
  margin-inline: auto;
}

.{$css}-cta__heading {
  margin: 0 0 var(--space-4);
  color: var(--{$slug}-accent);
  font-family: var(--font-heading);
  font-size: clamp(2rem, 1.75rem + 0.5vw, 2.75rem);
}

.{$css}-cta__subheading {
  margin: 0 0 var(--space-4);
  font-size: var(--text-lg);
}

.{$css}-cta__body {
  margin: 0 0 var(--space-8);
  color: var(--{$slug}-text);
}

.{$css}-cta__actions {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-4);
  justify-content: center;
}

.{$css}-cta__btn--primary {
  background: var(--{$slug}-accent);
  color: var(--color-white);
}

.{$css}-cta__btn--outline {
  border: 1px solid var(--{$slug}-accent);
  color: var(--{$slug}-accent);
}

@media (max-width: 768px) {
  .{$css}-split__inner {
    grid-template-columns: 1fr;
  }

  .{$css}-split--image-left .{$css}-split__media {
    order: 0;
  }
}

CSS;
	}

	/**
	 * @param array<string, mixed> $config Config.
	 * @return string
	 */
	private function build_helpers( $config ) {
		$code    = $config['code'];
		$prefix  = $config['option_prefix'];
		$title   = $config['title'];
		$defaults = anna_scaffold_build_default_content( $config['sections'], $title );
		$defaults_export = var_export( $defaults, true );
		$image_keys = array();
		$url_keys   = array();
		$textarea_keys = array();

		foreach ( $config['sections'] as $section ) {
			foreach ( $section['fields'] as $key => $field ) {
				$type = $field['type'] ?? 'text';
				$full = $prefix . $key;
				if ( 'media' === $type ) {
					$image_keys[] = $key;
				} elseif ( 'url' === $type ) {
					$url_keys[] = $key;
				} elseif ( 'textarea' === $type ) {
					$textarea_keys[] = $key;
				}
			}
		}

		$image_keys_php    = var_export( $image_keys, true );
		$url_keys_php      = var_export( array_map( static fn( $k ) => $prefix . $k, $url_keys ), true );
		$textarea_keys_php = var_export( array_map( static fn( $k ) => $prefix . $k, $textarea_keys ), true );

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
	return {$defaults_export};
}

/**
 * @return array<string, mixed>
 */
function anna_get_{$code}_theme_option_defaults() {
	\$out = array();
	foreach ( anna_get_{$code}_default_content() as \$key => \$value ) {
		\$out['{$prefix}' . \$key] = \$value;
	}
	return \$out;
}

/**
 * @return array<string, string>
 */
function anna_get_{$code}_page_option_map() {
	\$map = array();
	foreach ( array_keys( anna_get_{$code}_default_content() ) as \$key ) {
		\$map[ \$key ] = '{$prefix}' . \$key;
	}
	return \$map;
}

/**
 * @return array<string, mixed>
 */
function anna_get_{$code}_page_content() {
	\$defaults   = anna_get_{$code}_default_content();
	\$theme_defs = anna_get_default_options();
	\$content    = array();
	\$image_keys = {$image_keys_php};

	foreach ( \$defaults as \$key => \$default_value ) {
		\$option_key = '{$prefix}' . \$key;
		\$fallback   = \$theme_defs[ \$option_key ] ?? \$default_value;

		if ( in_array( \$key, \$image_keys, true ) ) {
			\$content[ \$key ] = absint( anna_get_option( \$option_key, \$fallback ) );
		} else {
			\$content[ \$key ] = anna_get_option( \$option_key, \$fallback );
		}
	}

	\$post_id = anna_get_current_page_content_id();
	if ( \$post_id && function_exists( 'anna_content_get_{$code}_page_content' ) ) {
		\$saved = anna_content_get_{$code}_page_content( \$post_id );
		if ( is_array( \$saved ) && ! empty( \$saved ) ) {
			\$merge = array();
			foreach ( \$saved as \$key => \$value ) {
				if ( is_array( \$value ) ) {
					continue;
				}
				if ( '' !== trim( (string) \$value ) || ( in_array( \$key, \$image_keys, true ) && absint( \$value ) > 0 ) ) {
					\$merge[ \$key ] = \$value;
				}
			}
			if ( ! empty( \$merge ) ) {
				\$content = wp_parse_args( \$merge, \$content );
			}
		}
	}

	return \$content;
}

/**
 * Anna Content Manager helper for this page.
 *
 * @param int \$post_id Post ID.
 * @return array<string, mixed>
 */
function anna_content_get_{$code}_page_content( \$post_id ) {
	if ( function_exists( 'anna_content_get_scaffold_page_content' ) ) {
		return anna_content_get_scaffold_page_content( \$post_id, '{$code}' );
	}
	return anna_get_{$code}_page_content();
}

/**
 * @param string \$key   Option key.
 * @param mixed  \$value Raw value.
 * @return mixed
 */
function anna_sanitize_{$code}_option( \$key, \$value ) {
	\$image_keys = array_map(
		static function ( \$field ) {
			return '{$prefix}' . \$field;
		},
		{$image_keys_php}
	);

	if ( in_array( \$key, \$image_keys, true ) ) {
		return absint( \$value );
	}

	\$url_keys = {$url_keys_php};
	if ( in_array( \$key, \$url_keys, true ) ) {
		return esc_url_raw( \$value );
	}

	\$textarea_keys = {$textarea_keys_php};
	if ( in_array( \$key, \$textarea_keys, true ) ) {
		return sanitize_textarea_field( \$value );
	}

	return sanitize_text_field( \$value );
}

PHP;
	}

	/**
	 * @param array<string, mixed> $config Config.
	 * @return string
	 */
	private function build_settings_fields( $config ) {
		$code   = $config['code'];
		$prefix = $config['option_prefix'];
		$title  = $config['title'];
		$render_blocks = '';

		foreach ( $config['sections'] as $section ) {
			$label = addslashes( (string) ( $section['label'] ?? 'Section' ) );
			$render_blocks .= "\tanna_field_heading( __( '{$label}', 'anna-baylis' ) );\n";

			foreach ( $section['fields'] as $key => $field ) {
				$field_label = addslashes( (string) ( $field['label'] ?? $key ) );
				$option_key  = $prefix . $key;
				$type        = $field['type'] ?? 'text';

				if ( 'textarea' === $type ) {
					$render_blocks .= "\tanna_field_textarea( '{$option_key}', __( '{$field_label}', 'anna-baylis' ), '', 6 );\n";
				} elseif ( 'media' === $type ) {
					$render_blocks .= "\tanna_field_media( '{$option_key}', __( '{$field_label}', 'anna-baylis' ) );\n";
				} elseif ( 'select' === $type ) {
					$choices = var_export( $field['choices'] ?? array(), true );
					$render_blocks .= "\tanna_field_select( '{$option_key}', __( '{$field_label}', 'anna-baylis' ), {$choices} );\n";
				} elseif ( 'url' === $type ) {
					$render_blocks .= "\tanna_field_text( '{$option_key}', __( '{$field_label}', 'anna-baylis' ), '', 'url' );\n";
				} else {
					$render_blocks .= "\tanna_field_text( '{$option_key}', __( '{$field_label}', 'anna-baylis' ) );\n";
				}
			}
		}

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
{$render_blocks}}

PHP;
	}
}
