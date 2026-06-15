<?php
/**
 * Contact page: hero section.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$contact = get_query_var( 'anna_contact_page_content', array() );
if ( empty( $contact ) ) {
	$contact = anna_get_contact_page_content();
}

$has_image = ! empty( $contact['hero_image_id'] );
?>

<section
	class="anna-hero-section anna-contact-page-hero<?php echo $has_image ? ' anna-contact-page-hero--has-image' : ''; ?>"
	<?php if ( $has_image ) : ?>
		style="background-image:url('<?php echo esc_url( anna_responsive_image_url( absint( $contact['hero_image_id'] ), 'full' ) ); ?>');"
	<?php endif; ?>
>

	<div class="anna-container anna-container--max">
		<div class="anna-contact-page-hero__content anna-reveal">
			<?php if ( ! empty( $contact['hero_eyebrow'] ) ) : ?>
				<p class="anna-contact-page-hero__eyebrow"><?php echo esc_html( $contact['hero_eyebrow'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $contact['hero_heading'] ) ) : ?>
				<h1 class="anna-contact-page-hero__heading"><?php echo wp_kses_post( nl2br( esc_html( $contact['hero_heading'] ) ) ); ?></h1>
			<?php endif; ?>
		</div>
	</div>
</section>
