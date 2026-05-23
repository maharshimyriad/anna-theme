<?php
/**
 * 404 template.
 * @package Anna_Baylis
 * @since   1.0.0
 */
get_header(); ?>
<main id="main-content" class="anna-main" role="main">
	<section class="anna-section anna-section--xl anna-text-center">
		<div class="anna-container anna-container--xs">
			<span class="anna-text-gradient" style="font-size:var(--text-6xl);font-weight:var(--font-weight-bold);font-family:var(--font-heading);display:block;line-height:1;margin-bottom:var(--space-6);">404</span>
			<h1 class="anna-heading anna-heading--3"><?php esc_html_e( 'Page Not Found', 'anna-baylis' ); ?></h1>
			<p class="anna-text-muted" style="margin:var(--space-6) auto;max-width:420px;">
				<?php esc_html_e( 'The page you are looking for may have been moved, deleted, or doesn\'t exist. Let\'s get you back on track.', 'anna-baylis' ); ?>
			</p>
			<div class="anna-btn-group anna-btn-group--center" style="margin-top:var(--space-8);">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="anna-btn anna-btn--primary"><?php esc_html_e( 'Back to Home', 'anna-baylis' ); ?></a>
			</div>
		</div>
	</section>
</main>
<?php get_footer();
