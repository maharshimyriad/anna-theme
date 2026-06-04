<?php
/**
 * About page qualifications section (repeater cards).
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

$items = (array) ( $about['qualifications'] ?? array() );
?>

<section class="anna-about-page-section anna-about-page-qualifications">
	<div class="anna-container">
		<div class="anna-about-page-qualifications__header anna-reveal">
			<?php if ( ! empty( $about['qual_eyebrow'] ) ) : ?>
				<p class="anna-about-page-qualifications__eyebrow"><?php echo esc_html( $about['qual_eyebrow'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $about['qual_heading'] ) ) : ?>
				<h2 class="anna-about-page-qualifications__heading"><?php echo esc_html( $about['qual_heading'] ); ?></h2>
			<?php endif; ?>

			<?php if ( ! empty( $about['qual_body'] ) ) : ?>
				<p class="anna-about-page-qualifications__intro"><?php echo esc_html( $about['qual_body'] ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $items ) ) : ?>
			<div class="anna-about-page-qualifications__grid anna-stagger">
				<?php foreach ( $items as $item ) : ?>
					<?php
					$logo_id     = absint( $item['logo_id'] ?? 0 );
					$title       = (string) ( $item['title'] ?? '' );
					$description = (string) ( $item['description'] ?? '' );

					if ( '' === trim( $title ) && '' === trim( $description ) ) {
						continue;
					}
					?>
					<article class="anna-about-page-qualifications-card">
						<div class="anna-about-page-qualifications-card__logo" aria-hidden="true">
							<?php if ( $logo_id ) : ?>
								<?php echo wp_get_attachment_image( $logo_id, 'thumbnail', false, array( 'loading' => 'lazy' ) ); ?>
							<?php else : ?>
								<span class="anna-about-page-qualifications-card__logo-fallback"></span>
							<?php endif; ?>
						</div>
						<div class="anna-about-page-qualifications-card__content">
							<?php if ( '' !== trim( $title ) ) : ?>
								<h3 class="anna-about-page-qualifications-card__title"><?php echo esc_html( $title ); ?></h3>
							<?php endif; ?>
							<?php if ( '' !== trim( $description ) ) : ?>
								<p class="anna-about-page-qualifications-card__description"><?php echo esc_html( $description ); ?></p>
							<?php endif; ?>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
