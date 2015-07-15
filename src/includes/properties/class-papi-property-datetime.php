<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Datetime.
 *
 * @package Papi
 */

class Papi_Property_Datetime extends Papi_Property {

	/**
	 * Get default settings.
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return [
			'format'       => 'YYYY-MM-DD hh:mm:ss',
			'show_seconds' => false,
			'show_time'    => true,
			'use_24_hours' => false
		];
	}

	/**
	 * Display property html.
	 */

	public function html() {
		$settings = $this->get_settings();
		$value    = $this->get_value();

		$settings_json = [
			'format'      => $settings->format,
			'showTime'    => $settings->show_time,
			'showSeconds' => $settings->show_seconds,
			'use24hour'   => $settings->use_24_hours
		];

		$settings_json = json_encode( (object) $settings_json );

		?>
		<input
			type="text"
			id="<?php echo $this->html_name(); ?>"
			name="<?php echo $this->html_name(); ?>"
			value="<?php echo $value; ?>"
			class="papi-property-datetime"
			data-settings='<?php echo $settings_json; ?>'/>
	<?php
	}

}
