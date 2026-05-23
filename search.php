<?php
/**
 * Search results template.
 * @package Anna_Baylis
 * @since   1.0.0
 */
get_header(); ?>
<main id="main-content" class="anna-main" role="main">
	<section class="anna-section anna-section--md">
		<div class="anna-container">
			<header class="anna-text-center" style="margin-bottom:var(--space-12);">
				<h1 class="anna-heading anna-heading--2">
					<?php printf( esc_html__( 'Search results for: %s', 'anna-baylis' ), '<span class="anna-text-primary">' . esc_html( get_search_query() ) . '</span>' ); ?>
				</h1>
			</header>
			<?php if ( have_posts() ) : ?>
				<div class="anna-grid anna-grid--3-col">
					<?php while ( have_posts() ) : the_post(); ?>
						<article class="anna-card anna-card--post">
							<div class="anna-card__header">
								<span class="anna-card__eyebrow"><?php echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ); ?></span>
								<h2 class="anna-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							</div>
							<div class="anna-card__body"><?php the_excerpt(); ?></div>
						</article>
					<?php endwhile; ?>
				</div>
				<?php the_posts_navigation(); ?>
			<?php else : ?>
				<div class="anna-text-center">
					<p class="anna-text-muted"><?php esc_html_e( 'No results found. Try a different search.', 'anna-baylis' ); ?></p>
					<?php get_search_form(); ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>
<?php get_footer();
