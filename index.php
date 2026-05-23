<?php
/**
 * The main template file
 *
 * @package Anna_Baylis
 */

get_header();
?>

<main id="main-content" class="site-main" role="main">
	<div class="container">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
						<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
					</header>

					<div class="entry-content">
						<?php the_content(); ?>
					</div>
				</article>
				<?php
			endwhile;
		else :
			echo '<p>No content found.</p>';
		endif;
		?>
	</div>
</main>

<?php
get_footer();
