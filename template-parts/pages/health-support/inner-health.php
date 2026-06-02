<?php
/**
 * Template part: Health Support page inner health.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$data = get_query_var( 'anna_health_support_page_content' );
$heading = $data['inner_heading'] ?? '';
$body    = $data['inner_body'] ?? '';
$image_id = $data['inner_image_id'] ?? 0;
?>

<section class="anna-section anna-section--md anna-hs-inner">
	<div class="anna-container">
		<div class="anna-grid anna-grid--split-6-6 anna-flex--center">
			<div class="anna-hs-inner__text" data-reveal="fade-right">
				<?php if ( $heading ) : ?>
					<h2 class="anna-heading-2 anna-text-primary"><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>
				<?php if ( $body ) : ?>
					<div class="anna-body-text">
						<?php echo wpautop( esc_html( $body ) ); ?>
					</div>
				<?php endif; ?>
			</div>
			
			<?php if ( $image_id ) : ?>
				<div class="anna-hs-inner__image" data-reveal="fade-left">
					<?php echo wp_get_attachment_image( $image_id, 'large' ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
