<?php
/**
 * Meta Box: Service
 * @package Anna_Baylis
 * @since   1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function anna_service_meta_box() {
	add_meta_box( 'anna_service_details', __( 'Service Details', 'anna-baylis' ), 'anna_service_meta_callback', 'anna_service', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'anna_service_meta_box' );

function anna_service_meta_callback( $post ) {
	wp_nonce_field( 'anna_service_save', 'anna_service_nonce' );
	$icon  = get_post_meta( $post->ID, '_anna_service_icon', true );
	$price = get_post_meta( $post->ID, '_anna_service_price', true );
	$cta   = get_post_meta( $post->ID, '_anna_service_cta_url', true );
	$icons = array( 'coaching', 'wellness', 'mindset', 'growth', 'workshop', 'community', 'default' );
	?>
	<table class="form-table">
		<tr>
			<th><label for="anna-icon"><?php esc_html_e( 'Icon', 'anna-baylis' ); ?></label></th>
			<td>
				<select id="anna-icon" name="anna_service_icon">
					<?php foreach ( $icons as $i ) : ?>
						<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $icon, $i ); ?>><?php echo esc_html( ucfirst( $i ) ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="anna-price"><?php esc_html_e( 'Price / Starting From', 'anna-baylis' ); ?></label></th>
			<td><input type="text" id="anna-price" name="anna_service_price" value="<?php echo esc_attr( $price ); ?>" class="regular-text" placeholder="From £299"></td>
		</tr>
		<tr>
			<th><label for="anna-cta-url"><?php esc_html_e( 'CTA / Booking URL', 'anna-baylis' ); ?></label></th>
			<td><input type="url" id="anna-cta-url" name="anna_service_cta_url" value="<?php echo esc_attr( $cta ); ?>" class="regular-text"></td>
		</tr>
	</table>
	<?php
}

function anna_service_meta_save( $post_id ) {
	if ( ! isset( $_POST['anna_service_nonce'] ) || ! wp_verify_nonce( $_POST['anna_service_nonce'], 'anna_service_save' ) ) { return; }
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }

	$fields = array(
		'anna_service_icon'    => '_anna_service_icon',
		'anna_service_price'   => '_anna_service_price',
		'anna_service_cta_url' => '_anna_service_cta_url',
	);
	foreach ( $fields as $pk => $mk ) {
		if ( isset( $_POST[ $pk ] ) ) {
			update_post_meta( $post_id, $mk, sanitize_text_field( $_POST[ $pk ] ) );
		}
	}
}
add_action( 'save_post_anna_service', 'anna_service_meta_save' );
