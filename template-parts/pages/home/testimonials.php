<?php
/**
 * Template part: Testimonials Section
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$section_data = anna_get_testimonials_section_content();
$heading      = $section_data['heading'];
$heading_main = $heading;
$heading_sub  = '';

if ( false !== stripos( $heading, ' Google reviews' ) ) {
	$heading_main = trim( str_ireplace( 'Google reviews', '', $heading ) );
	$heading_sub  = 'Google reviews';
}

$static       = array(
	array(
		'quote'  => 'Working with Anna has been profoundly healing. She has helped me build my own inner support system by reconnecting with my younger self and learning how to meet myself with love, compassion and understanding.',
		'name'   => 'Rebecca Browne',
		'role'   => '3 months ago',
		'rating' => 5,
	),
	array(
		'quote'  => 'Anna has an extraordinary ability to help you distil your core values and actually live them, while uncovering parts of yourself you did not even know existed. She is a guide and a healer.',
		'name'   => 'Mel',
		'role'   => '6 months ago',
		'rating' => 5,
	),
	array(
		'quote'  => 'Anna has a remarkable ability to create a safe, supportive space where I felt comfortable exploring the different parts that make up who I am. Every session was both grounding and empowering.',
		'name'   => 'Deane Voladimos',
		'role'   => '29 days ago',
		'rating' => 5,
	),
	);
?>

<section class="anna-testimonials-section anna-section anna-section--lg" id="testimonials" aria-labelledby="testimonials-heading">
	<div class="anna-container">
		<div class="anna-testimonials-section__header anna-reveal">
			<div>
				<?php if ( $section_data['eyebrow'] ) : ?>
					<span class="anna-eyebrow"><?php echo esc_html( $section_data['eyebrow'] ); ?></span>
				<?php endif; ?>
				<h2 class="anna-testimonials-section__heading" id="testimonials-heading">
					<span class="anna-testimonials-section__heading-main"><?php echo esc_html( $heading_main ); ?></span>
					<?php if ( $heading_sub ) : ?>
						<span class="anna-testimonials-section__heading-sub"><?php echo esc_html( $heading_sub ); ?></span>
					<?php endif; ?>
				</h2>
				<?php if ( $section_data['summary'] ) : ?>
					<p class="anna-testimonials-section__summary">
						<svg width="100" height="20" viewBox="0 0 100 20" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M9.60889 1.91642C9.68358 1.76545 9.8374 1.66992 10.0058 1.66992C10.1742 1.66992 10.328 1.76545 10.4027 1.91642L12.3329 5.8275C12.591 6.35005 13.0892 6.71248 13.6657 6.79712L17.9823 7.42905C18.1492 7.45323 18.2879 7.57009 18.34 7.73047C18.3921 7.89086 18.3487 8.06695 18.228 8.18469L15.1062 11.2256C14.6883 11.633 14.4974 12.22 14.5957 12.7954L15.3327 17.0918C15.3621 17.2587 15.2939 17.4277 15.1568 17.5273C15.0198 17.6269 14.838 17.6396 14.6884 17.5599L10.8297 15.5304C10.3136 15.2593 9.69719 15.2593 9.18106 15.5304L5.32314 17.5599C5.17368 17.6391 4.99222 17.6262 4.85545 17.5267C4.71869 17.4272 4.65051 17.2584 4.67973 17.0918L5.41589 12.7962C5.51461 12.2205 5.32366 11.6331 4.90534 11.2256L1.78357 8.18552C1.66184 8.0679 1.61776 7.89116 1.67 7.73013C1.72224 7.56909 1.86166 7.45192 2.02924 7.42821L6.34507 6.79712C6.92222 6.71313 7.42118 6.35058 7.67951 5.8275L9.60889 1.91642Z" fill="#A1C842" stroke="#A1C842" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M29.6089 1.91642C29.6836 1.76545 29.8374 1.66992 30.0058 1.66992C30.1742 1.66992 30.328 1.76545 30.4027 1.91642L32.3329 5.8275C32.591 6.35005 33.0892 6.71248 33.6657 6.79712L37.9823 7.42905C38.1492 7.45323 38.2879 7.57009 38.34 7.73047C38.3921 7.89086 38.3487 8.06695 38.228 8.18469L35.1062 11.2256C34.6883 11.633 34.4974 12.22 34.5957 12.7954L35.3327 17.0918C35.3621 17.2587 35.2939 17.4277 35.1568 17.5273C35.0198 17.6269 34.838 17.6396 34.6884 17.5599L30.8297 15.5304C30.3136 15.2593 29.6972 15.2593 29.1811 15.5304L25.3231 17.5599C25.1737 17.6391 24.9922 17.6262 24.8555 17.5267C24.7187 17.4272 24.6505 17.2584 24.6797 17.0918L25.4159 12.7962C25.5146 12.2205 25.3237 11.6331 24.9053 11.2256L21.7836 8.18552C21.6618 8.0679 21.6178 7.89116 21.67 7.73013C21.7222 7.56909 21.8617 7.45192 22.0292 7.42821L26.3451 6.79712C26.9222 6.71313 27.4212 6.35058 27.6795 5.8275L29.6089 1.91642Z" fill="#A1C842" stroke="#A1C842" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M49.6089 1.91642C49.6836 1.76545 49.8374 1.66992 50.0058 1.66992C50.1742 1.66992 50.328 1.76545 50.4027 1.91642L52.3329 5.8275C52.591 6.35005 53.0892 6.71248 53.6657 6.79712L57.9823 7.42905C58.1492 7.45323 58.2879 7.57009 58.34 7.73047C58.3921 7.89086 58.3487 8.06695 58.228 8.18469L55.1062 11.2256C54.6883 11.633 54.4974 12.22 54.5957 12.7954L55.3327 17.0918C55.3621 17.2587 55.2939 17.4277 55.1568 17.5273C55.0198 17.6269 54.838 17.6396 54.6884 17.5599L50.8297 15.5304C50.3136 15.2593 49.6972 15.2593 49.1811 15.5304L45.3231 17.5599C45.1737 17.6391 44.9922 17.6262 44.8555 17.5267C44.7187 17.4272 44.6505 17.2584 44.6797 17.0918L45.4159 12.7962C45.5146 12.2205 45.3237 11.6331 44.9053 11.2256L41.7836 8.18552C41.6618 8.0679 41.6178 7.89116 41.67 7.73013C41.7222 7.56909 41.8617 7.45192 42.0292 7.42821L46.3451 6.79712C46.9222 6.71313 47.4212 6.35058 47.6795 5.8275L49.6089 1.91642Z" fill="#A1C842" stroke="#A1C842" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M69.6089 1.91642C69.6836 1.76545 69.8374 1.66992 70.0058 1.66992C70.1742 1.66992 70.328 1.76545 70.4027 1.91642L72.3329 5.8275C72.591 6.35005 73.0892 6.71248 73.6657 6.79712L77.9823 7.42905C78.1492 7.45323 78.2879 7.57009 78.34 7.73047C78.3921 7.89086 78.3487 8.06695 78.228 8.18469L75.1062 11.2256C74.6883 11.633 74.4974 12.22 74.5957 12.7954L75.3327 17.0918C75.3621 17.2587 75.2939 17.4277 75.1568 17.5273C75.0198 17.6269 74.838 17.6396 74.6884 17.5599L70.8297 15.5304C70.3136 15.2593 69.6972 15.2593 69.1811 15.5304L65.3231 17.5599C65.1737 17.6391 64.9922 17.6262 64.8555 17.5267C64.7187 17.4272 64.6505 17.2584 64.6797 17.0918L65.4159 12.7962C65.5146 12.2205 65.3237 11.6331 64.9053 11.2256L61.7836 8.18552C61.6618 8.0679 61.6178 7.89116 61.67 7.73013C61.7222 7.56909 61.8617 7.45192 62.0292 7.42821L66.3451 6.79712C66.9222 6.71313 67.4212 6.35058 67.6795 5.8275L69.6089 1.91642Z" fill="#A1C842" stroke="#A1C842" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M89.6089 1.91642C89.6836 1.76545 89.8374 1.66992 90.0058 1.66992C90.1742 1.66992 90.328 1.76545 90.4027 1.91642L92.3329 5.8275C92.591 6.35005 93.0892 6.71248 93.6657 6.79712L97.9823 7.42905C98.1492 7.45323 98.2879 7.57009 98.34 7.73047C98.3921 7.89086 98.3487 8.06695 98.228 8.18469L95.1062 11.2256C94.6883 11.633 94.4974 12.22 94.5957 12.7954L95.3327 17.0918C95.3621 17.2587 95.2939 17.4277 95.1568 17.5273C95.0198 17.6269 94.838 17.6396 94.6884 17.5599L90.8297 15.5304C90.3136 15.2593 89.6972 15.2593 89.1811 15.5304L85.3231 17.5599C85.1737 17.6391 84.9922 17.6262 84.8555 17.5267C84.7187 17.4272 84.6505 17.2584 84.6797 17.0918L85.4159 12.7962C85.5146 12.2205 85.3237 11.6331 84.9053 11.2256L81.7836 8.18552C81.6618 8.0679 81.6178 7.89116 81.67 7.73013C81.7222 7.56909 81.8617 7.45192 82.0292 7.42821L86.3451 6.79712C86.9222 6.71313 87.4212 6.35058 87.6795 5.8275L89.6089 1.91642Z" fill="#A1C842" stroke="#A1C842" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>

						<?php echo esc_html( $section_data['summary'] ); ?>
				</p>
				<?php endif; ?>
			</div>
		</div>

		<div class="anna-testimonials-inner anna-testimonials-inner--static anna-stagger" role="list">
			<?php foreach ( $static as $testimonial ) : ?>
				<figure class="anna-testimonial" role="listitem">
					<div class="anna-testimonial__rating"><?php echo anna_star_rating( $testimonial['rating'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					<blockquote class="anna-testimonial__quote"><p><?php echo esc_html( $testimonial['quote'] ); ?></p></blockquote>
					<figcaption class="anna-testimonial__author">
						<cite class="anna-testimonial__name"><?php echo esc_html( $testimonial['name'] ); ?></cite>
						<span class="anna-testimonial__role"><?php echo esc_html( $testimonial['role'] ); ?></span>
					</figcaption>
				</figure>
			<?php endforeach; ?>
		</div>

		<?php if ( $section_data['cta_text'] ) : ?>
			<div class="anna-testimonials-section__footer anna-reveal">
				<a href="<?php echo esc_url( $section_data['cta_url'] ); ?>" class="anna-btn anna-btn--ghost anna-btn--lg anna-testimonials-section__link">
					<?php echo esc_html( $section_data['cta_text'] ); ?>
				</a>
			</div>
		<?php endif; ?>
			<?php echo do_shortcode('[brb_collection id="4755"]'); ?>
	</div>

</section>
