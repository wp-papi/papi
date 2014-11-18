<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Unit tests covering page type functionality.
 *
 * @package Papi
 */

class WP_Papi_Property_Number extends WP_UnitTestCase {

	/**
	 * Setup the test and register the page types directory.
	 *
	 * @since 1.0.0
	 */

	public function setUp() {
		parent::setUp();

		$this->post_id = $this->factory->post->create();
	}

	/**
	 * Test property options.
	 *
	 * @since 1.0.0
	 */

	public function test_property_options() {
		$property = papi_property( array(
			'type'  => 'number',
			'title' => 'Age',
			'slug'  => 'age'
		) );

		// Test the property
		$this->assertEquals( $property->type, 'number' );
		$this->assertEquals( $property->title, 'Age' );
		$this->assertEquals( $property->slug, 'papi_age' );

		// Add property value
		add_post_meta( $this->post_id, _papi_remove_papi( $property->slug ), 23 );

		// Add property type value
		$slug_type = _papi_get_property_type_key_f( $property->slug );
		add_post_meta( $this->post_id, $slug_type, $property->type );

		// Test get the value with papi_field function.
		$this->assertEquals( papi_field( $this->post_id, _papi_remove_papi( $property->slug ) ), 23 );
	}

}
