<?php
/**
 * Meta Box: Coach
 * @package Anna_Baylis
 * @since   1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function anna_coach_meta_box() {
	add_meta_box( 'anna_coach_details', __( 'Coach Details', 'anna-baylis' ), 'anna_coach_meta_callback', 'anna_coach', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'anna_coach_meta_box' );

function anna_coach_meta_callback( $post ) {
	wp_nonce_field( 'anna_coach_save', 'anna_coach_nonce' );
	$specialties = get_post_meta( $post->ID, '_anna_coach_specialties', true );
	$linkedin    = get_post_meta( $post->ID, '_anna_coach_linkedin', true );
	$website     = get_post_meta( $post->ID, '_anna_coach_website', true );
	?>
	<table class="form-table">
		<tr>
			<th><label for="anna-specialties"><?php esc_html_e( 'Specialties (comma-separated)', 'anna-baylis' ); ?></label></th>
			<td><input type="text" id="anna-specialties" name="anna_coach_specialties" value="<?php echo esc_attr( $specialties ); ?>" class="large-text"></td>
		</tr>
		<tr>
			<th><label for="anna-linkedin"><?php esc_html_e( 'LinkedIn URL', 'anna-baylis' ); ?></label></th>
			<td><input type="url" id="anna-linkedin" name="anna_coach_linkedin" value="<?php echo esc_attr( $linkedin ); ?>" class="regular-text"></td>
		</tr>
		<tr>
			<th><label for="anna-website"><?php esc_html_e( 'Website URL', 'anna-baylis' ); ?></label></th>
			<td><input type="url" id="anna-website" name="anna_coach_website" value="<?php echo esc_attr( $website ); ?>" class="regular-text"></td>
		</tr>
	</table>
	<?php
}

function anna_coach_meta_save( $post_id ) {
	if ( ! isset( $_POST['anna_coach_nonce'] ) || ! wp_verify_nonce( $_POST['anna_coach_nonce'], 'anna_coach_save' ) ) { return; }
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }

	foreach ( array( 'anna_coach_specialties' => '_anna_coach_specialties', 'anna_coach_linkedin' => '_anna_coach_linkedin', 'anna_coach_website' => '_anna_coach_website' ) as $pk => $mk ) {
		if ( isset( $_POST[ $pk ] ) ) {
			update_post_meta( $post_id, $mk, sanitize_text_field( $_POST[ $pk ] ) );
		}
	}
}
add_action( 'save_post_anna_coach', 'anna_coach_meta_save' );
