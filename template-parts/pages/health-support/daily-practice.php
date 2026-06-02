<?php
/**
 * Template part: Health Support page daily practice.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = anna_get_option( 'hs_pg_practice_heading' );
$body    = anna_get_option( 'hs_pg_practice_body' );
$link_txt = anna_get_option( 'hs_pg_practice_link_text' );
$link_url = anna_get_option( 'hs_pg_practice_link_url' );
?>

<section class="anna-section anna-section--primary anna-section--md anna-hs-practice">
	<div class="anna-container anna-container--narrow">
		<div class="anna-hs-practice__text" data-reveal="fade-up">
			<?php if ( $heading ) : ?>
				<h2 class="anna-heading-2 anna-text-white"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( $body ) : ?>
				<div class="anna-body-text anna-text-white">
					<?php echo wpautop( esc_html( $body ) ); ?>
				</div>
			<?php endif; ?>
			<?php if ( $link_txt && $link_url ) : ?>
				<div class="anna-margin-top-md">
					<a href="<?php echo esc_url( $link_url ); ?>" class="anna-text-white" style="text-decoration: underline;">
						<?php echo esc_html( $link_txt ); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
