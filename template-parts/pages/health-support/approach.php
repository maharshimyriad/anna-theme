<?php
/**
 * Template part: Health Support page approach (How I work).
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = anna_get_option( 'hs_pg_work_heading' );
$body    = anna_get_option( 'hs_pg_work_body' );
?>

<section class="anna-section anna-section--soft anna-section--md anna-hs-approach">
	<div class="anna-container anna-container--narrow">
		<div class="anna-hs-approach__text" data-reveal="fade-up">
			<?php if ( $heading ) : ?>
				<h2 class="anna-heading-2 anna-text-primary"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( $body ) : ?>
				<div class="anna-body-text">
					<?php echo wpautop( esc_html( $body ) ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
