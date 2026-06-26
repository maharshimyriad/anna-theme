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

				<?php
				global $wp_query;
				if ( $wp_query->max_num_pages > 1 ) :
					$total_pages  = (int) $wp_query->max_num_pages;
					$current_page = max( 1, (int) get_query_var( 'paged' ) );
				?>
				<nav class="anna-blog-pagination" aria-label="<?php esc_attr_e( 'Posts pagination', 'anna-baylis' ); ?>">

					<?php if ( $current_page > 1 ) : ?>
						<a href="<?php echo esc_url( get_previous_posts_page_link() ); ?>"
							class="anna-blog-pagination__btn anna-blog-pagination__btn--prev"
							aria-label="<?php esc_attr_e( 'Previous page', 'anna-baylis' ); ?>">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
								stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<polyline points="15 18 9 12 15 6" />
							</svg>
						</a>
					<?php endif; ?>

					<?php
					$page_links = paginate_links( array(
						'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
						'format'    => '',
						'current'   => $current_page,
						'total'     => $total_pages,
						'type'      => 'array',
						'prev_next' => false,
					) );

					if ( $page_links ) :
						foreach ( $page_links as $link ) :
							if ( strpos( $link, 'current' ) !== false ) {
								echo '<span class="anna-blog-pagination__btn anna-blog-pagination__btn--current" aria-current="page">'
									. esc_html( $current_page )
									. '</span>';
							} elseif ( strpos( $link, 'dots' ) !== false ) {
								echo '<span class="anna-blog-pagination__ellipsis">&hellip;</span>';
							} else {
								preg_match( '/href=["\']([^"\']+)["\']/', $link, $href_match );
								preg_match( '/>(\d+)</', $link, $num_match );
								if ( ! empty( $href_match[1] ) && ! empty( $num_match[1] ) ) {
									echo '<a href="' . esc_url( $href_match[1] ) . '" class="anna-blog-pagination__btn">'
										. esc_html( $num_match[1] )
										. '</a>';
								}
							}
						endforeach;
					endif;
					?>

					<?php if ( $current_page < $total_pages ) : ?>
						<a href="<?php echo esc_url( get_next_posts_page_link( $total_pages ) ); ?>"
							class="anna-blog-pagination__btn anna-blog-pagination__btn--next"
							aria-label="<?php esc_attr_e( 'Next page', 'anna-baylis' ); ?>">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
								stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<polyline points="9 18 15 12 9 6" />
							</svg>
						</a>
					<?php endif; ?>

				</nav>
				<?php endif; ?>

			<?php else : ?>
				<p class="anna-text-center"><?php esc_html_e( 'No posts found.', 'anna-baylis' ); ?></p>
			<?php endif; ?>
		</div>
	</section>
</main>
<?php get_footer();
