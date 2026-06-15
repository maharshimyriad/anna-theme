<?php
/**
 * Blog page: post grid with category filter.
 *
 * Uses the main WP_Query (set up by home.php / WordPress core) so that
 * pagination routing via /page/N/ works natively without a secondary query.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wp_query;

$blog = get_query_var( 'anna_blog_page_content', array() );
if ( empty( $blog ) ) {
	$blog = anna_get_blog_page_content();
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$active_cat  = isset( $_GET['cat'] ) ? sanitize_key( wp_unslash( $_GET['cat'] ) ) : '';
$categories  = $blog['categories'] ?? array();
$blog_page_id = get_option( 'page_for_posts' );
$blog_base_url = get_permalink( $blog_page_id );
?>

<section class="anna-blog-page-posts">
	<div class="anna-container anna-container--max">

		<!-- ── Section header with category filter ──────────────────── -->
		<div class="anna-blog-page-posts__header anna-reveal">
			<div class="anna-blog-page-posts__header-left">
				<?php if ( ! empty( $blog['section_heading'] ) ) : ?>
					<h2 class="anna-blog-page-posts__heading"><?php echo esc_html( $blog['section_heading'] ); ?></h2>
				<?php endif; ?>
				<?php if ( ! empty( $blog['section_subtext'] ) ) : ?>
					<p class="anna-blog-page-posts__subtext"><?php echo esc_html( $blog['section_subtext'] ); ?></p>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $categories ) ) : ?>
				<nav class="anna-blog-page-posts__cats"
					aria-label="<?php esc_attr_e( 'Filter by category', 'anna-baylis' ); ?>">
					<?php foreach ( $categories as $cat ) : ?>
						<?php
						$cat_slug  = $cat['slug'] ?? '';
						$cat_label = $cat['label'] ?? '';
						$is_active = ( $cat_slug === $active_cat ) || ( '' === $cat_slug && '' === $active_cat );
						$cat_url   = $cat_slug
							? add_query_arg( 'cat', $cat_slug, $blog_base_url )
							: $blog_base_url;
						?>
						<a href="<?php echo esc_url( $cat_url ); ?>"
							class="anna-blog-page-posts__cat-btn<?php echo $is_active ? ' is-active' : ''; ?>"
							<?php echo $is_active ? 'aria-current="true"' : ''; ?>>
							<?php echo esc_html( $cat_label ); ?>
						</a>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>
		</div>

		<!-- ── Post grid ────────────────────────────────────────────── -->
		<?php if ( have_posts() ) : ?>
			<div class="anna-blog-page-posts__grid anna-stagger">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php
					$post_cats    = get_the_category();
					$reading_time = max( 1, (int) ceil( str_word_count( wp_strip_all_tags( get_the_content() ) ) / 200 ) );
					?>
					<article class="anna-blog-card" id="post-<?php the_ID(); ?>">

						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php the_permalink(); ?>" class="anna-blog-card__thumb-link" tabindex="-1" aria-hidden="true">
								<figure class="anna-blog-card__thumb">
									<?php the_post_thumbnail( 'medium_large', array( 'class' => 'anna-blog-card__img' ) ); ?>
								</figure>
							</a>
						<?php endif; ?>

						<div class="anna-blog-card__body">
							<div class="anna-blog-card__cat--container">
								<?php foreach ( $post_cats as $post_cat ) : ?>
									<span class="anna-blog-card__cat anna-badge anna-badge--primary">
										<?php echo esc_html( $post_cat->name ); ?>
									</span>
								<?php endforeach; ?>
							</div>

							<h3 class="anna-blog-card__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h3>

							<p class="anna-blog-card__excerpt">
								<?php echo wp_trim_words( get_the_excerpt() ?: wp_strip_all_tags( get_the_content() ), 18, '…' ); ?>
							</p>

							<footer class="anna-blog-card__footer">
								<time class="anna-blog-card__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none"
										xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
										<rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
										<line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="2" />
										<line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="2" />
										<line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="2" />
									</svg>
									<?php echo esc_html( get_the_date( 'M j, Y' ) ); ?>
								</time>
								<span class="anna-blog-card__read-time">
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none"
										xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
										<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
										<path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
									</svg>
									<?php
									echo esc_html(
										sprintf(
											/* translators: %d: minutes */
											_n( '%d min read', '%d min read', $reading_time, 'anna-baylis' ),
											$reading_time
										)
									);
									?>
								</span>
							</footer>
						</div>

					</article>
				<?php endwhile; ?>
			</div>
		<?php else : ?>
			<p class="anna-blog-page-posts__empty"><?php esc_html_e( 'No posts found.', 'anna-baylis' ); ?></p>
		<?php endif; ?>

		<!-- ── Pagination ───────────────────────────────────────────── -->
		<?php if ( $wp_query->max_num_pages > 1 ) : ?>
			<nav class="anna-blog-pagination" aria-label="<?php esc_attr_e( 'Posts pagination', 'anna-baylis' ); ?>">
				<?php
				$total_pages  = (int) $wp_query->max_num_pages;
				$current_page = max( 1, (int) get_query_var( 'paged' ) );

				// Previous arrow.
				if ( $current_page > 1 ) :
					$prev = get_previous_posts_page_link();
					if ( $active_cat ) {
						$prev = add_query_arg( 'cat', $active_cat, $prev );
					}
					?>
					<a href="<?php echo esc_url( $prev ); ?>"
						class="anna-blog-pagination__btn anna-blog-pagination__btn--prev"
						aria-label="<?php esc_attr_e( 'Previous page', 'anna-baylis' ); ?>">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
							stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<polyline points="15 18 9 12 15 6" />
						</svg>
					</a>
				<?php endif; ?>

				<?php
				// Page number links via paginate_links() — WordPress handles pretty vs plain URLs.
				$paginate_args = array(
					'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
					'format'    => '',
					'current'   => $current_page,
					'total'     => $total_pages,
					'type'      => 'array',
					'prev_next' => false,
					'add_args'  => $active_cat ? array( 'cat' => $active_cat ) : false,
				);

				$page_links = paginate_links( $paginate_args );

				if ( $page_links ) :
					foreach ( $page_links as $link ) :
						// paginate_links() returns <span> for current, <a> for others — swap to our BEM classes.
						if ( strpos( $link, 'current' ) !== false ) {
							echo '<span class="anna-blog-pagination__btn anna-blog-pagination__btn--current" aria-current="page">'
								. esc_html( $current_page )
								. '</span>';
						} elseif ( strpos( $link, 'dots' ) !== false ) {
							echo '<span class="anna-blog-pagination__ellipsis">&hellip;</span>';
						} else {
							// Extract href and page number from the generated <a> tag.
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

				<?php
				// Next arrow.
				if ( $current_page < $total_pages ) :
					$next = get_next_posts_page_link( $total_pages );
					if ( $active_cat ) {
						$next = add_query_arg( 'cat', $active_cat, $next );
					}
					?>
					<a href="<?php echo esc_url( $next ); ?>"
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

	</div>
</section>
