<?php
/**
 * Sidebar template.
 * @package Anna_Baylis
 * @since   1.0.0
 */
if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>
<aside class="anna-sidebar" role="complementary" aria-label="<?php esc_attr_e( 'Sidebar', 'anna-baylis' ); ?>">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>
