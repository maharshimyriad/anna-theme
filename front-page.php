<?php
/**
 * The front page template file
 *
 * @package Anna_Baylis
 */

get_header();
?>

<main id="main-content" class="site-main" role="main">
	
	<!-- Hero Section -->
	<?php get_template_part('template-parts/sections/hero'); ?>

	<!-- Problem Recognition Section -->
	<?php get_template_part('template-parts/sections/problem'); ?>

	<!-- Services Grid Section -->
	<?php get_template_part('template-parts/sections/services'); ?>

	<!-- About Founder Section -->
	<?php get_template_part('template-parts/sections/about'); ?>

	<!-- Testimonials Section -->
	<?php get_template_part('template-parts/sections/testimonials'); ?>

	<!-- CTA Banner Section -->
	<?php get_template_part('template-parts/sections/cta'); ?>

</main>

<?php
get_footer();
