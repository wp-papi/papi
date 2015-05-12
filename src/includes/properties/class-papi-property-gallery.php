<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property Gallery.
 *
 * @package Papi
 * @since 1.2.0
 */

class Papi_Property_Gallery extends Papi_Property_Image {

	/**
	 * The default value.
	 *
	 * @var array
	 * @since 1.3.0
	 */

	public $default_value = array();

	/**
	 * Get default settings.
	 *
	 * @since 1.2.0
	 *
	 * @return array
	 */

	public function get_default_settings() {
		return array(
			'gallery' => true
		);
	}

}
