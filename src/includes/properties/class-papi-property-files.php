<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Files.
 *
 * @package Papi
 */

class Papi_Property_Files extends Papi_Property_File {

	/**
	 * The convert type.
	 *
	 * @var string
	 */

	public $convert_type = 'array';

	/**
	 * The default value.
	 *
	 * @var array
	 */

	public $default_value = [];

	/**
	 * Get default settings.
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return [
			'multiple' => true
		];
	}

}