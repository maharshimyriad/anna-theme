<?php
/**
 * Meta Box: Testimonial
 *
 * Custom fields for the Testimonials CPT.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function anna_testimonial_meta_box() {
	add_meta_box(
		'anna_testimonial_details',
		__( 'Testimonial Details', 'anna-baylis' ),
		'anna_testimonial_meta_callback',
		'anna_testimonial',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'anna_testimonial_meta_box' );

function anna_testimonial_meta_callback( $post ) {
	wp_nonce_field( 'anna_testimonial_save', 'anna_testimonial_nonce' );

	$rating   = get_post_meta( $post->ID, '_anna_testimonial_rating', true ) ?: 5;
	$role     = get_post_meta( $post->ID, '_anna_testimonial_role', true );
	$company  = get_post_meta( $post->ID, '_anna_testimonial_company', true );
	$featured = get_post_meta( $post->ID, '_anna_testimonial_featured', true );
	$platform = get_post_meta( $post->ID, '_anna_testimonial_platform', true );
	?>
	<table class="form-table">
		<tr>
			<th><label for="anna-rating"><?php esc_html_e( 'Rating (1-5)', 'anna-baylis' ); ?></label></th>
			<td><input type="number" id="anna-rating" name="anna_testimonial_rating" value="<?php echo esc_attr( $rating ); ?>" min="1" max="5" step="1" class="small-text"></td>
		</tr>
		<tr>
			<th><label for="anna-role"><?php esc_html_e( 'Client Role / Title', 'anna-baylis' ); ?></label></th>
			<td><input type="text" id="anna-role" name="anna_testimonial_role" value="<?php echo esc_attr( $role ); ?>" class="regular-text"></td>
		</tr>
		<tr>
			<th><label for="anna-company"><?php esc_html_e( 'Company / Organisation', 'anna-baylis' ); ?></label></th>
			<td><input type="text" id="anna-company" name="anna_testimonial_company" value="<?php echo esc_attr( $company ); ?>" class="regular-text"></td>
		</tr>
		<tr>
			<th><label for="anna-platform"><?php esc_html_e( 'Review Platform', 'anna-baylis' ); ?></label></th>
			<td>
				<select id="anna-platform" name="anna_testimonial_platform">
					<option value=""><?php esc_html_e( '— None —', 'anna-baylis' ); ?></option>
					<option value="google" <?php selected( $platform, 'google' ); ?>><?php esc_html_e( 'Google', 'anna-baylis' ); ?></option>
					<option value="facebook" <?php selected( $platform, 'facebook' ); ?>><?php esc_html_e( 'Facebook', 'anna-baylis' ); ?></option>
					<option value="trustpilot" <?php selected( $platform, 'trustpilot' ); ?>><?php esc_html_e( 'Trustpilot', 'anna-baylis' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="anna-featured"><?php esc_html_e( 'Featured on Homepage', 'anna-baylis' ); ?></label></th>
			<td><input type="checkbox" id="anna-featured" name="anna_testimonial_featured" value="1" <?php checked( $featured, '1' ); ?>></td>
		</tr>
	</table>
	<?php
}

function anna_testimonial_meta_save( $post_id ) {
	if ( ! isset( $_POST['anna_testimonial_nonce'] ) || ! wp_verify_nonce( $_POST['anna_testimonial_nonce'], 'anna_testimonial_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array(
		'anna_testimonial_rating'   => '_anna_testimonial_rating',
		'anna_testimonial_role'     => '_anna_testimonial_role',
		'anna_testimonial_company'  => '_anna_testimonial_company',
		'anna_testimonial_platform' => '_anna_testimonial_platform',
	);

	foreach ( $fields as $post_key => $meta_key ) {
		if ( isset( $_POST[ $post_key ] ) ) {
			update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[ $post_key ] ) );
		}
	}

	$featured = isset( $_POST['anna_testimonial_featured'] ) ? '1' : '0';
	update_post_meta( $post_id, '_anna_testimonial_featured', $featured );
}
add_action( 'save_post_anna_testimonial', 'anna_testimonial_meta_save' );
