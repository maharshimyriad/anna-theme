<?php
/**
 * Page template.
 * @package Anna_Baylis
 * @since   1.0.0
 */
get_header(); ?>
<main id="main-content" class="anna-main" role="main">
	<?php while ( have_posts() ) : the_post(); ?>
		<article class="anna-section anna-section--md">
			<div class="anna-container anna-container--narrow">
				<h1 class="anna-heading anna-heading--1"><?php the_title(); ?></h1>
				<div class="anna-post-content" style="margin-top:var(--space-10);">
					<?php the_content(); ?>
				</div>
			</div>
		</article>
	<?php endwhile; ?>
</main>
<?php get_footer();
