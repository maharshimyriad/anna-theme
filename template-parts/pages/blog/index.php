<?php
/**
 * Blog page: section loader.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$blog = anna_get_blog_page_content();
set_query_var( 'anna_blog_page_content', $blog );

get_template_part( 'template-parts/pages/blog/hero' );
get_template_part( 'template-parts/pages/blog/posts' );
