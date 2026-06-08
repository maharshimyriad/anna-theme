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
		<div class="anna-footer__top anna-container anna-container--wide">
			<div class="anna-footer__grid">
				<div class="anna-footer__col anna-footer__col--brand">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="anna-footer__logo">
						<img 
							src="https://mediumseagreen-barracuda-518910.hostingersite.com/wp-content/uploads/2026/05/Footer-logo.png" 
							alt="<?php bloginfo( 'name' ); ?>"
						>
					</a>

					<p class="anna-footer__brand-text">
						<?php echo nl2br( esc_html( anna_get_option( 'footer_description', '' ) ) ); ?>
					</p>
					<div class="anna-footer__bottom">
				<p class="anna-footer__copyright"><?php echo esc_html( $copy ); ?></p>
			</div>
				</div>

				<div class="anna-footer__col anna-footer__col--newsletter">
					<div class="ml-embedded" data-form="mTow5J"></div>
				</div>
				<div class="anna-footer__col">
					<h3 class="anna-footer__col-title"><?php esc_html_e( 'Navigation', 'anna-baylis' ); ?></h3>
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
				</div>
			</div>
		</div>

		
	</footer>

</div>

<?php wp_footer(); ?>
</body>
</html>
