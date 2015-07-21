<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Text class.
 *
 * @package Papi
 */

class Papi_Property_Text extends Papi_Property {

	/**
	 * Get default settings.
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return [
			'allow_html' => false
		];
	}

	/**
	 * Display property html.
	 */

	public function html() {
		papi_render_html_tag( 'textarea', [
			'class' => 'papi-property-text',
			'id'    => $this->html_id(),
			'name'  => $this->html_name(),
			sanitize_text_field( $this->get_value() )
		] );
	}
}
