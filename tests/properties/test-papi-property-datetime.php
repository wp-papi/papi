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

class WP_Papi_Property_Datetime extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 *
	 * @since 1.0.0
	 */

	public function setUp() {
		parent::setUp();

		$this->post_id = $this->factory->post->create();

		$this->property = papi_property( array(
			'type'  => 'datetime',
			'title' => 'Date',
			'slug'  => 'date'
		) );
	}

	/**
	 * Test property options.
	 *
	 * @since 1.0.0
	 */

	public function test_property_options() {
		$this->assertEquals( 'datetime', $this->property->type );
		$this->assertEquals( 'Date', $this->property->title );
		$this->assertEquals( 'papi_date', $this->property->slug );
	}

	/**
	 * Test save property value.
	 *
	 * @since 1.0.0
	 */

	public function test_save_property_value() {
		$handler = new Papi_Admin_Meta_Boxes();

		// Create post data.
		$_POST = _papi_test_create_property_post_data(array(
			'slug'  => $this->property->slug,
			'type'  => $this->property->type,
			'value' => '2014-11-23'
		), $_POST);

		// Save the property using the handler.
		$handler->save_property( $this->post_id );

		// Test get the value with papi_field function.
		$expected = '2014-11-23';
		$actual   = papi_field( $this->post_id, $this->property->slug );

		$this->assertEquals( $expected, $actual );
	}

}
