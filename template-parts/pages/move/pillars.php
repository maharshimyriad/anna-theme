<?php
/**
 * Template part: Move page pillars.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = anna_get_option( 'move_pg_pillars_heading' );

$pillars = array();
for ( $i = 1; $i <= 4; $i++ ) {
	$title = anna_get_option( 'move_pg_pillar_' . $i . '_title' );
	$body  = anna_get_option( 'move_pg_pillar_' . $i . '_body' );
	if ( $title || $body ) {
		$pillars[] = array( 'title' => $title, 'body' => $body );
	}
}
?>

<section class="anna-section anna-section--md anna-move-pillars">
	<div class="anna-container anna-container--narrow">
		<div class="anna-move-pillars__header" data-reveal="fade-up">
			<?php if ( $heading ) : ?>
				<h2 class="anna-heading-2 anna-text-primary"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $pillars ) ) : ?>
			<div class="anna-move-pillars__list">
				<?php foreach ( $pillars as $index => $pillar ) : ?>
					<div class="anna-move-pillars__item" data-reveal="fade-up" data-reveal-delay="<?php echo esc_attr( $index * 0.1 ); ?>">
						<?php if ( ! empty( $pillar['title'] ) ) : ?>
							<h3 class="anna-heading-4 anna-text-primary"><?php echo esc_html( $pillar['title'] ); ?></h3>
						<?php endif; ?>
						<?php if ( ! empty( $pillar['body'] ) ) : ?>
							<div class="anna-body-text">
								<?php echo wpautop( esc_html( $pillar['body'] ) ); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
