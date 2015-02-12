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
