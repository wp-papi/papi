<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Option Page.
 *
 * @package Papi
 */

class Papi_Option_Page extends Papi_Page_Manager {

	/**
	 * Data type to describe which
	 * type of page data is it.
	 *
	 * @var string
	 */

	protected $data_type = 'option';

	/**
	 * Get property value.
	 *
	 * @param string $slug
	 *
	 * @return mixed
	 */

	public function get_value( $slug ) {
		$property_value      = get_option( $slug );
		$property_type_key   = papi_get_property_type_key_f( $slug, true );
		$property_type_value = get_option( $property_type_key );

		if ( papi_is_empty( $property_value ) || empty( $property_type_value ) ) {
			return;
		}

		$property_value = $this->convert(
			$slug,
			$property_type_value,
			$property_value
		);

		if ( is_array( $property_value ) ) {
			$property_value = array_filter( $property_value );
		}

		return $property_value;
	}

	/**
	 * Check if it's a valid page.
	 *
	 * @return bool
	 */

	public function valid() {
		return $this->id === 0 && $this->valid_data_type();
	}

}
