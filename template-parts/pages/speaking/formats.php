<?php
/**
 * Speaking talk formats section.
 *
 * @package Anna_Baylis
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$speaking = get_query_var( 'anna_speaking_page_content', array() );
if ( empty( $speaking ) ) {
	$speaking = anna_get_speaking_page_content();
}
?>

<section class="anna-speaking-section anna-speaking-formats">
	<div class="anna-container anna-container--max">
		<header class="anna-speaking-section__header">
			<p class="anna-speaking-eyebrow"><?php echo esc_html( $speaking['formats_eyebrow'] ?? '' ); ?></p>
			<h2 class="anna-speaking-section__heading">
				<span><?php echo esc_html( $speaking['formats_heading_primary'] ?? '' ); ?></span>
				<span><?php echo esc_html( $speaking['formats_heading_secondary'] ?? '' ); ?></span>
			</h2>
		</header>

		<div class="anna-speaking-formats__grid">
			<?php foreach ( $speaking['format_items'] as $item ) : ?>
				<article class="anna-speaking-format-card">
					<span class="anna-speaking-format-card__number"><?php echo esc_html( $item['number'] ?? '' ); ?></span>
					<h3><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
					<p><?php echo esc_html( $item['body'] ?? '' ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>

		<?php if ( ! empty( $speaking['suited_items'] ) ) : ?>
			<div class="anna-speaking-suited">
				<h3><?php echo esc_html( $speaking['suited_heading'] ?? '' ); ?></h3>
				<ul>
					<?php foreach ( $speaking['suited_items'] as $item ) : ?>
						<li><?php echo esc_html( $item['text'] ?? '' ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	</div>
</section>
