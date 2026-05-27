<?php
/**
 * Template part: fixed About page design.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$about = anna_get_about_page_content();
?>

<section
	class="anna-about-page-hero"
	<?php if ( ! empty( $about['hero_image_id'] ) ) : ?>
		style="background-image:url('<?php echo esc_url( anna_responsive_image_url( absint( $about['hero_image_id'] ), 'full' ) ); ?>');"
	<?php endif; ?>
>
	<div class="anna-about-page-hero__overlay"></div>
	<div class="anna-container">
		<div class="anna-about-page-hero__content">
			<?php if ( $about['hero_eyebrow'] ) : ?>
				<span class="anna-about-page__eyebrow anna-about-page__eyebrow--light"><?php echo esc_html( $about['hero_eyebrow'] ); ?></span>
			<?php endif; ?>
			<h1 class="anna-about-page-hero__heading"><?php echo wp_kses_post( nl2br( $about['hero_heading'] ) ); ?></h1>
			<?php if ( $about['hero_subheading'] ) : ?>
				<p class="anna-about-page-hero__subheading"><?php echo esc_html( $about['hero_subheading'] ); ?></p>
			<?php endif; ?>
			<?php if ( $about['hero_description'] ) : ?>
				<p class="anna-about-page-hero__description"><?php echo esc_html( $about['hero_description'] ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="anna-about-page-section anna-about-page-story">
	<div class="anna-container">
		<div class="anna-about-page-story__grid">
			<?php if ( ! empty( $about['story_image_id'] ) ) : ?>
				<figure class="anna-about-page-story__media">
					<?php anna_responsive_image( absint( $about['story_image_id'] ), 'large', 'anna-img-cover' ); ?>
				</figure>
			<?php endif; ?>
			<div class="anna-about-page-story__content">
				<?php if ( $about['story_eyebrow'] ) : ?>
					<span class="anna-about-page__eyebrow"><?php echo esc_html( $about['story_eyebrow'] ); ?></span>
				<?php endif; ?>
				<h2 class="anna-about-page__heading"><?php echo wp_kses_post( nl2br( $about['story_heading'] ) ); ?></h2>
				<div class="anna-about-page__copy"><?php echo wp_kses_post( wpautop( $about['story_body'] ) ); ?></div>
			</div>
		</div>
	</div>
</section>

<section class="anna-about-page-section anna-about-page-rock">
	<div class="anna-container">
		<h2 class="anna-about-page__heading"><?php echo esc_html( $about['rock_heading'] ); ?></h2>
		<div class="anna-about-page-two-col">
			<div class="anna-about-page__copy"><?php echo wp_kses_post( wpautop( $about['rock_left_body'] ) ); ?></div>
			<div class="anna-about-page__copy"><?php echo wp_kses_post( wpautop( $about['rock_right_body'] ) ); ?></div>
		</div>
	</div>
</section>

<section class="anna-about-page-section anna-about-page-band">
	<div class="anna-container">
		<h2 class="anna-about-page__heading anna-about-page__heading--inline"><?php echo wp_kses_post( nl2br( $about['coach_heading'] ) ); ?></h2>
		<div class="anna-about-page-two-col">
			<div class="anna-about-page__copy"><?php echo wp_kses_post( wpautop( $about['coach_left_body'] ) ); ?></div>
			<div>
				<div class="anna-about-page__copy"><?php echo wp_kses_post( wpautop( $about['coach_right_body'] ) ); ?></div>
				<?php if ( $about['coach_quote'] ) : ?>
					<blockquote class="anna-about-page__quote"><?php echo esc_html( $about['coach_quote'] ); ?></blockquote>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>

<section class="anna-about-page-section anna-about-page-approach">
	<div class="anna-container">
		<?php if ( $about['approach_eyebrow'] ) : ?>
			<span class="anna-about-page__eyebrow"><?php echo esc_html( $about['approach_eyebrow'] ); ?></span>
		<?php endif; ?>
		<h2 class="anna-about-page__heading"><?php echo wp_kses_post( nl2br( $about['approach_heading'] ) ); ?></h2>
		<?php if ( $about['approach_intro'] ) : ?>
			<p class="anna-about-page__intro"><?php echo esc_html( $about['approach_intro'] ); ?></p>
		<?php endif; ?>
		<div class="anna-about-page-two-col">
			<div class="anna-about-page__copy"><?php echo wp_kses_post( wpautop( $about['approach_left_body'] ) ); ?></div>
			<div class="anna-about-page__copy"><?php echo wp_kses_post( wpautop( $about['approach_right_body'] ) ); ?></div>
		</div>
	</div>
</section>

<section class="anna-about-page-section anna-about-page-qualifications">
	<div class="anna-container">
		<h2 class="anna-about-page__heading"><?php echo esc_html( $about['qual_heading'] ); ?></h2>
		<?php if ( $about['qual_intro'] ) : ?>
			<p class="anna-about-page__intro"><?php echo esc_html( $about['qual_intro'] ); ?></p>
		<?php endif; ?>
		<?php if ( ! empty( $about['qual_items'] ) ) : ?>
			<ul class="anna-about-page-qualifications__list" role="list">
				<?php foreach ( $about['qual_items'] as $item ) : ?>
					<li><?php echo esc_html( $item ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>

<section class="anna-about-page-section anna-about-page-life">
	<div class="anna-container">
		<div class="anna-about-page-life__grid">
			<?php if ( ! empty( $about['life_image_id'] ) ) : ?>
				<figure class="anna-about-page-life__media">
					<?php anna_responsive_image( absint( $about['life_image_id'] ), 'large', 'anna-img-cover' ); ?>
				</figure>
			<?php endif; ?>
			<div class="anna-about-page-life__content">
				<?php if ( $about['life_eyebrow'] ) : ?>
					<span class="anna-about-page__eyebrow"><?php echo esc_html( $about['life_eyebrow'] ); ?></span>
				<?php endif; ?>
				<h2 class="anna-about-page__heading"><?php echo esc_html( $about['life_heading'] ); ?></h2>
				<div class="anna-about-page__copy"><?php echo wp_kses_post( wpautop( $about['life_body'] ) ); ?></div>
			</div>
		</div>
	</div>
</section>
