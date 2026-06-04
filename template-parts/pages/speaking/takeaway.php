<?php
/**
 * Speaking "What audiences take away" section.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$speaking = get_query_var( 'anna_speaking_page_content', array() );
if ( empty( $speaking ) ) {
	$speaking = anna_get_speaking_page_content();
}

$items = isset( $speaking['takeaway_items'] ) && is_array( $speaking['takeaway_items'] ) ? $speaking['takeaway_items'] : array();
?>

<section class="anna-speaking-page-section anna-speaking-page-takeaway">
	<div class="anna-container anna-container--max">
		<div class="anna-speaking-page-takeaway__grid">
			<div class="anna-speaking-page-takeaway__content">
				<?php if ( ! empty( $speaking['takeaway_eyebrow'] ) ) : ?>
					<p class="anna-speaking-page-takeaway__eyebrow"><?php echo esc_html( $speaking['takeaway_eyebrow'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $speaking['takeaway_heading'] ) ) : ?>
					<h2 class="anna-speaking-page-takeaway__heading"><?php echo esc_html( $speaking['takeaway_heading'] ); ?></h2>
				<?php endif; ?>

				<?php if ( ! empty( $speaking['takeaway_body'] ) ) : ?>
					<div class="anna-speaking-page-takeaway__copy"><?php echo wp_kses_post( wpautop( (string) $speaking['takeaway_body'] ) ); ?></div>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $items ) ) : ?>
				<ul class="anna-speaking-page-takeaway__list" role="list">
					<?php foreach ( $items as $item ) : ?>
						<?php
						$text = is_array( $item ) ? (string) ( $item['text'] ?? '' ) : (string) $item;
						if ( '' === trim( $text ) ) {
							continue;
						}
						?>
						<li class="anna-speaking-page-takeaway__item">
							<span class="anna-speaking-page-takeaway__icon" aria-hidden="true">
								<svg width="14" height="11" viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M1 5.5L5 9.5L13 1.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</span>
							<span class="anna-speaking-page-takeaway__text"><?php echo wp_kses_post( anna_format_speaking_emphasis_text( $text ) ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</section>
<section class="anna-speaking-page-section anna-speaking-page-bring">
    <div class="container">
		
		<div class="anna-speaking-page-takeaway__grid">
			 <div class="anna-speaking-page-takeaway__content">
            <span class="anna-speaking-page-formats__eyebrow">Book Anna to speak</span>

            <h2 class="anna-speaking-page-formats__heading">
				Let’s create something<br>
                <span>your audience won’t forget.</span>
            </h2>

            <div class="anna-speaking-page-bring__copy">
				<p>
                If you're looking for a speaker who brings authenticity,
                energy and practical insights to the stage someone whose
                story stops people in their tracks and whose tools actually
                work in real life I would love to hear from you.
            </p>

            <p>
                I tailor every talk to the audience and the goals of the
                event. Get in touch and let's have a conversation about what
                would work best for you.
            </p>
				 </div>
        </div>
			 <div class="enquiry-card">
            <h2>Enquire about speaking</h2>

            <p>
                Send me a message with details about your event and I'll get
                back to you within 48 hours.
            </p>

<a class="enquiry-card_btn" href="#contact">Send an enquiry</a>
            <div class="enquiry-card-contact">
                Or email me directly at
                <a href="mailto:info@annaluthi.com.au">
                    info@annaluthi.com.au
                </a>
                <br>
                I respond to all speaking enquiries personally.
            </div>
        </div>
		</div>
       

       
    </div>
</section>