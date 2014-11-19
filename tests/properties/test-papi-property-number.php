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

		$this->property = papi_property( array(
			'type'  => 'number',
			'title' => 'Age',
			'slug'  => 'age'
		) );
	}

	/**
	 * Test property options.
	 *
	 * @since 1.0.0
	 */

	public function test_property_options() {
		// Test the property
		$this->assertEquals( $this->property->type, 'number' );
		$this->assertEquals( $this->property->title, 'Age' );
		$this->assertEquals( $this->property->slug, 'papi_age' );
	}

	/**
	 * Test save property value.
	 *
	 * @since 1.0.0
	 */

	public function test_save_property_value () {
		// Save the property
		_papi_property_update_value( array(
			'post_id' => $this->post_id,
			'slug'    => $this->property->slug,
			'type'    => $this->property->type,
			'value'   => 23
		) );

		// Test get the value with papi_field function.
		$this->assertEquals( papi_field( $this->post_id, $this->property->slug ), 23 );
	}

}
