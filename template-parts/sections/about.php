<?php
/**
 * Template part: About Section
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading    = anna_get_option( 'about_heading', 'Olympian. Life Coach. Motivational Speaker.' );
$body       = anna_get_option( 'about_body', '' );
$quote      = anna_get_option( 'about_quote', '' );
$image_id   = anna_get_option( 'about_image_id', '' );
$badge_num  = anna_get_option( 'about_badge_number', '' );
$badge_text = anna_get_option( 'about_badge_text', '' );
$expertise  = anna_get_lines_option( 'about_expertise_text', array() );
$cta_text   = anna_get_option( 'about_cta_text', '' );
$cta_url    = anna_get_option( 'about_cta_url', '#' );
?>

<section class="anna-about anna-section anna-section--lg" id="about" aria-labelledby="about-heading">
	<div class="anna-container">
		<div class="anna-about__inner">
			<div class="anna-about__visual anna-reveal--left">
				<figure class="anna-about__image-wrap">
					<?php if ( $image_id ) : ?>
						<?php anna_responsive_image( $image_id, 'anna-portrait', 'anna-img-cover', true ); ?>
					<?php endif; ?>
				</figure>
				<?php if ( $badge_num || $badge_text ) : ?>
					<div class="anna-about__badge">
						<?php if ( $badge_num ) : ?><span class="anna-about__badge-number"><?php echo esc_html( $badge_num ); ?></span><?php endif; ?>
						<?php if ( $badge_text ) : ?><span class="anna-about__badge-text"><?php echo esc_html( $badge_text ); ?></span><?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="anna-about__content anna-reveal">
				<?php if ( anna_get_option( 'about_eyebrow', '' ) ) : ?>
					<span class="anna-eyebrow"><?php echo esc_html( anna_get_option( 'about_eyebrow', '' ) ); ?></span>
				<?php endif; ?>
				<h2 class="anna-about__heading" id="about-heading"><?php echo wp_kses_post( $heading ); ?></h2>

				<div class="anna-about__body"><?php echo wp_kses_post( $body ); ?></div>

				<?php if ( $quote ) : ?>
					<blockquote class="anna-about__quote"><p><?php echo esc_html( $quote ); ?></p></blockquote>
				<?php endif; ?>

				<?php if ( ! empty( $expertise ) ) : ?>
					<ul class="anna-tag-list" role="list">
						<?php foreach ( $expertise as $tag ) : ?>
							<li class="anna-tag-list__item"><span class="anna-badge anna-badge--primary"><?php echo esc_html( $tag ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( $cta_text ) : ?>
					<div>
						<a href="<?php echo esc_url( $cta_url ); ?>" class="anna-btn anna-btn--secondary anna-btn--lg"><?php echo esc_html( $cta_text ); ?></a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
