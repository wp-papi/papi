<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Gallery class.
 *
 * @package Papi
 */

class Papi_Property_Gallery extends Papi_Property_Image {

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
			'gallery' => true
		];
	}

}
