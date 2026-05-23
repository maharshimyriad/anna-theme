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

$heading    = anna_get_option( 'about_heading', 'Meet <em>Anna Baylis</em>' );
$body       = anna_get_option( 'about_body', '<p>With a passion for authentic transformation and over a decade of experience, I help ambitious individuals bridge the gap between where they are and where they truly want to be.</p><p>My background in psychology, wellness, and executive coaching enables me to offer a unique, evidence-based yet deeply intuitive approach to personal growth.</p>' );
$quote      = anna_get_option( 'about_quote', 'I believe everyone has the innate capacity for extraordinary growth — they just need the right guide and the courage to begin.' );
$image_id   = anna_get_option( 'about_image_id', '' );
$badge_num  = anna_get_option( 'about_badge_number', '12+' );
$badge_text = anna_get_option( 'about_badge_text', __( 'Years Experience', 'anna-baylis' ) );

$expertise = anna_get_option( 'about_expertise', array() );
if ( empty( $expertise ) ) {
	$expertise = array(
		'Life Coaching',
		'Executive Coaching',
		'Mindfulness',
		'NLP Practitioner',
		'Wellness Strategy',
		'Group Facilitation',
	);
}
?>

<section class="anna-about anna-section anna-section--lg anna-section--soft" id="about" aria-labelledby="about-heading">
	<div class="anna-container">
		<div class="anna-about__inner">

			<!-- Image column -->
			<div class="anna-about__visual anna-reveal--left">
				<figure class="anna-about__image-wrap">
					<?php
					if ( $image_id ) {
						anna_responsive_image( $image_id, 'anna-portrait', 'anna-img-cover', true );
					} else {
						echo '<div style="width:100%;height:100%;background:linear-gradient(135deg, var(--color-bg-muted), var(--color-bg-soft));display:flex;align-items:center;justify-content:center;"><span style="font-size:var(--text-xl);color:var(--color-text-light);">Portrait</span></div>';
					}
					?>
				</figure>
				<div class="anna-about__shape" aria-hidden="true"></div>
				<div class="anna-about__badge">
					<span class="anna-about__badge-number"><?php echo esc_html( $badge_num ); ?></span>
					<span class="anna-about__badge-text"><?php echo esc_html( $badge_text ); ?></span>
				</div>
			</div>

			<!-- Content column -->
			<div class="anna-about__content anna-reveal">
				<span class="anna-eyebrow"><?php echo esc_html( anna_get_option( 'about_eyebrow', __( 'About Me', 'anna-baylis' ) ) ); ?></span>
				<h2 class="anna-about__heading" id="about-heading"><?php echo wp_kses_post( $heading ); ?></h2>

				<div class="anna-about__body">
					<?php echo wp_kses_post( $body ); ?>
				</div>

				<?php if ( $quote ) : ?>
					<blockquote class="anna-about__quote">
						<p><?php echo esc_html( $quote ); ?></p>
					</blockquote>
				<?php endif; ?>

				<!-- Expertise badges -->
				<?php if ( ! empty( $expertise ) ) : ?>
					<div>
						<p class="anna-about__expertise-label"><?php esc_html_e( 'Areas of Expertise', 'anna-baylis' ); ?></p>
						<ul class="anna-tag-list" role="list">
							<?php foreach ( $expertise as $tag ) : ?>
								<li class="anna-tag-list__item">
									<span class="anna-badge anna-badge--primary"><?php echo esc_html( $tag ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<div>
					<?php
					anna_cta_button(
						'secondary',
						anna_get_option( 'about_cta_text', __( 'More About Me', 'anna-baylis' ) ),
						anna_get_option( 'about_cta_url', '#' ),
						'anna-btn--lg'
					);
					?>
				</div>
			</div>

		</div>
	</div>
</section>
