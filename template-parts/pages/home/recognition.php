<?php
/**
 * Template part: Recognition / Problem Section
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading     = anna_get_option( 'recognition_heading', 'Does This Sound Like You?' );
$description = anna_get_option( 'recognition_description', 'These are the challenges my clients face before we begin working together. You\'re not alone in this.' );

$items = anna_get_option( 'recognition_items', array() );
if ( empty( $items ) ) {
	$items = array(
		array(
			'icon' => 'mindset',
			'text' => 'Feeling stuck in your career despite external success',
		),
		array(
			'icon' => 'wellness',
			'text' => 'Struggling to balance ambition with wellbeing',
		),
		array(
			'icon' => 'growth',
			'text' => 'Lacking clarity about your next chapter in life',
		),
		array(
			'icon' => 'coaching',
			'text' => 'Wanting deeper, more meaningful relationships',
		),
		array(
			'icon' => 'workshop',
			'text' => 'Feeling disconnected from your true purpose',
		),
	);
}
?>

<section class="anna-recognition anna-section anna-section--md anna-section--soft" id="recognition" aria-labelledby="recognition-heading">
	<div class="anna-container">
		<div class="anna-recognition__inner">

			<!-- Left: Heading -->
			<div class="anna-recognition__header anna-reveal--left">
				<span class="anna-eyebrow"><?php echo esc_html( anna_get_option( 'recognition_eyebrow', __( 'Recognise Yourself?', 'anna-baylis' ) ) ); ?></span>
				<h2 class="anna-recognition__heading" id="recognition-heading">
					<?php echo esc_html( $heading ); ?>
				</h2>
				<p class="anna-recognition__description">
					<?php echo esc_html( $description ); ?>
				</p>
			</div>

			<!-- Right: Items card -->
			<div class="anna-recognition__card anna-reveal">
				<ul class="anna-recognition__list" role="list">
					<?php foreach ( $items as $item ) : ?>
						<li class="anna-recognition__item">
							<span class="anna-recognition__item-icon" aria-hidden="true">
								<?php anna_service_icon( $item['icon'] ?? 'default' ); ?>
							</span>
							<span class="anna-recognition__item-text">
								<?php echo esc_html( $item['text'] ); ?>
							</span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

		</div>
	</div>
</section>
