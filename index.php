<?php
/**
 * The main template file.
 *
 * This is the fallback template for WordPress when no more specific template
 * is found in the template hierarchy.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

get_header();
?>

<main id="main-content" class="anna-main" role="main">

	<?php if ( have_posts() ) : ?>

		<div class="anna-container">
			<div class="anna-posts-grid">

				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content/content', get_post_type() );
				endwhile;

				the_posts_navigation(
					array(
						'prev_text' => __( '<span aria-hidden="true">←</span> Older Posts', 'anna-baylis' ),
						'next_text' => __( 'Newer Posts <span aria-hidden="true">→</span>', 'anna-baylis' ),
					)
				);
				?>

			</div>
		</div>

	<?php else : ?>

		<section class="anna-no-results anna-section anna-section--md">
			<div class="anna-container anna-container--narrow">
				<header class="anna-no-results__header">
					<h1 class="anna-heading anna-heading--2">
						<?php esc_html_e( 'Nothing found', 'anna-baylis' ); ?>
					</h1>
				</header>
				<p><?php esc_html_e( 'It seems we cannot find what you are looking for. Try a search below.', 'anna-baylis' ); ?></p>
				<?php get_search_form(); ?>
			</div>
		</section>

	<?php endif; ?>

</main>

<?php
get_footer();
