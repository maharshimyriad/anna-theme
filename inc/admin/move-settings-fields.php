<?php
/**
 * MOVE page theme settings fields.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render MOVE settings tab.
 */
function anna_render_move_page_settings_fields() {
	anna_field_heading( __( 'MOVE — Hero', 'anna-baylis' ) );
	anna_field_text( 'move_pg_hero_eyebrow', __( 'Eyebrow', 'anna-baylis' ) );
	anna_field_text( 'move_pg_hero_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_media( 'move_pg_hero_image_id', __( 'Background Image', 'anna-baylis' ) );

	anna_field_heading( __( 'Opening — The Evolution', 'anna-baylis' ) );
	anna_field_text( 'move_pg_evolution_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_textarea( 'move_pg_evolution_body', __( 'Body', 'anna-baylis' ), __( 'One paragraph per blank line.', 'anna-baylis' ), 8 );
	anna_field_textarea( 'move_pg_evolution_callout', __( 'OASIS Callout Line', 'anna-baylis' ), '', 2 );
	anna_field_text( 'move_pg_evolution_gallery_heading', __( 'Gallery Heading', 'anna-baylis' ) );
	anna_render_move_gallery_repeater();

	anna_field_heading( __( 'What M.O.V.E Was', 'anna-baylis' ) );
	anna_field_text( 'move_pg_was_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_textarea( 'move_pg_was_body', __( 'Body', 'anna-baylis' ), '', 10 );

	anna_field_heading( __( 'What Women Said', 'anna-baylis' ) );
	anna_field_text( 'move_pg_said_heading', __( 'Heading', 'anna-baylis' ) );
	anna_render_move_quote_repeater( 'move_pg_said_items', __( 'Testimonials', 'anna-baylis' ), 'move-said' );

	anna_field_heading( __( 'Google Reviews', 'anna-baylis' ) );
	anna_field_text( 'move_pg_reviews_eyebrow', __( 'Eyebrow', 'anna-baylis' ) );
	anna_field_text( 'move_pg_reviews_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_text( 'move_pg_reviews_summary', __( 'Summary Line', 'anna-baylis' ) );
	anna_render_move_review_repeater();

	anna_field_heading( __( 'The Four Pillars', 'anna-baylis' ) );
	anna_field_text( 'move_pg_pillars_heading', __( 'Heading', 'anna-baylis' ) );
	anna_render_move_pillar_repeater();

	anna_field_heading( __( 'M.O.V.E Has Evolved (CTA)', 'anna-baylis' ) );
	anna_field_text( 'move_pg_evolved_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_textarea( 'move_pg_evolved_body', __( 'Body', 'anna-baylis' ), '', 3 );
	anna_field_text( 'move_pg_evolved_button_primary_text', __( 'Primary Button Text', 'anna-baylis' ) );
	anna_field_text( 'move_pg_evolved_button_primary_url', __( 'Primary Button URL', 'anna-baylis' ), '', 'url' );
	anna_field_text( 'move_pg_evolved_button_secondary_text', __( 'Secondary Button Text', 'anna-baylis' ) );
	anna_field_text( 'move_pg_evolved_button_secondary_url', __( 'Secondary Button URL', 'anna-baylis' ), '', 'url' );
	anna_field_text( 'move_pg_evolved_button_tertiary_text', __( 'Tertiary Button Text', 'anna-baylis' ) );
	anna_field_text( 'move_pg_evolved_button_tertiary_url', __( 'Tertiary Button URL', 'anna-baylis' ), '', 'url' );
}

/**
 * @param string $option_key Option key.
 * @param string $label      Field label.
 * @param string $slug       Repeater slug.
 */
function anna_render_move_quote_repeater( $option_key, $label, $slug ) {
	$key   = str_replace( 'move_pg_', '', $option_key );
	$items = anna_get_theme_field_value( $option_key, anna_get_move_default_content()[ $key ] ?? array() );
	$items = anna_normalize_move_quote_items( $items );
	?>
	<tr>
		<th scope="row"><?php echo esc_html( $label ); ?></th>
		<td>
			<div class="anna-admin-repeater" data-anna-repeater="<?php echo esc_attr( $slug ); ?>">
				<div class="anna-admin-repeater__rows" data-anna-repeater-rows="true">
					<?php foreach ( $items as $i => $item ) : ?>
						<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
							<textarea name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $i ); ?>][quote]" rows="3" class="large-text"><?php echo esc_textarea( $item['quote'] ?? '' ); ?></textarea>
							<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button>
						</div>
					<?php endforeach; ?>
				</div>
				<button type="button" class="button" data-anna-repeater-add="true"><?php esc_html_e( 'Add Quote', 'anna-baylis' ); ?></button>
				<template data-anna-repeater-template="true">
					<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
						<textarea name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][__INDEX__][quote]" rows="3" class="large-text"></textarea>
						<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button>
					</div>
				</template>
			</div>
		</td>
	</tr>
	<?php
}

function anna_render_move_gallery_repeater() {
	$items = anna_get_theme_field_value( 'move_pg_evolution_gallery_items', anna_get_move_default_content()['evolution_gallery_items'] ?? array() );
	$items = anna_normalize_move_gallery_items( $items );
	?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Gallery Images', 'anna-baylis' ); ?></th>
		<td>
			<div class="anna-admin-repeater" data-anna-repeater="move-gallery">
				<div class="anna-admin-repeater__rows" data-anna-repeater-rows="true">
					<?php foreach ( $items as $i => $item ) : ?>
						<?php
						$image_id    = absint( $item['image_id'] ?? 0 );
						$preview_url = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : '';
						?>
						<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
							<input type="hidden" id="move-gallery-<?php echo esc_attr( $i ); ?>" name="anna_theme_options[move_pg_evolution_gallery_items][<?php echo esc_attr( $i ); ?>][image_id]" value="<?php echo esc_attr( $image_id ); ?>">
							<div class="anna-media-preview" id="move-gallery-<?php echo esc_attr( $i ); ?>-preview">
								<?php if ( $preview_url ) : ?>
									<img src="<?php echo esc_url( $preview_url ); ?>" alt="" style="max-width:120px;height:auto;">
								<?php endif; ?>
							</div>
							<button type="button" class="button anna-media-upload-btn" data-target="move-gallery-<?php echo esc_attr( $i ); ?>" data-preview="move-gallery-<?php echo esc_attr( $i ); ?>-preview"><?php esc_html_e( 'Select Image', 'anna-baylis' ); ?></button>
							<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button>
						</div>
					<?php endforeach; ?>
				</div>
				<button type="button" class="button" data-anna-repeater-add="true"><?php esc_html_e( 'Add Image', 'anna-baylis' ); ?></button>
				<template data-anna-repeater-template="true">
					<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
						<input type="hidden" id="move-gallery-__INDEX__" name="anna_theme_options[move_pg_evolution_gallery_items][__INDEX__][image_id]" value="">
						<div class="anna-media-preview" id="move-gallery-__INDEX__-preview"></div>
						<button type="button" class="button anna-media-upload-btn" data-target="move-gallery-__INDEX__" data-preview="move-gallery-__INDEX__-preview"><?php esc_html_e( 'Select Image', 'anna-baylis' ); ?></button>
						<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button>
					</div>
				</template>
			</div>
		</td>
	</tr>
	<?php
}

function anna_render_move_pillar_repeater() {
	$items = anna_get_theme_field_value( 'move_pg_pillar_items', anna_get_move_default_content()['pillar_items'] ?? array() );
	$items = anna_normalize_move_pillar_items( $items );
	?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Pillars', 'anna-baylis' ); ?></th>
		<td>
			<div class="anna-admin-repeater" data-anna-repeater="move-pillars">
				<div class="anna-admin-repeater__rows" data-anna-repeater-rows="true">
					<?php foreach ( $items as $i => $item ) : ?>
						<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
							<p><input type="text" class="regular-text" name="anna_theme_options[move_pg_pillar_items][<?php echo esc_attr( $i ); ?>][title]" value="<?php echo esc_attr( $item['title'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Title', 'anna-baylis' ); ?>"></p>
							<p><textarea class="large-text" rows="3" name="anna_theme_options[move_pg_pillar_items][<?php echo esc_attr( $i ); ?>][body]"><?php echo esc_textarea( $item['body'] ?? '' ); ?></textarea></p>
							<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button>
						</div>
					<?php endforeach; ?>
				</div>
				<button type="button" class="button" data-anna-repeater-add="true"><?php esc_html_e( 'Add Pillar', 'anna-baylis' ); ?></button>
				<template data-anna-repeater-template="true">
					<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
						<p><input type="text" class="regular-text" name="anna_theme_options[move_pg_pillar_items][__INDEX__][title]" value="" placeholder="<?php esc_attr_e( 'Title', 'anna-baylis' ); ?>"></p>
						<p><textarea class="large-text" rows="3" name="anna_theme_options[move_pg_pillar_items][__INDEX__][body]"></textarea></p>
						<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button>
					</div>
				</template>
			</div>
		</td>
	</tr>
	<?php
}

function anna_render_move_review_repeater() {
	$items = anna_get_theme_field_value( 'move_pg_reviews_items', anna_get_move_default_content()['reviews_items'] ?? array() );
	$items = anna_normalize_move_review_items( $items );
	?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Review Cards', 'anna-baylis' ); ?></th>
		<td>
			<div class="anna-admin-repeater" data-anna-repeater="move-reviews">
				<div class="anna-admin-repeater__rows" data-anna-repeater-rows="true">
					<?php foreach ( $items as $i => $item ) : ?>
						<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
							<p><textarea class="large-text" rows="3" name="anna_theme_options[move_pg_reviews_items][<?php echo esc_attr( $i ); ?>][quote]"><?php echo esc_textarea( $item['quote'] ?? '' ); ?></textarea></p>
							<p><input type="text" class="regular-text" name="anna_theme_options[move_pg_reviews_items][<?php echo esc_attr( $i ); ?>][name]" value="<?php echo esc_attr( $item['name'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Name', 'anna-baylis' ); ?>"></p>
							<p><input type="text" class="regular-text" name="anna_theme_options[move_pg_reviews_items][<?php echo esc_attr( $i ); ?>][role]" value="<?php echo esc_attr( $item['role'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Date / role', 'anna-baylis' ); ?>"></p>
							<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button>
						</div>
					<?php endforeach; ?>
				</div>
				<button type="button" class="button" data-anna-repeater-add="true"><?php esc_html_e( 'Add Review', 'anna-baylis' ); ?></button>
				<template data-anna-repeater-template="true">
					<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
						<p><textarea class="large-text" rows="3" name="anna_theme_options[move_pg_reviews_items][__INDEX__][quote]"></textarea></p>
						<p><input type="text" class="regular-text" name="anna_theme_options[move_pg_reviews_items][__INDEX__][name]" value="" placeholder="<?php esc_attr_e( 'Name', 'anna-baylis' ); ?>"></p>
						<p><input type="text" class="regular-text" name="anna_theme_options[move_pg_reviews_items][__INDEX__][role]" value="" placeholder="<?php esc_attr_e( 'Date / role', 'anna-baylis' ); ?>"></p>
						<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button>
					</div>
				</template>
			</div>
		</td>
	</tr>
	<?php
}
