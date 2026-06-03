<?php
/**
 * Template Name: Mental Health Support Page
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main-content" class="anna-main anna-mhs-page-main" role="main">
	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/pages/mental-health-support/index' );
	endwhile;
	?>
</main>

<?php
get_footer();
