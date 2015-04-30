<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property String.
 *
 * @package Papi
 * @since 1.0.0
 */

class Papi_Property_String extends Papi_Property {

	/**
	 * The input type to use.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	public $input_type = 'text';

	/**
	 * The default value.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	public $default_value = '';

	/**
	 * Get default settings.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return array(
			'allow_html' => false
		);
	}

	/**
	 * Generate the HTML for the property.
	 *
	 * @since 1.0.0
	 */

	public function html() {
		$options = $this->get_options();
		$value   = $this->get_value();

		?>
		<input type="<?php echo $this->input_type; ?>" name="<?php echo $options->slug; ?>"
		       value="<?php echo $value; ?>" />
	<?php
	}

}
