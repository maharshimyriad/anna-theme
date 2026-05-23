<?php
/**
 * Template part: Intro / Approach Section
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$quote       = anna_get_option( 'intro_quote', 'True transformation begins when you choose to invest in yourself — not someday, but today.' );
$quote_cite  = anna_get_option( 'intro_quote_cite', 'Anna Baylis' );
$intro_body  = anna_get_option( 'intro_body', '' );
?>

<section class="anna-intro anna-section anna-section--lg" id="intro" aria-labelledby="intro-heading">
	<div class="anna-container">
		<div class="anna-intro__inner">

			<!-- Pull quote column -->
			<div class="anna-intro__quote-col anna-reveal--left">
				<span class="anna-eyebrow"><?php echo esc_html( anna_get_option( 'intro_eyebrow', __( 'My Approach', 'anna-baylis' ) ) ); ?></span>
				<blockquote class="anna-intro__quote">
					<p><?php echo esc_html( $quote ); ?></p>
					<?php if ( $quote_cite ) : ?>
						<cite>— <?php echo esc_html( $quote_cite ); ?></cite>
					<?php endif; ?>
				</blockquote>
			</div>

			<!-- Body text column -->
			<div class="anna-intro__body anna-reveal">
				<h2 id="intro-heading" class="anna-sr-only"><?php esc_html_e( 'My Approach', 'anna-baylis' ); ?></h2>

				<?php if ( $intro_body ) : ?>
					<?php echo wp_kses_post( $intro_body ); ?>
				<?php else : ?>
					<p>With over a decade of experience in holistic coaching and personal development, I specialise in guiding ambitious professionals toward authentic, lasting fulfilment.</p>
					<p>My approach combines evidence-based coaching methodologies with intuitive guidance, creating a unique experience that addresses both the practical and emotional dimensions of personal growth.</p>
					<p>Every journey is deeply personal. Together, we'll craft a tailored path that honours your unique story while building the mindset, habits, and clarity needed to create the life you envision.</p>
				<?php endif; ?>
			</div>

		</div>
	</div>
</section>
