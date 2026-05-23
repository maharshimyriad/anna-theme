<?php
/**
 * The footer template.
 *
 * Dynamic widgetized footer with newsletter, social links,
 * contact information, and copyright.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$email   = anna_get_option( 'contact_email', 'hello@annabaylis.com' );
$phone   = anna_get_option( 'contact_phone', '' );
$address = anna_get_option( 'contact_address', '' );
$copy    = anna_get_option( 'copyright_text', sprintf( '© %s Anna Baylis. All rights reserved.', date( 'Y' ) ) );
?>

	<footer class="anna-footer" id="site-footer" role="contentinfo">

		<div class="anna-footer__top anna-container anna-container--wide">
			<div class="anna-footer__grid">

				<!-- Column 1: Brand -->
				<div class="anna-footer__col anna-footer__col--brand">
					<?php anna_site_logo( 'footer' ); ?>
					<p class="anna-footer__brand-text">
						<?php echo esc_html( anna_get_option( 'footer_description', __( 'Empowering individuals to discover their true potential through transformative coaching and mindful guidance.', 'anna-baylis' ) ) ); ?>
					</p>
					<?php anna_social_links( 'footer' ); ?>
				</div>

				<!-- Column 2: Quick Links -->
				<div class="anna-footer__col">
					<h3 class="anna-footer__col-title"><?php esc_html_e( 'Quick Links', 'anna-baylis' ); ?></h3>
					<?php
					if ( has_nav_menu( 'footer' ) ) {
						wp_nav_menu(
							array(
								'theme_location' => 'footer',
								'container'      => false,
								'menu_class'     => 'anna-footer__nav',
								'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
								'depth'          => 1,
								'fallback_cb'    => false,
								'link_before'    => '',
								'link_after'     => '',
							)
						);
					}
					?>
				</div>

				<!-- Column 3: Contact -->
				<div class="anna-footer__col">
					<h3 class="anna-footer__col-title"><?php esc_html_e( 'Contact', 'anna-baylis' ); ?></h3>
					<address class="anna-footer__contact">
						<?php if ( $email ) : ?>
							<div class="anna-footer__contact-item">
								<svg class="anna-footer__contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 4L12 13L2 4"/></svg>
								<a href="mailto:<?php echo esc_attr( $email ); ?>" class="anna-footer__contact-link"><?php echo esc_html( $email ); ?></a>
							</div>
						<?php endif; ?>

						<?php if ( $phone ) : ?>
							<div class="anna-footer__contact-item">
								<svg class="anna-footer__contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
								<a href="tel:<?php echo esc_attr( str_replace( ' ', '', $phone ) ); ?>" class="anna-footer__contact-link"><?php echo esc_html( $phone ); ?></a>
							</div>
						<?php endif; ?>

						<?php if ( $address ) : ?>
							<div class="anna-footer__contact-item">
								<svg class="anna-footer__contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
								<span><?php echo esc_html( $address ); ?></span>
							</div>
						<?php endif; ?>
					</address>
				</div>

				<!-- Column 4: Newsletter -->
				<div class="anna-footer__col">
					<h3 class="anna-footer__col-title"><?php esc_html_e( 'Stay Connected', 'anna-baylis' ); ?></h3>
					<p class="anna-footer__newsletter-text">
						<?php echo esc_html( anna_get_option( 'newsletter_text', __( 'Subscribe for insights, tips, and updates on upcoming workshops and programs.', 'anna-baylis' ) ) ); ?>
					</p>
					<?php if ( is_active_sidebar( 'footer-newsletter' ) ) : ?>
						<?php dynamic_sidebar( 'footer-newsletter' ); ?>
					<?php else : ?>
						<form class="anna-footer__newsletter-form" action="#" method="post">
							<label class="anna-sr-only" for="footer-email"><?php esc_html_e( 'Email address', 'anna-baylis' ); ?></label>
							<input type="email" id="footer-email" class="anna-input" placeholder="<?php esc_attr_e( 'Your email address', 'anna-baylis' ); ?>" required>
							<button type="submit" class="anna-btn anna-btn--accent anna-btn--sm"><?php esc_html_e( 'Subscribe', 'anna-baylis' ); ?></button>
						</form>
					<?php endif; ?>
				</div>

			</div>
		</div>

		<div class="anna-container anna-container--wide">
			<div class="anna-footer__bottom">
				<p class="anna-footer__copyright"><?php echo esc_html( $copy ); ?></p>

				<ul class="anna-footer__legal">
					<li><a href="<?php echo esc_url( anna_get_option( 'privacy_url', '#' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'anna-baylis' ); ?></a></li>
					<li><a href="<?php echo esc_url( anna_get_option( 'terms_url', '#' ) ); ?>"><?php esc_html_e( 'Terms of Service', 'anna-baylis' ); ?></a></li>
				</ul>

				<button class="anna-footer__back-top" aria-label="<?php esc_attr_e( 'Back to top', 'anna-baylis' ); ?>" onclick="window.scrollTo({top:0,behavior:'smooth'})">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="18 15 12 9 6 15"/></svg>
				</button>
			</div>
		</div>

	</footer>

</div><!-- .anna-site -->

<?php wp_footer(); ?>
</body>
</html>
