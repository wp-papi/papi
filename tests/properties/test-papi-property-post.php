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

class WP_Papi_Property_Post extends WP_UnitTestCase {

	/**
	 * Setup the test and.
	 *
	 * @since 1.0.0
	 */

	public function setUp() {
		parent::setUp();

		$_POST = array();

		$this->post_id = $this->factory->post->create();

		$this->property = papi_property( array(
			'type'  => 'post',
			'title' => 'The big post',
			'slug'  => 'the_big_post'
		) );
	}

	/**
	 * Test property options.
	 *
	 * @since 1.0.0
	 */

	public function test_property_options() {
		// Test the property
		$this->assertEquals( 'post', $this->property->type );
		$this->assertEquals( 'The big post', $this->property->title );
		$this->assertEquals( 'papi_the_big_post', $this->property->slug );

		// Test default settings
		$this->assertEquals( 'post', $this->property->settings->post_type );
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
			'value' => $this->post_id
		), $_POST);

		// Save the property using the handler.
		$handler->save_property( $this->post_id );

		// Test get the value with papi_field function.
		$expected = get_post( $this->post_id );
		$actual   = papi_field( $this->post_id, $this->property->slug );

		$this->assertEquals( $expected, $actual );
	}

}
