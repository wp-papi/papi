<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi Property Datetime.
 *
 * @package Papi
 * @version 1.0.0
 */

class Papi_Property_Datetime extends Papi_Property {

	/**
	 * Get default settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return array(
			'format'       => 'YYYY-MM-DD hh:mm:ss',
			'show_seconds' => false,
			'show_time'    => true,
			'use_24_hours' => false
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

		$settings_json = array(
			'format'      => $settings->format,
			'showTime'    => $settings->show_time,
			'showSeconds' => $settings->show_seconds,
			'use24hour'   => $settings->use_24_hours
		);

		$settings_json = json_encode( (object)$settings_json );

		?>
		<input type="text" name="<?php echo $options->slug; ?>" value="<?php echo $value; ?>" class="papi-property-datetime" data-settings='<?php echo $settings_json; ?>'/>
	<?php
	}

}
