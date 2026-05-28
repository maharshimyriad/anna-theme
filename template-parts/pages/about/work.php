<?php
/**
 * About page "How I work" section.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$about = get_query_var( 'anna_about_page_content', array() );
if ( empty( $about ) ) {
	$about = anna_get_about_page_content();
}

$cards = array();
for ( $i = 1; $i <= 4; $i++ ) {
	$title = $about[ 'work_card_' . $i . '_title' ] ?? '';
	$body  = $about[ 'work_card_' . $i . '_body' ] ?? '';
	if ( '' !== trim( (string) $title ) || '' !== trim( (string) $body ) ) {
		$cards[] = array(
			'title' => (string) $title,
			'body'  => (string) $body,
		);
	}
}
?>

<section class="anna-about-page-section anna-about-page-work">
	<div class="anna-container">
		<div class="anna-about-page-work__grid">
			<div class="anna-about-page-work__content">
				<?php if ( ! empty( $about['work_eyebrow'] ) ) : ?>
					<p class="anna-about-page-work__eyebrow"><?php echo esc_html( $about['work_eyebrow'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $about['work_heading'] ) ) : ?>
					<h2 class="anna-about-page-work__heading"><?php echo esc_html( $about['work_heading'] ); ?></h2>
				<?php endif; ?>

				<?php if ( ! empty( $about['work_body'] ) ) : ?>
					<div class="anna-about-page-work__copy"><?php echo wp_kses_post( wpautop( (string) $about['work_body'] ) ); ?></div>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $cards ) ) : ?>
				<div class="anna-about-page-work__cards">
					<?php foreach ( $cards as $card ) : ?>
						<article class="anna-about-page-work-card">
							<?php if ( '' !== trim( $card['title'] ) ) : ?>
								<h3 class="anna-about-page-work-card__title"><?php echo esc_html( $card['title'] ); ?></h3>
							<?php endif; ?>
							<?php if ( '' !== trim( $card['body'] ) ) : ?>
								<p class="anna-about-page-work-card__body"><?php echo esc_html( $card['body'] ); ?></p>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

