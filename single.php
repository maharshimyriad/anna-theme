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

		<!-- ── Hero ──────────────────────────────────────────────────────── -->
		<section class="anna-single-hero">
			<div class="anna-container anna-container--max">
				<div class="anna-single-hero__inner anna-reveal">

					<?php
					$categories = get_the_category();
					if ( $categories ) :
						$cat = $categories[0];
					?>
						<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"
						   class="anna-eyebrow anna-single-hero__eyebrow">
							<?php echo esc_html( $cat->name ); ?>
						</a>
					<?php endif; ?>

					<h1 class="anna-single-hero__heading">
						<?php the_title(); ?>
					</h1>

					<div class="anna-single-hero__meta">
						<time class="anna-single-hero__date"
							  datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
							<?php echo esc_html( get_the_date() ); ?>
						</time>

						<?php if ( get_the_author() ) : ?>
							<span class="anna-single-hero__sep" aria-hidden="true">&middot;</span>
							<span class="anna-single-hero__author">
								<?php the_author(); ?>
							</span>
						<?php endif; ?>

						<?php
						$read_time = anna_estimate_read_time( get_the_content() );
						if ( $read_time ) :
						?>
							<span class="anna-single-hero__sep" aria-hidden="true">&middot;</span>
							<span class="anna-single-hero__read-time">
								<?php echo esc_html( $read_time ); ?>
							</span>
						<?php endif; ?>
					</div>

				</div>
			</div>
		</section>

		<!-- ── Featured image ────────────────────────────────────────────── -->
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="anna-single-featured-image">
				<figure class="anna-single-featured-image__figure">
					<?php
					the_post_thumbnail(
						'full',
						array(
							'class'   => 'anna-single-featured-image__img',
							'loading' => 'eager',
							'decoding' => 'async',
						)
					);
					?>
				</figure>
			</div>
		<?php endif; ?>

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

		<!-- ── Comments ──────────────────────────────────────────────────── -->
		<?php if ( comments_open() || get_comments_number() ) : ?>
			<section class="anna-single-comments">
				<div class="anna-container anna-container--max">
					<div class="anna-single-layout">
						<?php comments_template(); ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

	<?php endwhile; ?>
</main>

<?php get_footer();
