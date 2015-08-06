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
			'multiple' => true
		];
	}

	/**
	 * Import value to the property.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @return mixed
	 */
	public function import_value( $value, $slug, $post_id ) {
		if ( ! is_array( $value ) ) {
			return parent::import_value( $value, $slug, $post_id );
		}

		foreach ( $value as $index => $image ) {
			$value[$index] = parent::import_value( $image, $slug, $post_id );
		}

		return $value;
	}

}
