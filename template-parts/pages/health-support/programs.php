<?php
/**
 * Template part: Health Support page programs.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = anna_get_option( 'hs_pg_programs_heading' );
$body    = anna_get_option( 'hs_pg_programs_body' );
?>

<section class="anna-section anna-section--soft anna-section--md anna-hs-programs">
	<div class="anna-container anna-container--narrow">
		<div class="anna-hs-programs__text" data-reveal="fade-up">
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
