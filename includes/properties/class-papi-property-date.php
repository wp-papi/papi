<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi - Property Date
 *
 * @package Papi
 * @version 1.0.0
 */
class Papi_Property_Date extends Papi_Property {

	/**
	 * Get default settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return array(
			'format' => 'YYYY-MM-DD'
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
		?>
		<input type="text" name="<?php echo $options->slug; ?>" value="<?php echo $value; ?>" class="papi-property-date" data-format="<?php echo $settings->format; ?>"/>
	<?php
	}

}
