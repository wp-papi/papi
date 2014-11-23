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

class WP_Papi_Property_Image extends WP_UnitTestCase {

	/**
	* Setup the test.
	*
	* @since 1.0.0
	*/

	public function setUp() {
		parent::setUp();

		$this->post_id = $this->factory->post->create();

		$this->property = papi_property( array(
			'type'  => 'image',
			'title' => 'Image',
			'slug'  => 'image'
		) );
	}

	/**
	* Test property options.
	*
	* @since 1.0.0
	*/

	public function test_property_options() {
		// Test the property
		$this->assertEquals( 'image', $this->property->type );
		$this->assertEquals( 'Image', $this->property->title );
		$this->assertEquals( 'papi_image', $this->property->slug );
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
			'value' => 23
		), $_POST);

		// Save the property using the handler.
		$handler->save_property( $this->post_id );

		// Test get the value with papi_field function.
		// Property image can return the post image id if dosen't find the attachment.
		$expected = 23;
		$actual   = papi_field( $this->post_id, $this->property->slug );

		$this->assertEquals( $expected, $actual );
	}

}
