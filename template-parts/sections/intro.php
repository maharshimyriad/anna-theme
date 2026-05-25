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

$intro_heading = anna_get_option( 'intro_heading', 'Real change. From the inside out.' );
$intro_body    = anna_get_option( 'intro_body', '' );
$quote         = anna_get_option( 'intro_quote', '' );
$quote_cite    = anna_get_option( 'intro_quote_cite', '' );
$items         = anna_get_lines_option(
	'recognition_items_text',
	array(
		'You feel stuck, disconnected or like you\'re going through the motions',
		'You know what you need to do but you\'re not doing it',
		'You\'ve tried therapy, programs and self-help and something still feels missing',
		'You put everyone else first and run on empty',
		'You sense there\'s more available to you but don\'t know how to access it',
		'You want to feel genuinely well, not just functional',
	)
);
?>

<section class="anna-intro anna-section anna-section--lg" id="intro" aria-labelledby="intro-heading">
	<div class="anna-container">
		<div class="anna-intro__inner<?php echo ( $show_intro && $show_recognition ) ? '' : ' anna-intro__inner--single'; ?>">
			<?php if ( $show_intro ) : ?>
				<div class="anna-intro__body anna-reveal--left">
					<?php if ( anna_get_option( 'intro_eyebrow', '' ) ) : ?>
						<span class="anna-eyebrow"><?php echo esc_html( anna_get_option( 'intro_eyebrow', '' ) ); ?></span>
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
					<?php if ( anna_get_option( 'recognition_eyebrow', '' ) ) : ?>
						<span class="anna-eyebrow"><?php echo esc_html( anna_get_option( 'recognition_eyebrow', '' ) ); ?></span>
					<?php endif; ?>
					<h3 class="anna-recognition__heading" id="recognition-heading"><?php echo esc_html( anna_get_option( 'recognition_heading', 'You might recognise yourself here' ) ); ?></h3>
					<?php if ( anna_get_option( 'recognition_description', '' ) ) : ?>
						<p class="anna-recognition__description"><?php echo esc_html( anna_get_option( 'recognition_description', '' ) ); ?></p>
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
