<?php
/**
 * Admin Settings — Field Renderers
 *
 * Reusable field rendering functions for the admin panel.
 *
 * @package Anna_Baylis
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render a text input field.
 */
function anna_field_text( $key, $label, $desc = '', $type = 'text', $placeholder = '' ) {
	$value = anna_get_option( $key, '' );
	?>
	<tr>
		<th scope="row"><label for="anna-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
		<td>
			<input type="<?php echo esc_attr( $type ); ?>" id="anna-<?php echo esc_attr( $key ); ?>" name="anna_theme_options[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $value ); ?>" class="regular-text" placeholder="<?php echo esc_attr( $placeholder ); ?>">
			<?php if ( $desc ) : ?>
				<p class="description"><?php echo esc_html( $desc ); ?></p>
			<?php endif; ?>
		</td>
	</tr>
	<?php
}

/**
 * Render a textarea field.
 */
function anna_field_textarea( $key, $label, $desc = '', $rows = 4 ) {
	$value = anna_get_option( $key, '' );
	?>
	<tr>
		<th scope="row"><label for="anna-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
		<td>
			<textarea id="anna-<?php echo esc_attr( $key ); ?>" name="anna_theme_options[<?php echo esc_attr( $key ); ?>]" rows="<?php echo esc_attr( $rows ); ?>" class="large-text"><?php echo esc_textarea( $value ); ?></textarea>
			<?php if ( $desc ) : ?>
				<p class="description"><?php echo esc_html( $desc ); ?></p>
			<?php endif; ?>
		</td>
	</tr>
	<?php
}

/**
 * Render a color picker field.
 */
function anna_field_color( $key, $label, $default = '#007063' ) {
	$value = anna_get_option( $key, $default );
	?>
	<tr>
		<th scope="row"><label for="anna-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
		<td>
			<input type="text" id="anna-<?php echo esc_attr( $key ); ?>" name="anna_theme_options[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $value ); ?>" class="anna-color-picker" data-default-color="<?php echo esc_attr( $default ); ?>">
		</td>
	</tr>
	<?php
}

/**
 * Render a toggle/checkbox field.
 */
function anna_field_toggle( $key, $label, $desc = '' ) {
	$value = anna_get_option( $key, true );
	?>
	<tr>
		<th scope="row"><?php echo esc_html( $label ); ?></th>
		<td>
			<label for="anna-<?php echo esc_attr( $key ); ?>" class="anna-toggle-label">
				<input type="checkbox" id="anna-<?php echo esc_attr( $key ); ?>" name="anna_theme_options[<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( $value, true ); ?>>
				<?php if ( $desc ) : ?>
					<span class="description"><?php echo esc_html( $desc ); ?></span>
				<?php endif; ?>
			</label>
		</td>
	</tr>
	<?php
}

/**
 * Render a select field.
 */
function anna_field_select( $key, $label, $options, $desc = '' ) {
	$value = anna_get_option( $key, '' );
	?>
	<tr>
		<th scope="row"><label for="anna-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
		<td>
			<select id="anna-<?php echo esc_attr( $key ); ?>" name="anna_theme_options[<?php echo esc_attr( $key ); ?>]">
				<?php foreach ( $options as $val => $label_text ) : ?>
					<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $value, $val ); ?>><?php echo esc_html( $label_text ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php if ( $desc ) : ?>
				<p class="description"><?php echo esc_html( $desc ); ?></p>
			<?php endif; ?>
		</td>
	</tr>
	<?php
}

/**
 * Render a media upload field (uses WP media library).
 */
function anna_field_media( $key, $label, $desc = '' ) {
	$value = anna_get_option( $key, '' );
	$image_url = $value ? wp_get_attachment_image_url( absint( $value ), 'thumbnail' ) : '';
	?>
	<tr>
		<th scope="row"><label for="anna-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
		<td>
			<div class="anna-media-field">
				<input type="hidden" id="anna-<?php echo esc_attr( $key ); ?>" name="anna_theme_options[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $value ); ?>">
				<div class="anna-media-preview" id="anna-preview-<?php echo esc_attr( $key ); ?>">
					<?php if ( $image_url ) : ?>
						<img src="<?php echo esc_url( $image_url ); ?>" alt="" style="max-width:150px;height:auto;border-radius:8px;">
					<?php endif; ?>
				</div>
				<button type="button" class="button anna-media-upload-btn" data-target="anna-<?php echo esc_attr( $key ); ?>" data-preview="anna-preview-<?php echo esc_attr( $key ); ?>">
					<?php esc_html_e( 'Select Image', 'anna-baylis' ); ?>
				</button>
				<button type="button" class="button anna-media-remove-btn" data-target="anna-<?php echo esc_attr( $key ); ?>" data-preview="anna-preview-<?php echo esc_attr( $key ); ?>" <?php echo ! $value ? 'style="display:none;"' : ''; ?>>
					<?php esc_html_e( 'Remove', 'anna-baylis' ); ?>
				</button>
			</div>
			<?php if ( $desc ) : ?>
				<p class="description"><?php echo esc_html( $desc ); ?></p>
			<?php endif; ?>
		</td>
	</tr>
	<?php
}

/**
 * Render a section heading within the settings form.
 */
function anna_field_heading( $title, $desc = '' ) {
	?>
	<tr>
		<td colspan="2">
			<h3 class="anna-admin-section-title"><?php echo esc_html( $title ); ?></h3>
			<?php if ( $desc ) : ?>
				<p class="description"><?php echo esc_html( $desc ); ?></p>
			<?php endif; ?>
		</td>
	</tr>
	<?php
}
