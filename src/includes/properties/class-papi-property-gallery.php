<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Gallery.
 *
 * @package Papi
 */

class Papi_Property_Gallery extends Papi_Property_Image {

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
