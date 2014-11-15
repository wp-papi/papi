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

	/**
	 * Test to render a property.
	 * Should output HTML when running PHPUnit.
	 *
	 * @since 1.0.0
	 */

	public function test_render_property() {
		$property = _papi_get_property_options( array(
			'type'  => 'PropertyString',
			'title' => 'Heading',
			'slug'  => 'heading'
		) );

		_papi_render_property( $property );
	}

}
