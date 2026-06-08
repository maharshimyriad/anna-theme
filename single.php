<?php
/**
 * Single post template.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main-content" class="anna-main anna-single-main" role="main">
	<?php while ( have_posts() ) : the_post(); ?>

		<!-- ── Featured image banner (full width, no container) ─────────── -->
		<?php if ( has_post_thumbnail() ) :
			$thumb_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
		?>
			<div class="anna-single-featured-image"
				 style="--thumb-url: url('<?php echo esc_url( $thumb_url ); ?>');"
				 role="img"
				 aria-label="<?php echo esc_attr( get_the_title() ); ?>">
			</div>
		<?php endif; ?>

		<!-- ── Post header (title + meta) ───────────────────────────────── -->
		<div class="anna-single-header">
			<div class="anna-container anna-container--max">
				<div class="anna-single-header__inner anna-reveal">

					<?php
					$categories = get_the_category();
					if ( $categories ) :
						$cat = $categories[0];
					?>
						<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"
						   class="anna-eyebrow anna-single-header__eyebrow">
							<?php echo esc_html( $cat->name ); ?>
						</a>
					<?php endif; ?>

					<h1 class="anna-single-header__heading">
						<?php the_title(); ?>
					</h1>

					<div class="anna-single-header__meta">
						<time class="anna-single-header__date"
							  datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
							<?php echo esc_html( get_the_date() ); ?>
						</time>

						<?php if ( get_the_author() ) : ?>
							<span class="anna-single-header__sep" aria-hidden="true">&middot;</span>
							<span class="anna-single-header__author">
								<?php the_author(); ?>
							</span>
						<?php endif; ?>

						<?php
						$read_time = anna_estimate_read_time( get_the_content() );
						if ( $read_time ) :
						?>
							<span class="anna-single-header__sep" aria-hidden="true">&middot;</span>
							<span class="anna-single-header__read-time">
								<?php echo esc_html( $read_time ); ?>
							</span>
						<?php endif; ?>
					</div>

				</div>
			</div>
		</div>

		<!-- ── Content ───────────────────────────────────────────────────── -->
		<section class="anna-single-content-section">
			<div class="anna-container anna-container--max">
				<div class="anna-single-layout">

					<article class="anna-single-article anna-post-content anna-reveal">
						<?php the_content(); ?>

						<?php
						wp_link_pages(
							array(
								'before' => '<nav class="anna-single-page-links" aria-label="' . esc_attr__( 'Page navigation', 'anna-baylis' ) . '"><span class="anna-single-page-links__label">' . esc_html__( 'Pages:', 'anna-baylis' ) . '</span>',
								'after'  => '</nav>',
							)
						);
						?>
					</article>

				</div>
			</div>
		</section>

		<!-- ── Post navigation ───────────────────────────────────────────── -->
		<nav class="anna-single-nav" aria-label="<?php esc_attr_e( 'Post navigation', 'anna-baylis' ); ?>">
			<div class="anna-container anna-container--max">
				<?php
				the_post_navigation(
					array(
						'prev_text'          => '<span class="anna-single-nav__label">' . esc_html__( 'Previous', 'anna-baylis' ) . '</span><span class="anna-single-nav__title">%title</span>',
						'next_text'          => '<span class="anna-single-nav__label">' . esc_html__( 'Next', 'anna-baylis' ) . '</span><span class="anna-single-nav__title">%title</span>',
						'in_same_term'       => false,
						'screen_reader_text' => '',
					)
				);
				?>
			</div>
		</nav>

		<!-- ── Share this post ───────────────────────────────────────────── -->
		<section class="anna-single-share">
			<div class="anna-container anna-container--max">
				<div class="anna-single-layout">
					<div class="anna-single-share__inner">
						<span class="anna-single-share__label"><?php esc_html_e( 'Share this post', 'anna-baylis' ); ?></span>
						<div class="anna-single-share__buttons">

							<?php
							$post_url   = urlencode( get_permalink() );
							$post_title = urlencode( get_the_title() );
							$email_body = urlencode( get_the_title() . ' — ' . get_permalink() );
							?>

							<!-- Email / Gmail -->
							<a href="mailto:?subject=<?php echo $post_title; ?>&body=<?php echo $email_body; ?>"
							   class="anna-single-share__btn anna-single-share__btn--email"
							   aria-label="<?php esc_attr_e( 'Share via Email', 'anna-baylis' ); ?>">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 7l10 7 10-7"/></svg>
								<span><?php esc_html_e( 'Email', 'anna-baylis' ); ?></span>
							</a>

							<!-- X / Twitter -->
							<a href="https://twitter.com/intent/tweet?url=<?php echo $post_url; ?>&text=<?php echo $post_title; ?>"
							   class="anna-single-share__btn anna-single-share__btn--twitter"
							   target="_blank"
							   rel="noopener noreferrer"
							   aria-label="<?php esc_attr_e( 'Share on X (Twitter)', 'anna-baylis' ); ?>">
								<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.748l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
								<span><?php esc_html_e( 'X / Twitter', 'anna-baylis' ); ?></span>
							</a>

							<!-- LinkedIn -->
							<a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $post_url; ?>"
							   class="anna-single-share__btn anna-single-share__btn--linkedin"
							   target="_blank"
							   rel="noopener noreferrer"
							   aria-label="<?php esc_attr_e( 'Share on LinkedIn', 'anna-baylis' ); ?>">
								<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
								<span><?php esc_html_e( 'LinkedIn', 'anna-baylis' ); ?></span>
							</a>

						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- ── You might also like ────────────────────────────────────────── -->
		<?php
		$related_args = array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => 3,
			'post__not_in'        => array( get_the_ID() ),
			'orderby'             => 'rand',
			'ignore_sticky_posts' => true,
		);

		// Prefer posts from same category.
		$cats = get_the_category();
		if ( $cats ) {
			$related_args['category__in'] = wp_list_pluck( $cats, 'term_id' );
		}

		$related_query = new WP_Query( $related_args );

		// Fall back to any recent posts if not enough same-category posts.
		if ( $related_query->post_count < 3 ) {
			unset( $related_args['category__in'] );
			$related_query = new WP_Query( $related_args );
		}
		?>

		<?php if ( $related_query->have_posts() ) : ?>
			<section class="anna-single-related">
				<div class="anna-container anna-container--max">

					<h2 class="anna-single-related__heading">
						<?php esc_html_e( 'You might also like', 'anna-baylis' ); ?>
					</h2>

					<div class="anna-single-related__grid">
						<?php while ( $related_query->have_posts() ) : $related_query->the_post(); ?>
							<?php
							$rel_cats     = get_the_category();
							$rel_cat      = ! empty( $rel_cats ) ? $rel_cats[0] : null;
							$rel_read     = max( 1, (int) ceil( str_word_count( wp_strip_all_tags( get_the_content() ) ) / 200 ) );
							?>
							<article class="anna-blog-card">

								<?php if ( has_post_thumbnail() ) : ?>
									<a href="<?php the_permalink(); ?>" class="anna-blog-card__thumb-link" tabindex="-1" aria-hidden="true">
										<figure class="anna-blog-card__thumb">
											<?php the_post_thumbnail( 'medium_large', array( 'class' => 'anna-blog-card__img' ) ); ?>
										</figure>
									</a>
								<?php endif; ?>

								<div class="anna-blog-card__body">
									<?php if ( $rel_cat ) : ?>
										<span class="anna-blog-card__cat anna-badge anna-badge--primary">
											<?php echo esc_html( $rel_cat->name ); ?>
										</span>
									<?php endif; ?>

									<h3 class="anna-blog-card__title">
										<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									</h3>

									<p class="anna-blog-card__excerpt">
										<?php echo wp_trim_words( get_the_excerpt() ?: wp_strip_all_tags( get_the_content() ), 18, '…' ); ?>
									</p>

									<footer class="anna-blog-card__footer">
										<time class="anna-blog-card__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
											<?php echo esc_html( get_the_date( 'M j, Y' ) ); ?>
										</time>
										<span class="anna-blog-card__read-time">
											<?php echo esc_html( sprintf( _n( '%d min read', '%d min read', $rel_read, 'anna-baylis' ), $rel_read ) ); ?>
										</span>
									</footer>
								</div>

							</article>
						<?php endwhile; ?>
						<?php wp_reset_postdata(); ?>
					</div>

				</div>
			</section>
		<?php endif; ?>

	<?php endwhile; ?>
</main>

<?php get_footer();
