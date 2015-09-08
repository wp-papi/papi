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
	 * Render property html.
	 */
	public function html() {
		$settings = $this->get_settings();

		papi_render_html_tag( 'div', [
			'data-papi-rule' => $this->html_name(),
			'class'          => 'property-html',
			papi_maybe_get_callable_value( $settings->html )
		] );
	}
}
