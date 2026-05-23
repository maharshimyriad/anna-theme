<?php
/**
 * Single post template.
 * @package Anna_Baylis
 * @since   1.0.0
 */
get_header(); ?>
<main id="main-content" class="anna-main" role="main">
	<article class="anna-section anna-section--md">
		<div class="anna-container anna-container--narrow">
			<?php
			while ( have_posts() ) :
				the_post();
			?>
				<header class="anna-post-header">
					<span class="anna-eyebrow"><?php echo esc_html( get_the_category_list( ', ' ) ); ?></span>
					<h1 class="anna-heading anna-heading--1"><?php the_title(); ?></h1>
					<p class="anna-text-muted" style="margin-top:var(--space-3);font-size:var(--text-sm);">
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
						<?php if ( get_the_author() ) : ?>
							&middot; <?php the_author(); ?>
						<?php endif; ?>
					</p>
				</header>
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="anna-figure anna-figure--rounded" style="margin:var(--space-10) 0;">
						<?php the_post_thumbnail( 'anna-wide' ); ?>
					</figure>
				<?php endif; ?>
				<div class="anna-post-content">
					<?php the_content(); ?>
				</div>
				<?php
				the_post_navigation(
					array(
						'prev_text' => '<span class="anna-text-muted">' . __( 'Previous', 'anna-baylis' ) . '</span> %title',
						'next_text' => '<span class="anna-text-muted">' . __( 'Next', 'anna-baylis' ) . '</span> %title',
					)
				);
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
			endwhile;
			?>
		</div>
	</article>
</main>
<?php get_footer();
