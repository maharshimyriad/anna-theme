<?php
/**
 * Blog page: post grid with category filter.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

$blog = get_query_var('anna_blog_page_content', array());
if (empty($blog)) {
	$blog = anna_get_blog_page_content();
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$active_cat = isset($_GET['cat']) ? sanitize_key(wp_unslash($_GET['cat'])) : '';
$posts_per_page = 9;
$paged = max(1, absint(get_query_var('paged') ?: ($_GET['paged'] ?? 1))); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

$query_args = array(
	'post_type' => 'post',
	'post_status' => 'publish',
	'posts_per_page' => $posts_per_page,
	'paged' => $paged,
	'orderby' => 'date',
	'order' => 'DESC',
);

if ($active_cat) {
	$query_args['category_name'] = $active_cat;
}

$posts_query = new WP_Query($query_args);
$categories = $blog['categories'] ?? array();
?>

<section class="anna-blog-page-posts">
	<div class="anna-container anna-container--max">

		<!-- ── Section header with category filter ──────────────────── -->
		<div class="anna-blog-page-posts__header anna-reveal">
			<div class="anna-blog-page-posts__header-left">
				<?php if (!empty($blog['section_heading'])): ?>
					<h2 class="anna-blog-page-posts__heading"><?php echo esc_html($blog['section_heading']); ?></h2>
				<?php endif; ?>
				<?php if (!empty($blog['section_subtext'])): ?>
					<p class="anna-blog-page-posts__subtext"><?php echo esc_html($blog['section_subtext']); ?></p>
				<?php endif; ?>
			</div>

			<?php if (!empty($categories)): ?>
				<nav class="anna-blog-page-posts__cats"
					aria-label="<?php esc_attr_e('Filter by category', 'anna-baylis'); ?>">
				<?php 
				// Get the blog page URL as the base for category filters.
				$blog_page_id = get_option('page_for_posts');
				$blog_base_url = get_permalink($blog_page_id);
				foreach ($categories as $cat): 
				?>
					<?php
					$cat_slug = $cat['slug'] ?? '';
					$cat_label = $cat['label'] ?? '';
					$is_active = ($cat_slug === $active_cat) || ('' === $cat_slug && '' === $active_cat);
					$cat_url = $cat_slug ? add_query_arg('cat', $cat_slug, $blog_base_url) : $blog_base_url;
						?>
						<a href="<?php echo esc_url($cat_url); ?>"
							class="anna-blog-page-posts__cat-btn<?php echo $is_active ? ' is-active' : ''; ?>" <?php echo $is_active ? 'aria-current="true"' : ''; ?>>
							<?php echo esc_html($cat_label); ?>
						</a>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>
		</div>

		<!-- ── Post grid ────────────────────────────────────────────── -->
		<?php if ($posts_query->have_posts()): ?>
			<div class="anna-blog-page-posts__grid anna-stagger">
				<?php while ($posts_query->have_posts()):
					$posts_query->the_post(); ?>
					<?php
					$post_cats = get_the_category();
					$reading_time = max(1, (int) ceil(str_word_count(wp_strip_all_tags(get_the_content())) / 200));
					?>
					<article class="anna-blog-card" id="post-<?php the_ID(); ?>">

						<?php if (has_post_thumbnail()): ?>
							<a href="<?php the_permalink(); ?>" class="anna-blog-card__thumb-link" tabindex="-1" aria-hidden="true">
								<figure class="anna-blog-card__thumb">
									<?php the_post_thumbnail('medium_large', array('class' => 'anna-blog-card__img')); ?>
								</figure>
							</a>
						<?php endif; ?>

						<div class="anna-blog-card__body">
							<div class="anna-blog-card__cat--container">
								<?php if (!empty($post_cats)): ?>
									<?php foreach ($post_cats as $cat): ?>
										<span class="anna-blog-card__cat anna-badge anna-badge--primary">
											<?php echo esc_html($cat->name); ?>
										</span>
									<?php endforeach; ?>
								<?php endif; ?>
							</div>

							<h3 class="anna-blog-card__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h3>

							<p class="anna-blog-card__excerpt">
								<?php echo wp_trim_words(get_the_excerpt() ?: wp_strip_all_tags(get_the_content()), 18, '…'); ?>
							</p>

							<footer class="anna-blog-card__footer">
								<time class="anna-blog-card__date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none"
										xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
										<rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="currentColor"
											stroke-width="2" />
										<line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="2" />
										<line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="2" />
										<line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="2" />
									</svg>
									<?php echo esc_html(get_the_date('M j, Y')); ?>
								</time>
								<span class="anna-blog-card__read-time">
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none"
										xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
										<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
										<path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"
											stroke-linejoin="round" />
									</svg>
									<?php
									echo esc_html(
										sprintf(
											/* translators: %d: minutes */
											_n('%d min read', '%d min read', $reading_time, 'anna-baylis'),
											$reading_time
										)
									);
									?>
								</span>
							</footer>
						</div>

					</article>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			</div>
		<?php else: ?>
			<p class="anna-blog-page-posts__empty"><?php esc_html_e('No posts found.', 'anna-baylis'); ?></p>
			<?php wp_reset_postdata(); ?>
		<?php endif; ?>

		<!-- ── Pagination ───────────────────────────────────────────── -->
		<?php if ($posts_query->max_num_pages > 1): ?>
			<nav class="anna-blog-pagination" aria-label="<?php esc_attr_e('Posts pagination', 'anna-baylis'); ?>">
				<?php
				$total_pages = $posts_query->max_num_pages;
				$current_page = $paged;

				// Build base URL preserving category filter.
				$blog_page_id = get_option('page_for_posts');
				$base_url = get_permalink($blog_page_id);
				if ($active_cat) {
					$base_url = add_query_arg('cat', $active_cat, $base_url);
				}

				// Previous.
				if ($current_page > 1):
					$prev_url = add_query_arg('paged', $current_page - 1, $base_url);
					?>
					<a href="<?php echo esc_url($prev_url); ?>"
						class="anna-blog-pagination__btn anna-blog-pagination__btn--prev"
						aria-label="<?php esc_attr_e('Previous page', 'anna-baylis'); ?>">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
							stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<polyline points="15 18 9 12 15 6" />
						</svg>
					</a>
				<?php endif; ?>

				<?php
				// Page numbers.
				for ($i = 1; $i <= $total_pages; $i++):
					if ($i === $current_page):
						?>
						<span class="anna-blog-pagination__btn anna-blog-pagination__btn--current"
							aria-current="page"><?php echo esc_html($i); ?></span>
					<?php elseif ($i === 1 || $i === $total_pages || abs($i - $current_page) <= 2): ?>
						<a href="<?php echo esc_url(add_query_arg('paged', $i, $base_url)); ?>"
							class="anna-blog-pagination__btn"><?php echo esc_html($i); ?></a>
					<?php elseif (abs($i - $current_page) === 3): ?>
						<span class="anna-blog-pagination__ellipsis">&hellip;</span>
					<?php endif; ?>
				<?php endfor; ?>

				<?php
				// Next.
				if ($current_page < $total_pages):
					$next_url = add_query_arg('paged', $current_page + 1, $base_url);
					?>
					<a href="<?php echo esc_url($next_url); ?>"
						class="anna-blog-pagination__btn anna-blog-pagination__btn--next"
						aria-label="<?php esc_attr_e('Next page', 'anna-baylis'); ?>">
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