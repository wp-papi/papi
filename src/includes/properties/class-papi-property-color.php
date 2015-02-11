<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Property Color
 *
 * @since 1.1.0
 */

class Papi_Property_Color extends Papi_Property {

	/**
	 * Get default settings.
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return array(
			'palettes'   => array(),
			'show_input' => true
		);
	}

	/**
	 * Generate the HTML for the property.
	 *
	 * @since 1.1.0
	 */

	public function html() {
		$options  = $this->get_options();
		$settings = $this->get_settings();
		$value    = $this->get_value();

		?>
			<div class="papi-property-color-picker">
				<input type="<?php echo $settings->show_input === true ? 'text' : 'hidden'; ?>"
				value="<?php echo $value; ?>" data-palettes='<?php echo json_encode( $settings->palettes ); ?>'
				name="<?php echo $options->slug; ?>" />
			</div>
		<?php
	}
}
