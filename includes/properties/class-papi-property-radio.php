<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi Property Radio.
 *
 * @package Papi
 * @version 1.0.0
 */

class Papi_Property_Radio extends Papi_Property {

	/**
	 * Get default settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return array(
			'items'    => array(),
			'selected' => ''
		);
	}

	/**
	 * Generate the HTML for the property.
	 *
	 * @since 1.0.0
	 */

	public function html() {
		$options  = $this->get_options();
		$settings = $this->get_settings();
		$value    = $this->get_value();

		// Override selected setting with
		// database value if not null.
		if ( ! empty( $value ) ) {
			$settings->selected = $value;
		}

		foreach ( $settings->items as $key => $value ) {

			if ( is_numeric( $key ) ) {
				$key = $value;
			}

			?>
			<input type="radio" value="<?php echo $value ?>"
			       name="<?php echo $options->slug; ?>" <?php echo $value == $settings->selected ? 'checked="checked"' : ''; ?> />
			<?php
			echo $key . '<br />';
		}
	}
}
