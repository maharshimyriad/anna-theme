<?php
/**
 * About page "What people say" section.
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

$items = (array) ( $about['people_items'] ?? array() );
?>

<section class="anna-about-page-section anna-about-page-people">
	<div class="anna-container">
		<div class="anna-about-page-people__header">
			<?php if ( ! empty( $about['people_eyebrow'] ) ) : ?>
				<p class="anna-about-page-people__eyebrow"><?php echo esc_html( $about['people_eyebrow'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $about['people_heading'] ) ) : ?>
				<h2 class="anna-about-page-people__heading"><?php echo esc_html( $about['people_heading'] ); ?></h2>
			<?php endif; ?>

			<?php if ( ! empty( $about['people_body'] ) ) : ?>
				<p class="anna-about-page-people__intro"><?php echo esc_html( $about['people_body'] ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $items ) ) : ?>
			<div class="anna-about-page-people__grid">
				<?php foreach ( $items as $item ) : ?>
					<?php
					$initials = (string) ( $item['initials'] ?? '' );
					$title    = (string) ( $item['title'] ?? '' );
					$org      = (string) ( $item['org'] ?? '' );

					if ( '' === trim( $title ) && '' === trim( $org ) ) {
						continue;
					}
					?>
					<article class="anna-about-page-people-card">
						<?php if ( '' !== trim( $initials ) ) : ?>
							<div class="anna-about-page-people-card__badge" aria-hidden="true">
								<?php echo esc_html( $initials ); ?>
							</div>
						<?php endif; ?>
						<div class="anna-about-page-people-card__content">
							<?php if ( '' !== trim( $title ) ) : ?>
								<h3 class="anna-about-page-people-card__title"><?php echo esc_html( $title ); ?></h3>
							<?php endif; ?>
							<?php if ( '' !== trim( $org ) ) : ?>
								<p class="anna-about-page-people-card__org"><?php echo esc_html( $org ); ?></p>
							<?php endif; ?>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

