<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property String.
 *
 * @package Papi
 */

class Papi_Property_String extends Papi_Property {

	/**
	 * The input type to use.
	 *
	 * @var string
	 */

	public $input_type = 'text';

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
		papi_render_html_tag( 'input', [
			'id'      => $this->html_id(),
			'name'    => $this->html_name(),
			'type'    => $this->input_type,
			'value'   => $this->get_value()
		] );
	}

}
