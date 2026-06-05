<?php
/**
 * Template Name: Contact Page
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main-content" class="anna-main anna-contact-page-main" role="main">
	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/pages/contact/index' );
	endwhile;
	?>
</main>

<?php
get_footer();
