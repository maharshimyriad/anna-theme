<?php
/**
 * Template part: Approach / Recognition Section
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$show_intro       = anna_section_enabled( 'intro' );
$show_recognition = anna_section_enabled( 'recognition' );

if ( ! $show_intro && ! $show_recognition ) {
	return;
}

$section_data  = anna_get_intro_section_content();
$intro_heading = $section_data['intro_heading'];
$intro_body    = $section_data['intro_body'];
$quote         = $section_data['intro_quote'];
$quote_cite    = $section_data['intro_quote_cite'];
$items         = $section_data['recognition_items'];
?>

<section class="anna-intro anna-section anna-section--lg" id="intro" aria-labelledby="intro-heading">
	<div class="anna-container">
		<div class="anna-intro__inner<?php echo ( $show_intro && $show_recognition ) ? '' : ' anna-intro__inner--single'; ?>">
			<?php if ( $show_intro ) : ?>
				<div class="anna-intro__body anna-reveal--left">
					<?php if ( $section_data['intro_eyebrow'] ) : ?>
						<span class="anna-eyebrow"><?php echo esc_html( $section_data['intro_eyebrow'] ); ?></span>
					<?php endif; ?>

					<h2 id="intro-heading" class="anna-intro__heading"><?php echo wp_kses_post( $intro_heading ); ?></h2>
					<div class="anna-intro__copy"><?php echo wp_kses_post( $intro_body ); ?></div>

					<?php if ( $quote ) : ?>
						<blockquote class="anna-intro__quote">
							<p><?php echo esc_html( $quote ); ?></p>
							<?php if ( $quote_cite ) : ?>
								<cite><?php echo esc_html( $quote_cite ); ?></cite>
							<?php endif; ?>
						</blockquote>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $show_recognition ) : ?>
				<div class="anna-recognition__card anna-reveal">
					<?php if ( $section_data['recognition_eyebrow'] ) : ?>
						<span class="anna-eyebrow"><?php echo esc_html( $section_data['recognition_eyebrow'] ); ?></span>
					<?php endif; ?>
					<h3 class="anna-recognition__heading" id="recognition-heading"><?php echo esc_html( $section_data['recognition_heading'] ); ?></h3>
					<?php if ( $section_data['recognition_description'] ) : ?>
						<p class="anna-recognition__description"><?php echo esc_html( $section_data['recognition_description'] ); ?></p>
					<?php endif; ?>

					<ul class="anna-recognition__list" role="list">
						<?php foreach ( $items as $item ) : ?>
							<li class="anna-recognition__item">
								<span class="anna-recognition__item-dot" aria-hidden="true"></span>
								<span class="anna-recognition__item-text"><?php echo esc_html( $item ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
