<?php
/**
 * Speaking page theme settings fields.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render Speaking settings tab.
 */
function anna_render_speaking_page_settings_fields() {
	anna_field_heading( __( 'Speaking Page Hero', 'anna-baylis' ) );
	anna_field_text( 'speaking_pg_hero_eyebrow', __( 'Eyebrow', 'anna-baylis' ) );
	anna_field_textarea( 'speaking_pg_hero_heading', __( 'Heading', 'anna-baylis' ), __( 'One line per row.', 'anna-baylis' ), 3 );
	anna_field_textarea( 'speaking_pg_hero_body', __( 'Description', 'anna-baylis' ), '', 4 );
	anna_field_media( 'speaking_pg_hero_image_id', __( 'Background Image', 'anna-baylis' ) );
	anna_field_text( 'speaking_pg_hero_button_text', __( 'Primary Button Text', 'anna-baylis' ) );
	anna_field_text( 'speaking_pg_hero_button_url', __( 'Primary Button URL', 'anna-baylis' ), '', 'url' );
	anna_field_text( 'speaking_pg_hero_secondary_text', __( 'Secondary Link Text', 'anna-baylis' ) );
	anna_field_text( 'speaking_pg_hero_secondary_url', __( 'Secondary Link URL', 'anna-baylis' ), '', 'url' );
	anna_render_speaking_stat_repeater();

	anna_field_heading( __( 'What I Bring to the Stage', 'anna-baylis' ) );
	anna_field_text( 'speaking_pg_bring_eyebrow', __( 'Eyebrow', 'anna-baylis' ) );
	anna_field_text( 'speaking_pg_bring_heading_line1', __( 'Heading Line 1', 'anna-baylis' ) );
	anna_field_text( 'speaking_pg_bring_heading_line2', __( 'Heading Line 2', 'anna-baylis' ) );
	anna_field_textarea( 'speaking_pg_bring_body', __( 'Body', 'anna-baylis' ), '', 8 );
	anna_field_textarea( 'speaking_pg_bring_quote', __( 'Quote', 'anna-baylis' ), '', 3 );
	anna_field_media( 'speaking_pg_bring_image_id', __( 'Image', 'anna-baylis' ) );
	anna_field_text( 'speaking_pg_bring_button_text', __( 'Button Text', 'anna-baylis' ) );
	anna_field_text( 'speaking_pg_bring_button_url', __( 'Button URL', 'anna-baylis' ), '', 'url' );

	anna_field_heading( __( 'Speaking Topics', 'anna-baylis' ) );
	anna_field_text( 'speaking_pg_topics_eyebrow', __( 'Eyebrow', 'anna-baylis' ) );
	anna_field_text( 'speaking_pg_topics_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_textarea( 'speaking_pg_topics_intro', __( 'Intro', 'anna-baylis' ), '', 3 );
	anna_render_speaking_topic_repeater();

	anna_field_heading( __( 'Talk Formats', 'anna-baylis' ) );
	anna_field_text( 'speaking_pg_formats_eyebrow', __( 'Eyebrow', 'anna-baylis' ) );
	anna_field_text( 'speaking_pg_formats_heading', __( 'Heading', 'anna-baylis' ) );
	anna_render_speaking_format_repeater();
	anna_field_text( 'speaking_pg_formats_audience_heading', __( 'Audience Heading', 'anna-baylis' ) );
	anna_render_speaking_text_repeater( 'speaking_pg_formats_audience_items', __( 'Audience List', 'anna-baylis' ), 'speaking-audience' );

	anna_field_heading( __( 'What Audiences Take Away', 'anna-baylis' ) );
	anna_field_text( 'speaking_pg_takeaway_eyebrow', __( 'Eyebrow', 'anna-baylis' ) );
	anna_field_text( 'speaking_pg_takeaway_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_textarea( 'speaking_pg_takeaway_body', __( 'Body', 'anna-baylis' ), '', 5 );
	anna_render_speaking_text_repeater( 'speaking_pg_takeaway_items', __( 'Takeaway Items', 'anna-baylis' ), 'speaking-takeaway', __( 'Use **word** for emphasis.', 'anna-baylis' ) );

	anna_field_heading( __( 'Recent Experience', 'anna-baylis' ) );
	anna_field_text( 'speaking_pg_experience_eyebrow', __( 'Eyebrow', 'anna-baylis' ) );
	anna_field_text( 'speaking_pg_experience_heading_primary', __( 'Heading (Primary)', 'anna-baylis' ) );
	anna_field_text( 'speaking_pg_experience_heading_secondary', __( 'Heading (Secondary)', 'anna-baylis' ) );
	anna_field_textarea( 'speaking_pg_experience_body', __( 'Body', 'anna-baylis' ), '', 8 );
	anna_field_text( 'speaking_pg_experience_link_prefix', __( 'Link Prefix', 'anna-baylis' ) );
	anna_field_text( 'speaking_pg_experience_link_label', __( 'Link Label', 'anna-baylis' ) );
	anna_field_text( 'speaking_pg_experience_link_url', __( 'Link URL', 'anna-baylis' ), '', 'url' );
	anna_field_textarea( 'speaking_pg_experience_testimonial_quote', __( 'Testimonial Quote', 'anna-baylis' ), '', 4 );
	anna_field_text( 'speaking_pg_experience_testimonial_name', __( 'Testimonial Name', 'anna-baylis' ) );
	anna_field_text( 'speaking_pg_experience_testimonial_role', __( 'Testimonial Role', 'anna-baylis' ) );
}

function anna_render_speaking_text_repeater( $option_key, $label, $slug, $desc = '' ) {
	$items = anna_get_option( $option_key, anna_get_speaking_default_content()[ str_replace( 'speaking_pg_', '', $option_key ) ] ?? array() );
	$items = anna_normalize_speaking_text_items( $items );
	?>
	<tr>
		<th scope="row"><?php echo esc_html( $label ); ?></th>
		<td>
			<?php if ( $desc ) : ?><p class="description"><?php echo esc_html( $desc ); ?></p><?php endif; ?>
			<div class="anna-admin-repeater" data-anna-repeater="<?php echo esc_attr( $slug ); ?>">
				<div class="anna-admin-repeater__rows" data-anna-repeater-rows="true">
					<?php foreach ( $items as $i => $item ) : ?>
						<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
							<input type="text" class="large-text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $i ); ?>][text]" value="<?php echo esc_attr( $item['text'] ?? '' ); ?>">
							<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button>
						</div>
					<?php endforeach; ?>
				</div>
				<button type="button" class="button" data-anna-repeater-add="true"><?php esc_html_e( 'Add', 'anna-baylis' ); ?></button>
				<template data-anna-repeater-template="true">
					<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
						<input type="text" class="large-text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][__INDEX__][text]" value="">
						<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button>
					</div>
				</template>
			</div>
		</td>
	</tr>
	<?php
}

function anna_render_speaking_stat_repeater() {
	$option_key = 'speaking_pg_hero_stat_items';
	$items      = anna_normalize_speaking_stat_items( anna_get_option( $option_key, anna_get_speaking_default_content()['hero_stat_items'] ?? array() ) );
	?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Hero Stats', 'anna-baylis' ); ?></th>
		<td>
			<div class="anna-admin-repeater" data-anna-repeater="speaking-stats">
				<div class="anna-admin-repeater__rows" data-anna-repeater-rows="true">
					<?php foreach ( $items as $i => $item ) : ?>
						<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
							<p><input type="text" class="small-text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $i ); ?>][value]" value="<?php echo esc_attr( $item['value'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Value', 'anna-baylis' ); ?>"></p>
							<p><input type="text" class="large-text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $i ); ?>][label]" value="<?php echo esc_attr( $item['label'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Label', 'anna-baylis' ); ?>"></p>
							<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button><hr>
						</div>
					<?php endforeach; ?>
				</div>
				<button type="button" class="button" data-anna-repeater-add="true"><?php esc_html_e( 'Add Stat', 'anna-baylis' ); ?></button>
				<template data-anna-repeater-template="true">
					<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
						<p><input type="text" class="small-text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][__INDEX__][value]" value=""></p>
						<p><input type="text" class="large-text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][__INDEX__][label]" value=""></p>
						<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button><hr>
					</div>
				</template>
			</div>
		</td>
	</tr>
	<?php
}

function anna_render_speaking_topic_repeater() {
	$option_key = 'speaking_pg_topics_card_items';
	$items      = anna_normalize_speaking_topic_cards( anna_get_option( $option_key, anna_get_speaking_default_content()['topics_card_items'] ?? array() ) );
	?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Topic Cards', 'anna-baylis' ); ?></th>
		<td>
			<div class="anna-admin-repeater" data-anna-repeater="speaking-topics">
				<div class="anna-admin-repeater__rows" data-anna-repeater-rows="true">
					<?php foreach ( $items as $i => $item ) : ?>
						<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
							<p><label><?php esc_html_e( 'Icon', 'anna-baylis' ); ?> <input type="text" class="small-text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $i ); ?>][icon]" value="<?php echo esc_attr( $item['icon'] ?? 'brain' ); ?>"></label></p>
							<p><input type="text" class="large-text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $i ); ?>][title]" value="<?php echo esc_attr( $item['title'] ?? '' ); ?>"></p>
							<p><textarea class="large-text" rows="3" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $i ); ?>][body]"><?php echo esc_textarea( $item['body'] ?? '' ); ?></textarea></p>
							<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button><hr>
						</div>
					<?php endforeach; ?>
				</div>
				<button type="button" class="button" data-anna-repeater-add="true"><?php esc_html_e( 'Add Topic', 'anna-baylis' ); ?></button>
				<template data-anna-repeater-template="true">
					<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
						<p><input type="text" class="small-text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][__INDEX__][icon]" value="brain"></p>
						<p><input type="text" class="large-text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][__INDEX__][title]" value=""></p>
						<p><textarea class="large-text" rows="3" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][__INDEX__][body]"></textarea></p>
						<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button><hr>
					</div>
				</template>
			</div>
		</td>
	</tr>
	<?php
}

function anna_render_speaking_format_repeater() {
	$option_key = 'speaking_pg_formats_card_items';
	$items      = anna_normalize_speaking_format_cards( anna_get_option( $option_key, anna_get_speaking_default_content()['formats_card_items'] ?? array() ) );
	?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Format Cards', 'anna-baylis' ); ?></th>
		<td>
			<div class="anna-admin-repeater" data-anna-repeater="speaking-formats">
				<div class="anna-admin-repeater__rows" data-anna-repeater-rows="true">
					<?php foreach ( $items as $i => $item ) : ?>
						<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
							<p><input type="text" class="small-text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $i ); ?>][number]" value="<?php echo esc_attr( $item['number'] ?? '' ); ?>" placeholder="01"></p>
							<p><input type="text" class="large-text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $i ); ?>][title]" value="<?php echo esc_attr( $item['title'] ?? '' ); ?>"></p>
							<p><textarea class="large-text" rows="3" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $i ); ?>][body]"><?php echo esc_textarea( $item['body'] ?? '' ); ?></textarea></p>
							<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button><hr>
						</div>
					<?php endforeach; ?>
				</div>
				<button type="button" class="button" data-anna-repeater-add="true"><?php esc_html_e( 'Add Format', 'anna-baylis' ); ?></button>
				<template data-anna-repeater-template="true">
					<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
						<p><input type="text" class="small-text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][__INDEX__][number]" value=""></p>
						<p><input type="text" class="large-text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][__INDEX__][title]" value=""></p>
						<p><textarea class="large-text" rows="3" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][__INDEX__][body]"></textarea></p>
						<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button><hr>
					</div>
				</template>
			</div>
		</td>
	</tr>
	<?php
}
