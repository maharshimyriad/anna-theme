<?php
/**
 * Front page template.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main-content" class="anna-main" role="main">
	<?php
	$sections = array( 'hero', 'intro', 'services', 'about', 'testimonials', 'cta' );

	foreach ( $sections as $section ) {
		if ( anna_section_enabled( $section ) || ( 'intro' === $section && anna_section_enabled( 'recognition' ) ) ) {
			get_template_part( 'template-parts/pages/home/' . $section );
		}
	}
	?>
</main>

<?php
get_footer();
