<?php
/**
 * The footer template.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$email   = anna_get_option( 'contact_email', 'info@annabaylis.com.au' );
$phone   = anna_get_option( 'contact_phone', '' );
$address = anna_get_option( 'contact_address', '' );
$hours   = anna_get_option( 'contact_hours', '' );
$copy    = anna_get_option( 'copyright_text', '' );

if ( ! $copy ) {
	$copy = sprintf( 'Copyright %s ABN: 62834308042 Anna Baylis', date( 'Y' ) );
}
?>

	<footer class="anna-footer" id="site-footer" role="contentinfo">
		<div class="anna-footer__top anna-container anna-container--max">
			<div class="anna-footer__grid">
				<div class="anna-footer__col anna-footer__col--brand">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="anna-footer__logo">
						<?php
						$footer_logo_id  = absint( anna_get_option( 'footer_logo_id', 0 ) );
						$fallback_logo_id = anna_get_site_logo_id();
						$logo_id         = $footer_logo_id ?: $fallback_logo_id;

						if ( $logo_id ) {
							$logo_src = wp_get_attachment_image_src( $logo_id, 'full' );
							if ( $logo_src ) {
								printf(
									'<img src="%1$s" alt="%2$s" width="%3$s" height="%4$s" loading="lazy" decoding="async">',
									esc_url( $logo_src[0] ),
									esc_attr( get_bloginfo( 'name' ) ),
									esc_attr( $logo_src[1] ),
									esc_attr( $logo_src[2] )
								);
							}
						} else {
							echo esc_html( get_bloginfo( 'name' ) );
						}
						?>
					</a>

					<p class="anna-footer__brand-text">
						<?php echo nl2br( esc_html( anna_get_option( 'footer_description', '' ) ) ); ?>
					</p>
				</div>

				<div class="anna-footer__col anna-footer__col--newsletter">
					<h3 class="anna-footer__col-title"><?php esc_html_e( 'Newsletter', 'anna-baylis' ); ?></h3>
					<div class="ml-embedded" data-form="mTow5J"></div>
				</div>
				<div class="anna-footer__col">
					<h3 class="anna-footer__col-title"><?php esc_html_e( 'Quick Links', 'anna-baylis' ); ?></h3>
					<?php
					$footer_menu_args = array(
								'theme_location' => 'footer',
								'container'      => false,
								'menu_class'     => 'anna-footer__nav',
								'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
								'depth'          => 1,
								'fallback_cb'    => false,
					);

					if ( has_nav_menu( 'footer' ) ) {
						wp_nav_menu( $footer_menu_args );
					} else {
						$menus = wp_get_nav_menus();
						if ( ! empty( $menus ) ) {
							$footer_menu_args['menu'] = $menus[0]->term_id;
							wp_nav_menu( $footer_menu_args );
					}
					}
					?>
				</div>

				<div class="anna-footer__col">
					<h3 class="anna-footer__col-title"><?php esc_html_e( 'Contact', 'anna-baylis' ); ?></h3>
					<address class="anna-footer__contact">
						<?php if ( $email ) : ?><div class="anna-footer__contact-item"><a href="mailto:<?php echo esc_attr( $email ); ?>" class="anna-footer__contact-link"><?php echo esc_html( $email ); ?></a></div><?php endif; ?>
						<?php if ( $phone ) : ?><div class="anna-footer__contact-item"><a href="tel:<?php echo esc_attr( str_replace( ' ', '', $phone ) ); ?>" class="anna-footer__contact-link"><?php echo esc_html( $phone ); ?></a></div><?php endif; ?>
						<?php if ( $address ) : ?><div class="anna-footer__contact-item"><?php echo nl2br( esc_html( $address ) ); ?></div><?php endif; ?>
						<?php if ( $hours ) : ?><div class="anna-footer__contact-item"><?php echo esc_html( $hours ); ?></div><?php endif; ?>
					</address>

					<div class="anna-footer__social">
						<span class="anna-footer__social-label"><?php esc_html_e( 'Socials', 'anna-baylis' ); ?></span>
						<?php anna_social_links( 'footer' ); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="anna-footer__bar">
			<p class="anna-footer__bar-text">
				<?php echo esc_html( $copy ); ?>&nbsp;&nbsp;|&nbsp;&nbsp;<?php esc_html_e( 'Powered by', 'anna-baylis' ); ?> <a href="https://myriadsolutionz.com" target="_blank" rel="noopener noreferrer" class="anna-footer__powered-link">Myriad Solutionz</a>
			</p>
		</div>

	</footer>

</div>

<?php wp_footer(); ?>
</body>
</html>
