<?php
/**
 * Speaking "What audiences take away" section.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if (!defined("ABSPATH")) {
    exit();
}

$speaking = get_query_var("anna_speaking_page_content", []);
if (empty($speaking)) {
    $speaking = anna_get_speaking_page_content();
}

$items =
    isset($speaking["takeaway_items"]) && is_array($speaking["takeaway_items"])
        ? $speaking["takeaway_items"]
        : [];
?>

<section class="anna-speaking-page-section anna-speaking-page-takeaway">
	<div class="anna-container anna-container--max">
		<div class="anna-speaking-page-takeaway__grid">
			<div class="anna-speaking-page-takeaway__content anna-reveal--left">
				<?php if (!empty($speaking["takeaway_eyebrow"])): ?>
					<p class="anna-speaking-page-takeaway__eyebrow"><?php echo esc_html(
         $speaking["takeaway_eyebrow"],
     ); ?></p>
				<?php endif; ?>

				<?php if (!empty($speaking["takeaway_heading"])): ?>
					<h2 class="anna-speaking-page-takeaway__heading"><?php echo esc_html(
         $speaking["takeaway_heading"],
     ); ?></h2>
				<?php endif; ?>

				<?php if (!empty($speaking["takeaway_body"])): ?>
					<div class="anna-speaking-page-takeaway__copy"><?php echo wp_kses_post(
         wpautop((string) $speaking["takeaway_body"]),
     ); ?></div>
				<?php endif; ?>
			</div>

			<?php if (!empty($items)): ?>
				<ul class="anna-speaking-page-takeaway__list anna-stagger" role="list">
					<?php foreach ($items as $item): ?>
						<?php
      $text = is_array($item) ? (string) ($item["text"] ?? "") : (string) $item;
      if ("" === trim($text)) {
          continue;
      }
      ?>
						<li class="anna-speaking-page-takeaway__item">
							<span class="anna-speaking-page-takeaway__icon" aria-hidden="true">
								<svg width="14" height="11" viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M1 5.5L5 9.5L13 1.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</span>
							<span class="anna-speaking-page-takeaway__text"><?php echo wp_kses_post(
           anna_format_speaking_emphasis_text($text),
       ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</section>
<section class="anna-speaking-page-section anna-speaking-page-book">
	<div class="anna-container anna-container--max">
		<div class="anna-speaking-page-book__grid">

			<div class="anna-speaking-page-book__content anna-reveal--left">
				<?php if (!empty($speaking["book_eyebrow"])): ?>
					<p class="anna-speaking-page-book__eyebrow"><?php echo esc_html(
         $speaking["book_eyebrow"],
     ); ?></p>
				<?php endif; ?>

				<?php if (
        !empty($speaking["book_heading_line1"]) ||
        !empty($speaking["book_heading_line2"])
    ): ?>
					<h2 class="anna-speaking-page-book__heading">
						<?php if (!empty($speaking["book_heading_line1"])): ?>
							<?php echo esc_html($speaking["book_heading_line1"]); ?>
						<?php endif; ?>
						<?php if (!empty($speaking["book_heading_line2"])): ?>
							<br><span class="anna-speaking-page-book__heading-accent"><?php echo esc_html(
           $speaking["book_heading_line2"],
       ); ?></span>
						<?php endif; ?>
					</h2>
				<?php endif; ?>

				<?php if (!empty($speaking["book_body"])): ?>
					<div class="anna-speaking-page-book__copy"><?php echo wp_kses_post(
         wpautop((string) $speaking["book_body"]),
     ); ?></div>
				<?php endif; ?>
			</div>

			<div class="anna-speaking-page-book__card enquiry-card anna-reveal--right">
				<?php if (!empty($speaking["book_card_heading"])): ?>
					<h2 class="anna-speaking-page-book__card-heading"><?php echo esc_html(
         $speaking["book_card_heading"],
     ); ?></h2>
				<?php endif; ?>

				<?php if (!empty($speaking["book_card_body"])): ?>
					<p class="anna-speaking-page-book__card-body"><?php echo esc_html(
         $speaking["book_card_body"],
     ); ?></p>
				<?php endif; ?>

				<?php if (
        !empty($speaking["book_card_button_text"]) &&
        !empty($speaking["book_card_button_url"])
    ): ?>
					<a class="enquiry-card_btn" href="<?php echo esc_url(
         $speaking["book_card_button_url"],
     ); ?>">
						<?php echo esc_html($speaking["book_card_button_text"]); ?>
					</a>
				<?php endif; ?>

				<?php if (
        !empty($speaking["book_card_contact_prefix"]) ||
        !empty($speaking["book_card_email"]) ||
        !empty($speaking["book_card_footer"])
    ): ?>
					<div class="enquiry-card-contact">
						<?php if (!empty($speaking["book_card_contact_prefix"])): ?>
							<?php echo esc_html($speaking["book_card_contact_prefix"]); ?>
						<?php endif; ?>
						<?php if (!empty($speaking["book_card_email"])): ?>
							<a href="mailto:<?php echo esc_attr(
           antispambot($speaking["book_card_email"]),
       ); ?>"><?php echo esc_html(
    antispambot($speaking["book_card_email"]),
); ?></a>
						<?php endif; ?>
						<?php if (!empty($speaking["book_card_footer"])): ?>
							<br><?php echo esc_html($speaking["book_card_footer"]); ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

		</div>
	</div>
</section>
