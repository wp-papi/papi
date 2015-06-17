<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Property number.
 *
 * @package Papi
 */

class Papi_Property_Number extends Papi_Property_String {

	/**
	 * The convert type.
	 *
	 * @var string
	 */

	public $convert_type = 'int';

	/**
	 * The input type to use.
	 *
	 * @var string
	 */

	public $input_type = 'number';

	/**
	 * Format the value of the property before it's returned to the theme.
	 *
	 * @param mixed $value
	 * @param string $slug
	 * @param int $post_id
	 *
	 * @return array
	 */

	public function format_value( $value, $slug, $post_id ) {
		if ( floatval( $value ) && intval( $value ) !== floatval( $value ) ) {
			return floatval( $value );
		} else {
			return intval( $value );
		}
	}
}
