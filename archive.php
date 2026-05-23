<?php
/**
 * Archive template.
 * @package Anna_Baylis
 * @since   1.0.0
 */
get_header(); ?>
<main id="main-content" class="anna-main" role="main">
	<section class="anna-section anna-section--md">
		<div class="anna-container">
			<header class="anna-text-center" style="margin-bottom:var(--space-12);">
				<?php the_archive_title( '<h1 class="anna-heading anna-heading--2">', '</h1>' ); ?>
				<?php the_archive_description( '<p class="anna-text-muted" style="margin-top:var(--space-4);max-width:540px;margin-inline:auto;">', '</p>' ); ?>
			</header>
			<?php if ( have_posts() ) : ?>
				<div class="anna-grid anna-grid--3-col">
					<?php while ( have_posts() ) : the_post(); ?>
						<article class="anna-card anna-card--post">
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="anna-card__media">
									<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'anna-card' ); ?></a>
								</div>
							<?php endif; ?>
							<div class="anna-card__header">
								<span class="anna-card__eyebrow"><?php echo esc_html( get_the_date() ); ?></span>
								<h2 class="anna-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							</div>
							<div class="anna-card__body"><?php the_excerpt(); ?></div>
						</article>
					<?php endwhile; ?>
				</div>
				<?php the_posts_navigation(); ?>
			<?php else : ?>
				<p class="anna-text-center"><?php esc_html_e( 'No posts found.', 'anna-baylis' ); ?></p>
			<?php endif; ?>
		</div>
	</section>
</main>
<?php get_footer();
