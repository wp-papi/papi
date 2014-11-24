<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi Property number.
 *
 * @package Papi
 * @version 1.0.0
 */

class Papi_Property_Number extends Papi_Property_String {

	/**
	 * The default value.
	 *
	 * @var int
	 * @since 1.0.0
	 */

	public $default_value = 0;

	/**
	 * The input type to use.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	public $input_type = 'number';

	/**
	 * Format the value of the property before we output it to the application.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	public function format_value( $value, $slug, $post_id ) {
		if ( floatval( $value ) && intval( $value ) != floatval( $value ) ) {
			return floatval( $value );
		} else {
			return intval( $value );
		}
	}
}
