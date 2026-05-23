<?php
/**
 * The footer for our theme
 *
 * @package anna-theme
 */
?>

<footer class="site-footer">
	<div class="container site-footer__inner">
		<div class="site-info">
			<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'anna-theme' ); ?></p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>

</body>
</html>
