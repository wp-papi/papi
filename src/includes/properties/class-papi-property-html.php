<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Html class.
 *
 * @package Papi
 */

class Papi_Property_Html extends Papi_Property {

	/**
	 * Get default settings.
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return [
			'html' => ''
		];
	}

	/**
	 * Display property html.
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
