<?php
/**
 * The header template.
 *
 * Outputs the opening HTML, <head>, and site header.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$header_classes = 'anna-header anna-header--solid';
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="<?php echo esc_attr( anna_get_option( 'color_primary', '#007063' ) ); ?>">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Page Transition Overlay — persistent, never recreated, outside all content -->
<div id="page-transition" aria-hidden="true" role="presentation">
	<div class="transition-column"></div>
	<div class="transition-column"></div>
	<div class="transition-column"></div>
	<div class="transition-column"></div>
	<div class="transition-column"></div>
	<div class="transition-column"></div>
	<div class="transition-column"></div>
	<div class="transition-column"></div>
</div>

<div class="anna-site" id="page">

	<header class="<?php echo esc_attr( $header_classes ); ?>" id="site-header" role="banner">
		<div class="anna-container anna-container--max">
			<div class="anna-header__inner">

				<?php anna_site_logo( 'header' ); ?>

				<nav class="anna-nav" id="primary-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'anna-baylis' ); ?>">
					<?php
					if ( has_nav_menu( 'primary' ) ) {
						wp_nav_menu(
							array(
								'theme_location' => 'primary',
								'container'      => false,
								'menu_class'     => 'anna-nav__list',
								'items_wrap'     => '<ul id="%1$s" class="%2$s" role="menubar">%3$s</ul>',
								'depth'          => 2,
								'walker'         => new Anna_Nav_Walker(),
								'fallback_cb'    => false,
							)
						);
					}
					?>
				</nav>

				<div class="anna-header__actions">
					<?php
					$header_cta_text = anna_get_option( 'header_cta_text', __( 'Book a Call', 'anna-baylis' ) );
					$header_cta_url  = anna_get_option( 'header_cta_url', '#contact' );
					?>
					<a target="_blank" href="<?php echo esc_url( $header_cta_url ); ?>" class="anna-btn anna-btn--primary anna-btn--sm anna-hidden--mobile">
						<?php echo esc_html( $header_cta_text ); ?>
					</a>

					<button
						class="anna-mobile-toggle"
						id="mobile-menu-toggle"
						aria-controls="mobile-navigation"
						aria-expanded="false"
						aria-label="<?php esc_attr_e( 'Open menu', 'anna-baylis' ); ?>"
					>
						<span class="anna-mobile-toggle__bar"></span>
						<span class="anna-mobile-toggle__bar"></span>
						<span class="anna-mobile-toggle__bar"></span>
					</button>
				</div>

			</div>
		</div>
	</header>

	<!-- Mobile Navigation Panel -->
	<nav class="anna-mobile-nav" id="mobile-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Mobile Navigation', 'anna-baylis' ); ?>" aria-hidden="true">
		<div class="anna-mobile-nav__header">
			<span class="anna-mobile-nav__label">
				<span class="anna-mobile-nav__label-dot" aria-hidden="true"></span>
				<?php esc_html_e( 'MENU', 'anna-baylis' ); ?>
			</span>
			<button
				type="button"
				class="anna-mobile-nav__close"
				id="mobile-menu-close"
				aria-label="<?php esc_attr_e( 'Close menu', 'anna-baylis' ); ?>"
			>
				<span class="anna-mobile-nav__close-line"></span>
				<span class="anna-mobile-nav__close-line"></span>
			</button>
		</div>
		<?php
		if ( has_nav_menu( 'mobile' ) ) {
			wp_nav_menu(
				array(
					'theme_location' => 'mobile',
					'container'      => false,
					'menu_class'     => 'anna-mobile-nav__list',
					'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
					'depth'          => 2,
					'fallback_cb'    => false,
					'link_before'    => '',
					'link_after'     => '',
				)
			);
		} elseif ( has_nav_menu( 'primary' ) ) {
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'anna-mobile-nav__list',
					'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
					'depth'          => 2,
					'fallback_cb'    => false,
				)
			);
		}
		?>
		<div class="anna-mobile-nav__cta">
			<a href="<?php echo esc_url( $header_cta_url ); ?>" class="anna-btn anna-btn--primary anna-btn--full">
				<?php echo esc_html( $header_cta_text ); ?>
			</a>
		</div>
		<div class="anna-mobile-nav__social">
			<span class="anna-mobile-nav__social-label"><?php esc_html_e( 'Socials', 'anna-baylis' ); ?></span>
			<?php anna_social_links( 'footer' ); ?>
		</div>
	</nav>

<!-- 	<div class="anna-header-placeholder" aria-hidden="true"></div> -->
