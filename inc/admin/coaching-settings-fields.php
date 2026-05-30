<?php
/**
 * Coaching page settings fields (repeaters).
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render coaching page settings tab fields.
 */
function anna_render_coaching_page_settings_fields() {
	anna_field_heading( __( 'Coaching Page Hero', 'anna-baylis' ), __( 'Content for the Coaching page template.', 'anna-baylis' ) );
	anna_field_text( 'coaching_pg_hero_eyebrow', __( 'Eyebrow', 'anna-baylis' ) );
	anna_field_textarea( 'coaching_pg_hero_heading', __( 'Heading', 'anna-baylis' ), __( 'Use line breaks for the hero layout.', 'anna-baylis' ), 4 );
	anna_field_textarea( 'coaching_pg_hero_description', __( 'Description', 'anna-baylis' ) );
	anna_field_textarea( 'coaching_pg_hero_tags_text', __( 'Hero Tags (pills)', 'anna-baylis' ), __( 'One tag per line.', 'anna-baylis' ), 5 );
	anna_field_media( 'coaching_pg_hero_image_id', __( 'Hero Background Image', 'anna-baylis' ) );
	anna_field_text( 'coaching_pg_hero_button_text', __( 'Button Text', 'anna-baylis' ) );
	anna_field_text( 'coaching_pg_hero_button_url', __( 'Button URL', 'anna-baylis' ), '', 'url' );

	anna_field_heading( __( 'What We Work On', 'anna-baylis' ) );
	anna_field_text( 'coaching_pg_work_eyebrow', __( 'Eyebrow', 'anna-baylis' ) );
	anna_field_text( 'coaching_pg_work_heading', __( 'Heading', 'anna-baylis' ) );
	anna_field_text( 'coaching_pg_work_gains_heading', __( 'Gains Column Heading', 'anna-baylis' ) );

	anna_render_coaching_text_repeater(
		'coaching_pg_work_topics_items',
		__( 'Session Topics', 'anna-baylis' ),
		__( 'Bulleted list in the left column.', 'anna-baylis' ),
		'coaching-topics'
	);

	anna_render_coaching_text_repeater(
		'coaching_pg_work_gains_items',
		__( 'What Clients Gain', 'anna-baylis' ),
		__( 'Use **word** to bold key terms in each line.', 'anna-baylis' ),
		'coaching-gains'
	);

	anna_field_heading( __( 'What to Expect', 'anna-baylis' ) );
	anna_field_text( 'coaching_pg_expect_eyebrow', __( 'Eyebrow (optional)', 'anna-baylis' ) );
	anna_field_text( 'coaching_pg_expect_heading_line1', __( 'Heading Line 1', 'anna-baylis' ) );
	anna_field_text( 'coaching_pg_expect_heading_line2', __( 'Heading Line 2', 'anna-baylis' ) );
	anna_field_textarea( 'coaching_pg_expect_body', __( 'Body Copy', 'anna-baylis' ), __( 'One paragraph per blank line.', 'anna-baylis' ), 8 );
	anna_field_textarea( 'coaching_pg_expect_quote', __( 'Quote', 'anna-baylis' ), '', 3 );
	anna_field_text( 'coaching_pg_expect_button_text', __( 'Button Text', 'anna-baylis' ) );
	anna_field_text( 'coaching_pg_expect_button_url', __( 'Button URL', 'anna-baylis' ), '', 'url' );

	anna_render_coaching_info_cards_repeater();

	anna_field_heading( __( 'Everything You Need to Know (FAQ)', 'anna-baylis' ) );
	anna_field_text( 'coaching_pg_faq_heading', __( 'Section Heading', 'anna-baylis' ) );
	anna_render_coaching_faq_repeater();
}

/**
 * Render a simple text repeater for coaching settings.
 *
 * @param string $option_key Option key.
 * @param string $label      Field label.
 * @param string $desc       Description.
 * @param string $slug       Repeater slug for JS hooks.
 */
function anna_render_coaching_text_repeater( $option_key, $label, $desc, $slug ) {
	$items = anna_get_option( $option_key, array() );
	if ( ! is_array( $items ) || empty( $items ) ) {
		$defaults = anna_get_default_options();
		$items    = $defaults[ $option_key ] ?? array();
	}
	$items = function_exists( 'anna_normalize_coaching_text_items' ) ? anna_normalize_coaching_text_items( $items ) : $items;
	$count = count( $items );
	?>
	<tr>
		<th scope="row"><?php echo esc_html( $label ); ?></th>
		<td>
			<?php if ( $desc ) : ?>
				<p class="description"><?php echo esc_html( $desc ); ?></p>
			<?php endif; ?>
			<div class="anna-repeater-collapse">
				<button type="button" class="anna-repeater-collapse__toggle" aria-expanded="false">
					<span class="anna-repeater-collapse__arrow" aria-hidden="true">▶</span>
					<span class="anna-repeater-collapse__label">
						<?php
						echo esc_html(
							sprintf(
								/* translators: %d: number of items */
								__( 'Show all items (%d)', 'anna-baylis' ),
								$count
							)
						);
						?>
					</span>
				</button>
				<div class="anna-repeater-collapse__panel is-collapsed" data-anna-repeater-collapse-panel="true">
					<div class="anna-admin-repeater" data-anna-repeater="<?php echo esc_attr( $slug ); ?>">
						<div class="anna-admin-repeater__rows" data-anna-repeater-rows="true">
							<?php foreach ( $items as $index => $item ) : ?>
								<?php $text = (string) ( $item['text'] ?? '' ); ?>
								<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
									<div class="anna-admin-repeater__row-fields">
										<div class="anna-admin-repeater__field">
											<label class="anna-admin-repeater__label"><?php esc_html_e( 'Text', 'anna-baylis' ); ?></label>
											<input type="text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $index ); ?>][text]" value="<?php echo esc_attr( $text ); ?>" class="large-text">
										</div>
									</div>
									<div class="anna-admin-repeater__row-actions">
										<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
						<button type="button" class="button" data-anna-repeater-add="true"><?php esc_html_e( 'Add Item', 'anna-baylis' ); ?></button>
						<template data-anna-repeater-template="true">
							<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
								<div class="anna-admin-repeater__row-fields">
									<div class="anna-admin-repeater__field">
										<label class="anna-admin-repeater__label"><?php esc_html_e( 'Text', 'anna-baylis' ); ?></label>
										<input type="text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][__INDEX__][text]" value="" class="large-text">
									</div>
								</div>
								<div class="anna-admin-repeater__row-actions">
									<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button>
								</div>
							</div>
						</template>
					</div>
				</div>
			</div>
		</td>
	</tr>
	<?php
}

/**
 * Render info cards repeater.
 */
function anna_render_coaching_info_cards_repeater() {
	$option_key = 'coaching_pg_expect_info_cards';
	$items      = anna_get_option( $option_key, array() );
	if ( ! is_array( $items ) || empty( $items ) ) {
		$defaults = anna_get_default_options();
		$items    = $defaults[ $option_key ] ?? array();
	}
	$items = function_exists( 'anna_normalize_coaching_info_cards' ) ? anna_normalize_coaching_info_cards( $items ) : $items;
	$count = count( $items );
	?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Info Cards', 'anna-baylis' ); ?></th>
		<td>
			<div class="anna-repeater-collapse">
				<button type="button" class="anna-repeater-collapse__toggle" aria-expanded="false">
					<span class="anna-repeater-collapse__arrow" aria-hidden="true">▶</span>
					<span class="anna-repeater-collapse__label"><?php echo esc_html( sprintf( __( 'Show all cards (%d)', 'anna-baylis' ), $count ) ); ?></span>
				</button>
				<div class="anna-repeater-collapse__panel is-collapsed" data-anna-repeater-collapse-panel="true">
					<div class="anna-admin-repeater" data-anna-repeater="coaching-info-cards">
						<div class="anna-admin-repeater__rows" data-anna-repeater-rows="true">
							<?php foreach ( $items as $index => $item ) : ?>
								<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
									<div class="anna-admin-repeater__row-fields">
										<div class="anna-admin-repeater__field">
											<label class="anna-admin-repeater__label"><?php esc_html_e( 'Label', 'anna-baylis' ); ?></label>
											<input type="text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $index ); ?>][label]" value="<?php echo esc_attr( $item['label'] ?? '' ); ?>" class="regular-text">
										</div>
										<div class="anna-admin-repeater__field">
											<label class="anna-admin-repeater__label"><?php esc_html_e( 'Body', 'anna-baylis' ); ?></label>
											<textarea name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $index ); ?>][body]" rows="3" class="large-text"><?php echo esc_textarea( $item['body'] ?? '' ); ?></textarea>
										</div>
									</div>
									<div class="anna-admin-repeater__row-actions">
										<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
						<button type="button" class="button" data-anna-repeater-add="true"><?php esc_html_e( 'Add Card', 'anna-baylis' ); ?></button>
						<template data-anna-repeater-template="true">
							<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
								<div class="anna-admin-repeater__row-fields">
									<div class="anna-admin-repeater__field">
										<label class="anna-admin-repeater__label"><?php esc_html_e( 'Label', 'anna-baylis' ); ?></label>
										<input type="text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][__INDEX__][label]" value="" class="regular-text">
									</div>
									<div class="anna-admin-repeater__field">
										<label class="anna-admin-repeater__label"><?php esc_html_e( 'Body', 'anna-baylis' ); ?></label>
										<textarea name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][__INDEX__][body]" rows="3" class="large-text"></textarea>
									</div>
								</div>
								<div class="anna-admin-repeater__row-actions">
									<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button>
								</div>
							</div>
						</template>
					</div>
				</div>
			</div>
		</td>
	</tr>
	<?php
}

/**
 * Render FAQ repeater.
 */
function anna_render_coaching_faq_repeater() {
	$option_key = 'coaching_pg_faq_items';
	$items      = anna_get_option( $option_key, array() );
	if ( ! is_array( $items ) || empty( $items ) ) {
		$defaults = anna_get_default_options();
		$items    = $defaults[ $option_key ] ?? array();
	}
	$items = function_exists( 'anna_normalize_coaching_faq_items' ) ? anna_normalize_coaching_faq_items( $items ) : $items;
	$count = count( $items );
	?>
	<tr>
		<th scope="row"><?php esc_html_e( 'FAQ Items', 'anna-baylis' ); ?></th>
		<td>
			<div class="anna-repeater-collapse">
				<button type="button" class="anna-repeater-collapse__toggle" aria-expanded="false">
					<span class="anna-repeater-collapse__arrow" aria-hidden="true">▶</span>
					<span class="anna-repeater-collapse__label"><?php echo esc_html( sprintf( __( 'Show all questions (%d)', 'anna-baylis' ), $count ) ); ?></span>
				</button>
				<div class="anna-repeater-collapse__panel is-collapsed" data-anna-repeater-collapse-panel="true">
					<div class="anna-admin-repeater" data-anna-repeater="coaching-faq">
						<div class="anna-admin-repeater__rows" data-anna-repeater-rows="true">
							<?php foreach ( $items as $index => $item ) : ?>
								<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
									<div class="anna-admin-repeater__row-fields">
										<div class="anna-admin-repeater__field">
											<label class="anna-admin-repeater__label"><?php esc_html_e( 'Question', 'anna-baylis' ); ?></label>
											<input type="text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $index ); ?>][question]" value="<?php echo esc_attr( $item['question'] ?? '' ); ?>" class="large-text">
										</div>
										<div class="anna-admin-repeater__field">
											<label class="anna-admin-repeater__label"><?php esc_html_e( 'Answer', 'anna-baylis' ); ?></label>
											<textarea name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][<?php echo esc_attr( $index ); ?>][answer]" rows="4" class="large-text"><?php echo esc_textarea( $item['answer'] ?? '' ); ?></textarea>
										</div>
									</div>
									<div class="anna-admin-repeater__row-actions">
										<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
						<button type="button" class="button" data-anna-repeater-add="true"><?php esc_html_e( 'Add Question', 'anna-baylis' ); ?></button>
						<template data-anna-repeater-template="true">
							<div class="anna-admin-repeater__row" data-anna-repeater-row="true">
								<div class="anna-admin-repeater__row-fields">
									<div class="anna-admin-repeater__field">
										<label class="anna-admin-repeater__label"><?php esc_html_e( 'Question', 'anna-baylis' ); ?></label>
										<input type="text" name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][__INDEX__][question]" value="" class="large-text">
									</div>
									<div class="anna-admin-repeater__field">
										<label class="anna-admin-repeater__label"><?php esc_html_e( 'Answer', 'anna-baylis' ); ?></label>
										<textarea name="anna_theme_options[<?php echo esc_attr( $option_key ); ?>][__INDEX__][answer]" rows="4" class="large-text"></textarea>
									</div>
								</div>
								<div class="anna-admin-repeater__row-actions">
									<button type="button" class="button-link-delete anna-admin-repeater__remove" data-anna-repeater-remove="true"><?php esc_html_e( 'Remove', 'anna-baylis' ); ?></button>
								</div>
							</div>
						</template>
					</div>
				</div>
			</div>
		</td>
	</tr>
	<?php
}
