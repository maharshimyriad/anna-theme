<?php
/**
 * Oasis "Choose your experience" pricing section.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$oasis = get_query_var( 'anna_oasis_page_content', array() );
if ( empty( $oasis ) ) {
	$oasis = anna_get_oasis_page_content();
}

$plans = isset( $oasis['choose_plan_items'] ) && is_array( $oasis['choose_plan_items'] ) ? $oasis['choose_plan_items'] : array();
?>

<section class="anna-oasis-page-section anna-oasis-page-choose">
	<div class="anna-container anna-container--max">
		<header class="anna-oasis-page-choose__header">
			<?php if ( ! empty( $oasis['choose_eyebrow'] ) ) : ?>
				<p class="anna-oasis-page-choose__eyebrow"><?php echo esc_html( $oasis['choose_eyebrow'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $oasis['choose_heading'] ) ) : ?>
				<h2 class="anna-oasis-page-choose__heading"><?php echo esc_html( $oasis['choose_heading'] ); ?></h2>
			<?php endif; ?>

			<?php if ( ! empty( $oasis['choose_intro'] ) ) : ?>
				<p class="anna-oasis-page-choose__intro"><?php echo esc_html( $oasis['choose_intro'] ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( ! empty( $plans ) ) : ?>
			<div class="anna-oasis-page-choose__grid">
				<?php foreach ( $plans as $plan ) : ?>
					<?php
					if ( ! is_array( $plan ) ) {
						continue;
					}
					$title    = trim( (string) ( $plan['title'] ?? '' ) );
					$featured = ! empty( $plan['featured'] );
					$features = isset( $plan['features'] ) && is_array( $plan['features'] ) ? $plan['features'] : array();
					if ( '' === $title ) {
						continue;
					}
					?>
					<article class="anna-oasis-page-plan<?php echo $featured ? ' anna-oasis-page-plan--featured' : ''; ?>">
						<?php if ( $featured && ! empty( $plan['badge'] ) ) : ?>
							<span class="anna-oasis-page-plan__badge"><?php echo esc_html( $plan['badge'] ); ?></span>
						<?php endif; ?>

						<h3 class="anna-oasis-page-plan__title"><?php echo esc_html( $title ); ?></h3>

						<?php if ( ! empty( $plan['price'] ) ) : ?>
							<p class="anna-oasis-page-plan__price">
								<strong><?php echo esc_html( $plan['price'] ); ?></strong>
								<?php if ( ! empty( $plan['price_suffix'] ) ) : ?>
									<span><?php echo esc_html( $plan['price_suffix'] ); ?></span>
								<?php endif; ?>
							</p>
						<?php endif; ?>

						<?php if ( ! empty( $plan['annual'] ) ) : ?>
							<p class="anna-oasis-page-plan__annual"><?php echo esc_html( $plan['annual'] ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $plan['founding'] ) ) : ?>
							<p class="anna-oasis-page-plan__founding"><?php echo esc_html( $plan['founding'] ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $features ) ) : ?>
							<ul class="anna-oasis-page-plan__features" role="list">
								<?php foreach ( $features as $feature ) : ?>
									<?php
									$text = is_array( $feature ) ? (string) ( $feature['text'] ?? '' ) : (string) $feature;
									if ( '' === trim( $text ) ) {
										continue;
									}
									?>
									<li><?php echo esc_html( $text ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $oasis['choose_footer'] ) ) : ?>
			<p class="anna-oasis-page-choose__footer"><?php echo esc_html( $oasis['choose_footer'] ); ?></p>
		<?php endif; ?>
	</div>
</section>
