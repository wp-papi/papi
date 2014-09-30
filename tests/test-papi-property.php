<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Unit tests covering property functionality.
 *
 * @package Papi
 */
class WP_Papi_Property extends WP_UnitTestCase {

	public function test_render_property() {
		$property = _papi_get_property_options( array(
			'type'  => 'PropertyString',
			'title' => 'Heading',
			'slug'  => 'heading'
		) );

		_papi_render_property( $property );
	}

}
