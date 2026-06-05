<?php
/**
 * Reviews Custom Post Type.
 *
 * Registers the anna_review CPT, meta box, save handler,
 * and a helper to query/seed reviews.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ─── CPT Registration ────────────────────────────────────────────────────────

function anna_register_review_cpt() {
	$labels = array(
		'name'               => __( 'Reviews', 'anna-baylis' ),
		'singular_name'      => __( 'Review', 'anna-baylis' ),
		'menu_name'          => __( 'Reviews', 'anna-baylis' ),
		'add_new'            => __( 'Add New', 'anna-baylis' ),
		'add_new_item'       => __( 'Add New Review', 'anna-baylis' ),
		'edit_item'          => __( 'Edit Review', 'anna-baylis' ),
		'new_item'           => __( 'New Review', 'anna-baylis' ),
		'view_item'          => __( 'View Review', 'anna-baylis' ),
		'search_items'       => __( 'Search Reviews', 'anna-baylis' ),
		'not_found'          => __( 'No reviews found', 'anna-baylis' ),
		'not_found_in_trash' => __( 'No reviews found in trash', 'anna-baylis' ),
	);

	register_post_type(
		'anna_review',
		array(
			'labels'          => $labels,
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-star-filled',
			'menu_position'   => 5,
			'supports'        => array( 'title', 'editor' ),
			'has_archive'     => false,
			'rewrite'         => false,
			'capability_type' => 'post',
			'show_in_rest'    => true,
		)
	);
}
add_action( 'init', 'anna_register_review_cpt' );

// ─── Meta Box ────────────────────────────────────────────────────────────────

function anna_review_meta_box_init() {
	add_meta_box(
		'anna_review_details',
		__( 'Review Details', 'anna-baylis' ),
		'anna_render_review_meta_box',
		'anna_review',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'anna_review_meta_box_init' );

function anna_render_review_meta_box( $post ) {
	wp_nonce_field( 'anna_save_review', 'anna_review_nonce' );
	$name      = get_post_meta( $post->ID, '_anna_review_name', true );
	$role      = get_post_meta( $post->ID, '_anna_review_role', true );
	$rating    = get_post_meta( $post->ID, '_anna_review_rating', true ) ?: 5;
	$date_text = get_post_meta( $post->ID, '_anna_review_date_text', true );
	$featured  = get_post_meta( $post->ID, '_anna_review_featured', true );
	?>
	<table class="form-table">
		<tr>
			<th><label><?php esc_html_e( 'Reviewer Name', 'anna-baylis' ); ?></label></th>
			<td><input type="text" name="anna_review_name" value="<?php echo esc_attr( $name ); ?>" class="regular-text"></td>
		</tr>
		<tr>
			<th><label><?php esc_html_e( 'Reviewer Role / Source', 'anna-baylis' ); ?></label></th>
			<td><input type="text" name="anna_review_role" value="<?php echo esc_attr( $role ); ?>" class="regular-text" placeholder="Google Review"></td>
		</tr>
		<tr>
			<th><label><?php esc_html_e( 'Rating (1–5)', 'anna-baylis' ); ?></label></th>
			<td>
				<select name="anna_review_rating">
					<?php for ( $i = 5; $i >= 1; $i-- ) : ?>
						<option value="<?php echo esc_attr( $i ); ?>" <?php selected( (int) $rating, $i ); ?>>
							<?php echo esc_html( $i ); ?> star<?php echo $i > 1 ? 's' : ''; ?>
						</option>
					<?php endfor; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th><label><?php esc_html_e( 'Date Text', 'anna-baylis' ); ?></label></th>
			<td><input type="text" name="anna_review_date_text" value="<?php echo esc_attr( $date_text ); ?>" class="regular-text" placeholder="3 months ago"></td>
		</tr>
		<tr>
			<th><label><?php esc_html_e( 'Featured', 'anna-baylis' ); ?></label></th>
			<td>
				<label>
					<input type="checkbox" name="anna_review_featured" value="1" <?php checked( $featured, '1' ); ?>>
					<?php esc_html_e( 'Show on homepage/reviews page', 'anna-baylis' ); ?>
				</label>
			</td>
		</tr>
	</table>
	<?php
}

function anna_save_review_meta( $post_id ) {
	if ( ! isset( $_POST['anna_review_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['anna_review_nonce'] ) ), 'anna_save_review' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, '_anna_review_name',      sanitize_text_field( wp_unslash( $_POST['anna_review_name'] ?? '' ) ) );
	update_post_meta( $post_id, '_anna_review_role',      sanitize_text_field( wp_unslash( $_POST['anna_review_role'] ?? '' ) ) );
	update_post_meta( $post_id, '_anna_review_rating',    absint( $_POST['anna_review_rating'] ?? 5 ) );
	update_post_meta( $post_id, '_anna_review_date_text', sanitize_text_field( wp_unslash( $_POST['anna_review_date_text'] ?? '' ) ) );
	update_post_meta( $post_id, '_anna_review_featured',  isset( $_POST['anna_review_featured'] ) ? '1' : '0' );
}
add_action( 'save_post_anna_review', 'anna_save_review_meta' );

// ─── Query Helper ────────────────────────────────────────────────────────────

/**
 * Get reviews as a normalized array.
 *
 * @param array $args Optional WP_Query overrides.
 * @return array<int, array{id:int,quote:string,name:string,role:string,rating:int,date_text:string,featured:bool}>
 */
function anna_get_reviews( $args = array() ) {
	$defaults = array(
		'post_type'      => 'anna_review',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
	);

	$query_args = wp_parse_args( $args, $defaults );
	$query      = new WP_Query( $query_args );
	$reviews    = array();

	foreach ( $query->posts as $post ) {
		$reviews[] = array(
			'id'        => $post->ID,
			'quote'     => wp_strip_all_tags( $post->post_content ),
			'name'      => get_post_meta( $post->ID, '_anna_review_name', true ),
			'role'      => get_post_meta( $post->ID, '_anna_review_role', true ),
			'rating'    => absint( get_post_meta( $post->ID, '_anna_review_rating', true ) ?: 5 ),
			'date_text' => get_post_meta( $post->ID, '_anna_review_date_text', true ),
			'featured'  => get_post_meta( $post->ID, '_anna_review_featured', true ) === '1',
		);
	}

	wp_reset_postdata();
	return $reviews;
}

// ─── Seed Placeholder Reviews ────────────────────────────────────────────────

function anna_seed_placeholder_reviews() {
	if ( get_option( 'anna_reviews_seeded_v1' ) ) {
		return;
	}

	$reviews = array(
		array(
			'title'     => 'Rebecca Browne',
			'content'   => 'Working with Anna has been profoundly healing. She has helped me build my own inner support system by reconnecting with my younger self and learning how to meet myself with love, compassion and understanding.',
			'name'      => 'Rebecca Browne',
			'role'      => 'Google Review',
			'rating'    => 5,
			'date_text' => '3 months ago',
			'featured'  => '1',
		),
		array(
			'title'     => 'Mel',
			'content'   => "Anna has an extraordinary ability to help you distil your core values and actually live them, while uncovering parts of yourself you didn't even know existed. She is a guide and a healer.",
			'name'      => 'Mel',
			'role'      => 'Google Review',
			'rating'    => 5,
			'date_text' => '6 months ago',
			'featured'  => '1',
		),
		array(
			'title'     => 'Sarah Clarke',
			'content'   => 'Anna has been a truly remarkable coach and always shows up for you and validates your feelings. As a result of working with her I am more in touch with my feelings and overall happier with life.',
			'name'      => 'Sarah Clarke',
			'role'      => 'Google Review',
			'rating'    => 5,
			'date_text' => 'a year ago',
			'featured'  => '1',
		),
		array(
			'title'     => 'Renee Berger',
			'content'   => 'Working with Anna has been one of the best things I could do for myself and my family. The most transformative part of our work was understanding how my inner child was influencing my daily reactions.',
			'name'      => 'Renee Berger',
			'role'      => 'Google Review',
			'rating'    => 5,
			'date_text' => 'a year ago',
			'featured'  => '1',
		),
		array(
			'title'     => 'Laura Hodges',
			'content'   => "I have been in the mental health system for years. Not even the meds they so quickly handed out compares to the progress I made with Anna. She is able to help you make peace with the past. I 100% highly recommend Anna.",
			'name'      => 'Laura Hodges',
			'role'      => 'Google Review',
			'rating'    => 5,
			'date_text' => '2 years ago',
			'featured'  => '1',
		),
		array(
			'title'     => 'Jessica T',
			'content'   => "Anna creates a genuinely safe space to do the real work. I came to her feeling stuck and left with tools I actually use every single day. Highly recommend to anyone considering coaching.",
			'name'      => 'Jessica T',
			'role'      => 'Google Review',
			'rating'    => 5,
			'date_text' => '4 months ago',
			'featured'  => '1',
		),
	);

	foreach ( $reviews as $data ) {
		$existing = get_posts(
			array(
				'post_type'   => 'anna_review',
				'post_title'  => $data['title'],
				'numberposts' => 1,
				'fields'      => 'ids',
			)
		);

		if ( ! empty( $existing ) ) {
			continue;
		}

		$post_id = wp_insert_post(
			array(
				'post_type'    => 'anna_review',
				'post_title'   => $data['title'],
				'post_content' => $data['content'],
				'post_status'  => 'publish',
			)
		);

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			update_post_meta( $post_id, '_anna_review_name',      $data['name'] );
			update_post_meta( $post_id, '_anna_review_role',      $data['role'] );
			update_post_meta( $post_id, '_anna_review_rating',    $data['rating'] );
			update_post_meta( $post_id, '_anna_review_date_text', $data['date_text'] );
			update_post_meta( $post_id, '_anna_review_featured',  $data['featured'] );
		}
	}

	update_option( 'anna_reviews_seeded_v1', 1 );
}
add_action( 'init', 'anna_seed_placeholder_reviews', 20 );
