<?php
/**
 * Front page template.
 *
 * Renders all homepage sections via modular template parts.
 * Each section can be toggled on/off from the admin panel.
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
	// Section order — each is conditionally rendered
	$sections = array( 'hero', 'intro', 'recognition', 'services', 'about', 'testimonials', 'cta' );

	foreach ( $sections as $section ) {
		if ( anna_section_enabled( $section ) ) {
			get_template_part( 'template-parts/sections/' . $section );
		}
	}
	?>

</main>

<?php
get_footer();
