<?php

/**
 * Datetime property.
 */
class Papi_Property_Datetime extends Papi_Property {

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return [
			'format'           => 'YYYY-MM-DD hh:mm:ss',
			'show_seconds'     => false,
			'show_time'        => true,
			'show_week_number' => false,
			'use_24_hours'     => get_locale() === 'sv_SE'
		];
	}

	/**
	 * Render property html.
	 */
	public function html() {
		$settings = $this->get_settings();
		$value    = $this->get_value();

		$settings_json = [
			'i18n' => [
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
			]
		];

		// Remove i18n setting if it exists.
		if ( isset( $settings->i18n ) ) {
			unset( $settings->i18n );
		}

		// Remove default time format if show time is false.
		if ( isset( $settings->show_time ) && ! $settings->show_time && isset( $settings->format ) ) {
			$settings->format = trim( str_replace( 'hh:mm:ss', '', $settings->format ) );
		}

		// Convert all sneak case key to camel case.
		foreach ( (array) $settings as $key => $val ) {
			if ( ! is_string( $key ) ) {
				continue;
			}

			if ( $key = papi_camel_case( $key ) ) {
				$settings_json[$key] = $val;
			}
		}

		// Papi has `use24Hours` as key and Pikaday has `use24hour`.
		// This code will fix it.
		if ( isset( $settings_json['use24Hours'] ) ) {
			$settings_json['use24hour'] = $settings_json['use24Hours'];
			unset( $settings_json['use24Hours'] );
		}

		papi_render_html_tag( 'input', [
			'class'         => 'papi-property-datetime',
			'data-settings' => (object) $settings_json,
			'id'            => $this->html_id(),
			'name'          => $this->html_name(),
			'type'          => 'text',
			'value'         => $value
		] );
	}
}
