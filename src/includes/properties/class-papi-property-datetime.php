<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Datetime class.
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
			'i18n'        => [
				'previousMonth' => __( 'Previous Month', 'papi' ),
				'nextMonth'     => __( 'Next Month', 'papi' ),
				'midnight'      => __( 'Midnight', 'papi' ),
				'months'        => [
					__( 'January', 'papi' ),
					__( 'February', 'papi' ),
					__( 'March', 'papi' ),
					__( 'April', 'papi' ),
					__( 'May', 'papi' ),
					__( 'June', 'papi' ),
					__( 'July', 'papi' ),
					__( 'August', 'papi' ),
					__( 'September', 'papi' ),
					__( 'October', 'papi' ),
					__( 'November', 'papi' ),
					__( 'December', 'papi' )
				],
				'noon'          => __( 'Noon', 'papi' ),
				'weekdays'      => [
					__( 'Sunday', 'papi' ),
					__( 'Monday', 'papi' ),
					__( 'Tuesday', 'papi' ),
					__( 'Wednesday', 'papi' ),
					__( 'Thursday', 'papi' ),
					__( 'Friday', 'papi' ),
					__( 'Saturday', 'papi' )
				],
				'weekdaysShort' => [
					__( 'Sun', 'papi' ),
					__( 'Mon', 'papi' ),
					__( 'Tue', 'papi' ),
					__( 'Wed', 'papi' ),
					__( 'Thu', 'papi' ),
					__( 'Fri', 'papi' ),
					__( 'Sat', 'papi' )
				]
			],
			'showTime'    => $settings->show_time,
			'showSeconds' => $settings->show_seconds,
			'use24hour'   => $this->use_24_hours(),
		];

		papi_render_html_tag( 'input', [
			'class'         => 'papi-property-datetime',
			'data-settings' => (object) $settings_json,
			'id'            => $this->html_id(),
			'name'          => $this->html_name(),
			'type'          => 'text',
			'value'         => $value
		] );
	}

	/**
	 * Check if 24 hours should be used or not.
	 *
	 * @return bool
	 */
	protected function use_24_hours() {
		return get_locale() === 'sv_SE' || $this->get_setting( 'use_24_hours' );
	}
}
