<?php

/**
 * WordPress media file property as a gallery.
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
			'multiple' => true
		];
	}
}
