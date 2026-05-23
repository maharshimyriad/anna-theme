<?php
/**
 * The template for displaying the footer
 *
 * @package Anna_Baylis
 */
?>
<footer class="site-footer" role="contentinfo">
	<div class="container footer-inner">
		<div class="footer-widget branding-widget">
			<?php if ( has_custom_logo() ) : ?>
				<div class="footer-logo"><?php the_custom_logo(); ?></div>
			<?php else: ?>
				<h2 class="footer-title"><?php bloginfo( 'name' ); ?></h2>
			<?php endif; ?>
			<p class="footer-desc">You can tell when something is working - you feel in your community, the people you support start showing change - from the inside out.</p>
			<p class="footer-copyright">&copy; <?php echo date('Y'); ?> Anna Baylis. All rights reserved.</p>
		</div>

		<div class="footer-widget nav-widget">
			<h3>Navigation</h3>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'menu_id'        => 'footer-menu',
					'container'      => false,
					'fallback_cb'    => false,
				)
			);
			?>
		</div>

		<div class="footer-widget newsletter-widget">
			<h3>Newsletter</h3>
			<form class="newsletter-form" aria-label="Newsletter signup">
				<label for="nl-name" class="screen-reader-text">Name</label>
				<input type="text" id="nl-name" placeholder="Name" required>
				<label for="nl-email" class="screen-reader-text">Email</label>
				<input type="email" id="nl-email" placeholder="Email" required>
				<button type="submit" class="btn btn--secondary">Subscribe</button>
			</form>
		</div>

		<div class="footer-widget contact-widget">
			<h3>Contact</h3>
			<address>
				<p><a href="mailto:hello@annabaylis.com.au">hello@annabaylis.com.au</a></p>
				<p><a href="tel:0400111222">0400 111 222</a></p>
				<p>St Kilda, Melbourne<br>Wurundjeri Woi Wurrung Country</p>
			</address>
			<div class="social-links">
				<!-- Social links go here -->
			</div>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
