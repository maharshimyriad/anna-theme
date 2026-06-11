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

$section_data = anna_get_about_section_content();
$heading      = $section_data['heading'];
$body         = $section_data['body'];
$quote        = $section_data['quote'];
$image_id     = $section_data['image_id'];
$badge_num    = $section_data['badge_number'];
$badge_text   = $section_data['badge_text'];
$expertise    = $section_data['expertise'];
$cta_text     = $section_data['cta_text'];
$cta_url      = $section_data['cta_url'];
?>

<section class="anna-about anna-section anna-section--lg" id="about" aria-labelledby="about-heading">
	<div class="anna-container">
		<div class="anna-about__inner">
			<div class="anna-about__visual anna-reveal--left">
				<figure class="anna-about__image-wrap">
					<?php if ( $image_id ) : ?>
						<?php anna_responsive_image( $image_id, 'full', 'anna-img-cover', true ); ?>
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
				<?php if ( $section_data['eyebrow'] ) : ?>
					<span class="anna-eyebrow"><?php echo esc_html( $section_data['eyebrow'] ); ?></span>
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
