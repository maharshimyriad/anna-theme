<?php
/**
 * Template part: Health Support page hero.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow  = anna_get_option( 'hs_pg_hero_eyebrow' );
$heading  = anna_get_option( 'hs_pg_hero_heading' );
$image_id = anna_get_option( 'hs_pg_hero_image_id' );
$image_url= $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';
?>

<section class="anna-section anna-hero anna-hero--health-support" <?php echo $image_url ? 'style="background-image: url(' . esc_url( $image_url ) . ');"' : ''; ?>>
	<div class="anna-hero__overlay"></div>
	<div class="anna-container">
		<div class="anna-hero__content" data-reveal="fade-up">
			<?php if ( $eyebrow ) : ?>
				<p class="anna-hero__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>
			<?php if ( $heading ) : ?>
				<h1 class="anna-hero__title"><?php echo nl2br( esc_html( $heading ) ); ?></h1>
			<?php endif; ?>
		</div>
	</div>
</section>
