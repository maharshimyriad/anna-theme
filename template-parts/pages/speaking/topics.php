<?php
/**
 * Speaking topics section.
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

<section id="speaking-topics" class="anna-speaking-section anna-speaking-topics">
	<div class="anna-container anna-container--max">
		<header class="anna-speaking-section__header">
			<p class="anna-speaking-eyebrow"><?php echo esc_html( $speaking['topics_eyebrow'] ?? '' ); ?></p>
			<h2 class="anna-speaking-section__heading">
				<span><?php echo esc_html( $speaking['topics_heading_primary'] ?? '' ); ?></span>
				<span><?php echo esc_html( $speaking['topics_heading_secondary'] ?? '' ); ?></span>
			</h2>
			<?php if ( ! empty( $speaking['topics_intro'] ) ) : ?>
				<p class="anna-speaking-section__intro"><?php echo esc_html( $speaking['topics_intro'] ); ?></p>
			<?php endif; ?>
		</header>

		<div class="anna-speaking-topics__grid">
			<?php foreach ( $speaking['topic_items'] as $item ) : ?>
				<article class="anna-speaking-topic-card">
					<span class="anna-speaking-topic-card__icon" aria-hidden="true"><?php echo wp_kses( anna_speaking_icon( $item['icon'] ?? 'circle' ), anna_allowed_svg_tags() ); ?></span>
					<h3><?php echo esc_html( $item['title'] ?? '' ); ?></h3>
					<p><?php echo esc_html( $item['body'] ?? '' ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
