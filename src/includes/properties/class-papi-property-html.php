<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Html.
 *
 * @package Papi
 * @since 1.2.0
 */

class Papi_Property_Html extends Papi_Property {

	/**
	 * Get default settings.
	 *
	 * @since 1.2.0
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return array(
			'html' => ''
		);
	}

	/**
	 * Generate the HTML for the property.
	 *
	 * @since 1.2.0
	 */

	public function html() {
		$settings = $this->get_settings();

		if ( is_callable( $settings->html ) ) {
			call_user_func( $settings->html );
		} else {
			echo papi_convert_to_string( $settings->html );
		}
	}

}
