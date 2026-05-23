<?php
/**
 * Custom Theme Settings Page
 */

class Anna_Theme_Settings {

	private $options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
		add_action( 'wp_head', array( $this, 'generate_css_variables' ) );
	}

	public function add_plugin_page() {
		add_theme_page(
			'Theme Settings', 
			'Theme Settings', 
			'manage_options', 
			'anna-theme-settings', 
			array( $this, 'create_admin_page' )
		);
	}

	public function create_admin_page() {
		$this->options = get_option( 'anna_theme_options' );
		?>
		<div class="wrap">
			<h1>Anna Baylis Theme Settings</h1>
			<form method="post" action="options.php">
			<?php
				settings_fields( 'anna_option_group' );
				do_settings_sections( 'anna-theme-settings' );
				submit_button();
			?>
			</form>
		</div>
		<?php
	}

	public function page_init() {
		register_setting(
			'anna_option_group', 
			'anna_theme_options', 
			array( $this, 'sanitize' )
		);

		add_settings_section(
			'setting_section_id', 
			'Global Colors', 
			array( $this, 'print_section_info' ), 
			'anna-theme-settings'
		);

		add_settings_field(
			'color_primary', 
			'Primary Color', 
			array( $this, 'color_primary_callback' ), 
			'anna-theme-settings', 
			'setting_section_id'
		);

		add_settings_field(
			'color_secondary', 
			'Secondary Color (Light Green)', 
			array( $this, 'color_secondary_callback' ), 
			'anna-theme-settings', 
			'setting_section_id'
		);
	}

	public function sanitize( $input ) {
		$new_input = array();
		if( isset( $input['color_primary'] ) )
			$new_input['color_primary'] = sanitize_hex_color( $input['color_primary'] );
		
		if( isset( $input['color_secondary'] ) )
			$new_input['color_secondary'] = sanitize_hex_color( $input['color_secondary'] );

		return $new_input;
	}

	public function print_section_info() {
		print 'Enter your global theme settings below:';
	}

	public function color_primary_callback() {
		$val = isset( $this->options['color_primary'] ) ? esc_attr( $this->options['color_primary']) : '#007063';
		printf(
			'<input type="color" id="color_primary" name="anna_theme_options[color_primary]" value="%s" />',
			$val
		);
	}

	public function color_secondary_callback() {
		$val = isset( $this->options['color_secondary'] ) ? esc_attr( $this->options['color_secondary']) : '#F2F6F2';
		printf(
			'<input type="color" id="color_secondary" name="anna_theme_options[color_secondary]" value="%s" />',
			$val
		);
	}

	public function generate_css_variables() {
		$options = get_option( 'anna_theme_options' );
		$primary = !empty($options['color_primary']) ? $options['color_primary'] : '#007063';
		$secondary = !empty($options['color_secondary']) ? $options['color_secondary'] : '#F2F6F2';
		
		echo "<style>
			:root {
				--color-primary: {$primary};
				--color-secondary: {$secondary};
			}
		</style>";
	}
}

if( is_admin() || !is_admin() ) {
	new Anna_Theme_Settings();
}
