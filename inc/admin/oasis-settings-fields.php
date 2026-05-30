<?php
/**
 * Oasis page theme settings fields.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render Oasis settings tab.
 */
function anna_render_oasis_page_settings_fields() {
	anna_field_heading( __( 'Oasis Page Hero', 'anna-baylis' ) );
	anna_field_text( 'oasis_pg_hero_eyebrow', __( 'Eyebrow', 'anna-baylis' ) );
	anna_field_text( 'oasis_pg_hero_heading', __( 'Title', 'anna-baylis' ) );
	anna_field_text( 'oasis_pg_hero_subheading', __( 'Subheading', 'anna-baylis' ) );
	anna_field_textarea( 'oasis_pg_hero_body', __( 'Body', 'anna-baylis' ), __( 'One paragraph per blank line.', 'anna-baylis' ), 8 );
	anna_field_media( 'oasis_pg_hero_image_id', __( 'Background Image', 'anna-baylis' ) );
	anna_field_text( 'oasis_pg_hero_button_text', __( 'Button Text', 'anna-baylis' ) );
	anna_field_text( 'oasis_pg_hero_button_url', __( 'Button URL', 'anna-baylis' ), '', 'url' );

	anna_field_heading( __( 'What Oasis Is', 'anna-baylis' ) );
	anna_field_text( 'oasis_pg_what_eyebrow', __( 'Eyebrow', 'anna-baylis' ) );
	anna_field_text( 'oasis_pg_what_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_textarea( 'oasis_pg_what_body', __( 'Body', 'anna-baylis' ), '', 5 );
	anna_field_text( 'oasis_pg_what_footer_line', __( 'Footer Line', 'anna-baylis' ) );

	anna_field_heading( __( 'Where Oasis Began', 'anna-baylis' ) );
	anna_field_text( 'oasis_pg_begun_eyebrow', __( 'Eyebrow', 'anna-baylis' ) );
	anna_field_text( 'oasis_pg_begun_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_text( 'oasis_pg_begun_subheading', __( 'Credentials Line', 'anna-baylis' ) );
	anna_field_textarea( 'oasis_pg_begun_body', __( 'Body', 'anna-baylis' ), '', 10 );
	anna_field_text( 'oasis_pg_begun_quote', __( 'Quote', 'anna-baylis' ) );
	anna_field_textarea( 'oasis_pg_begun_closing', __( 'Closing Paragraph', 'anna-baylis' ), '', 4 );
	anna_field_media( 'oasis_pg_begun_image_id', __( 'Portrait Image', 'anna-baylis' ) );
	anna_field_text( 'oasis_pg_begun_callout_label', __( 'Callout Label', 'anna-baylis' ) );
	anna_field_textarea( 'oasis_pg_begun_callout_body', __( 'Callout Body', 'anna-baylis' ), '', 3 );

	anna_field_heading( __( 'Inside Oasis', 'anna-baylis' ) );
	anna_field_text( 'oasis_pg_inside_eyebrow', __( 'Eyebrow', 'anna-baylis' ) );
	anna_field_text( 'oasis_pg_inside_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_textarea( 'oasis_pg_inside_body', __( 'Body', 'anna-baylis' ), '', 6 );
	anna_field_text( 'oasis_pg_inside_highlight', __( 'Highlight Line', 'anna-baylis' ) );
	anna_field_text( 'oasis_pg_inside_pills_intro', __( 'Pills Intro', 'anna-baylis' ) );
	anna_render_oasis_text_repeater( 'oasis_pg_inside_pill_items', __( 'Value Pills', 'anna-baylis' ), 'oasis-pills' );
	anna_render_oasis_schedule_repeater();

	anna_field_heading( __( 'How It Works', 'anna-baylis' ) );
	anna_field_text( 'oasis_pg_how_eyebrow', __( 'Eyebrow', 'anna-baylis' ) );
	anna_field_text( 'oasis_pg_how_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_textarea( 'oasis_pg_how_intro', __( 'Intro', 'anna-baylis' ), '', 3 );
	anna_render_oasis_how_repeater();
	anna_field_text( 'oasis_pg_how_footer', __( 'Footer Line', 'anna-baylis' ) );

	anna_field_heading( __( 'Choose Your Experience', 'anna-baylis' ) );
	anna_field_text( 'oasis_pg_choose_eyebrow', __( 'Eyebrow', 'anna-baylis' ) );
	anna_field_text( 'oasis_pg_choose_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_text( 'oasis_pg_choose_intro', __( 'Intro', 'anna-baylis' ) );
	anna_render_oasis_plan_repeater();
	anna_field_textarea( 'oasis_pg_choose_footer', __( 'Footer Note', 'anna-baylis' ), '', 3 );

	anna_field_heading( __( 'Is Oasis Right For You', 'anna-baylis' ) );
	anna_field_text( 'oasis_pg_ready_eyebrow', __( 'Eyebrow', 'anna-baylis' ) );
	anna_field_text( 'oasis_pg_ready_heading', __( 'Heading', 'anna-baylis' ) );
	anna_render_oasis_text_repeater( 'oasis_pg_ready_items', __( 'Cards', 'anna-baylis' ), 'oasis-ready' );
}

/**
 * Simple text repeater row.
 */
function anna_render_oasis_text_repeater( $option_key, $label, $slug ) {
	$items = anna_get_option( $option_key, array() );
	if ( empty( $items ) && function_exists( 'anna_get_oasis_default_content' ) ) {
		$key = str_replace( 'oasis_pg_', '', $option_key );
		$def = anna_get_oasis_default_content();
		$items = $def[ $key ] ?? array();
	}
	$items = function_exists( 'anna_normalize_oasis_text_items' ) ? anna_normalize_oasis_text_items( $items ) : $items;
	?>
	<tr>
		<th scope="row"><?php echo esc_html( $label ); ?></th>
		<td>
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

function anna_render_oasis_schedule_repeater() {
	$option_key = 'oasis_pg_inside_schedule_items';
	$items      = anna_get_option( $option_key, anna_get_oasis_default_content()['inside_schedule_items'] ?? array() );
	$items      = anna_normalize_oasis_schedule_items( $items );
	?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Weekly Rhythm', 'anna-baylis' ); ?></th>
		<td>
			<div class="anna-admin-repeater" data-anna-repeater="oasis-schedule">
				<div class="anna-admin-repeater__rows" data-anna-repeater-rows="true">
					<?php foreach ( $items as $i => $item ) : ?>
						<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
							<p><input type="text" class="large-text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $i ); ?>][title]" value="<?php echo esc_attr( $item['title'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Title', 'anna-baylis' ); ?>"></p>
							<p><textarea class="large-text" rows="3" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $i ); ?>][body]"><?php echo esc_textarea( $item['body'] ?? '' ); ?></textarea></p>
							<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button><hr>
						</div>
					<?php endforeach; ?>
				</div>
				<button type="button" class="button" data-anna-repeater-add="true"><?php esc_html_e( 'Add Day', 'anna-baylis' ); ?></button>
				<template data-anna-repeater-template="true">
					<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
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

function anna_render_oasis_how_repeater() {
	$option_key = 'oasis_pg_how_card_items';
	$items      = anna_normalize_oasis_how_cards( anna_get_option( $option_key, anna_get_oasis_default_content()['how_card_items'] ?? array() ) );
	?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Rhythm Cards', 'anna-baylis' ); ?></th>
		<td>
			<div class="anna-admin-repeater" data-anna-repeater="oasis-how">
				<div class="anna-admin-repeater__rows" data-anna-repeater-rows="true">
					<?php foreach ( $items as $i => $item ) : ?>
						<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
							<p><label><?php esc_html_e( 'Icon slug', 'anna-baylis' ); ?> <input type="text" class="small-text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $i ); ?>][icon]" value="<?php echo esc_attr( $item['icon'] ?? 'roots' ); ?>"></label></p>
							<p><input type="text" class="large-text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $i ); ?>][title]" value="<?php echo esc_attr( $item['title'] ?? '' ); ?>"></p>
							<p><textarea class="large-text" rows="3" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $i ); ?>][body]"><?php echo esc_textarea( $item['body'] ?? '' ); ?></textarea></p>
							<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button><hr>
						</div>
					<?php endforeach; ?>
				</div>
				<button type="button" class="button" data-anna-repeater-add="true"><?php esc_html_e( 'Add Card', 'anna-baylis' ); ?></button>
				<template data-anna-repeater-template="true">
					<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
						<p><input type="text" class="small-text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][__INDEX__][icon]" value="roots"></p>
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

function anna_render_oasis_plan_repeater() {
	$option_key = 'oasis_pg_choose_plan_items';
	$items      = anna_normalize_oasis_plan_items( anna_get_option( $option_key, anna_get_oasis_default_content()['choose_plan_items'] ?? array() ) );
	$defaults   = anna_get_oasis_default_content()['choose_plan_items'] ?? array();
	if ( count( $items ) < 2 ) {
		$items = array_merge( $items, array_slice( $defaults, count( $items ), 2 - count( $items ) ) );
	}
	?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Pricing Plans', 'anna-baylis' ); ?></th>
		<td>
			<?php foreach ( $items as $i => $plan ) : ?>
				<div style="border:1px solid #ccd0d4;padding:12px;margin-bottom:12px;background:#fff;">
					<p><strong><?php echo esc_html( sprintf( __( 'Plan %d', 'anna-baylis' ), $i + 1 ) ); ?></strong></p>
					<p><input type="text" class="regular-text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $i ); ?>][title]" value="<?php echo esc_attr( $plan['title'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Title', 'anna-baylis' ); ?>"></p>
					<p>
						<input type="text" class="small-text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $i ); ?>][price]" value="<?php echo esc_attr( $plan['price'] ?? '' ); ?>" placeholder="$49">
						<input type="text" class="regular-text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $i ); ?>][price_suffix]" value="<?php echo esc_attr( $plan['price_suffix'] ?? '' ); ?>" placeholder="/ month AUD">
					</p>
					<p><input type="text" class="large-text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $i ); ?>][annual]" value="<?php echo esc_attr( $plan['annual'] ?? '' ); ?>"></p>
					<p><input type="text" class="large-text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $i ); ?>][founding]" value="<?php echo esc_attr( $plan['founding'] ?? '' ); ?>"></p>
					<p><input type="text" class="regular-text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $i ); ?>][badge]" value="<?php echo esc_attr( $plan['badge'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Badge (featured plan)', 'anna-baylis' ); ?>"></p>
					<p><label><input type="checkbox" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $i ); ?>][featured]" value="1" <?php checked( ! empty( $plan['featured'] ) ); ?>> <?php esc_html_e( 'Featured (dark card)', 'anna-baylis' ); ?></label></p>
					<p><textarea class="large-text" rows="6" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $i ); ?>][features_text]" placeholder="<?php esc_attr_e( 'One feature per line', 'anna-baylis' ); ?>"><?php
						if ( ! empty( $plan['features'] ) && is_array( $plan['features'] ) ) {
							$lines = array();
							foreach ( $plan['features'] as $f ) {
								$lines[] = $f['text'] ?? '';
							}
							echo esc_textarea( implode( "\n", $lines ) );
						}
					?></textarea></p>
				</div>
			<?php endforeach; ?>
			<p class="description"><?php esc_html_e( 'Edit both plans above. Use two plan blocks when saving.', 'anna-baylis' ); ?></p>
		</td>
	</tr>
	<?php
}
