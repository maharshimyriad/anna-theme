<?php
/**
 * Reviews page: reviews grid section.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

$data = get_query_var('anna_reviews_page_content', array());
if (empty($data)) {
	$data = anna_get_reviews_page_content();
}

$reviews = function_exists('anna_get_reviews') ? anna_get_reviews() : array();
?>

<section class="anna-reviews-page-grid">
	<div class="anna-container anna-container--max">


	

	</div>
</section>	<div class="anna-reviews-page-grid__cards anna-stagger">
			<?php echo do_shortcode( '[brb_collection id="5610"]' ); ?>
		</div>