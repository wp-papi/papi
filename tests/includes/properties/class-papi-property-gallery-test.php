<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
* Unit tests covering page type gallery.
*
* @package Papi
*/

class Papi_Property_Gallery_Test extends WP_UnitTestCase {

	/**
	* Setup the test.
	*
	* @since 1.3.0
	*/

	public function setUp() {
		parent::setUp();

		$this->post_id = $this->factory->post->create();

		$this->property = papi_property( array(
			'type'  => 'gallery',
			'title' => 'Images',
			'slug'  => 'images'
		) );
	}

	/**
	 * Tear down test.
	 *
	 * @since 1.3.0
	 */

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id, $this->property );
	}

	/**
	 * Test output to check if property slug exists and the property type value.
	 *
	 * @since 1.3.0
	 */

	public function test_output() {
		papi_render_property( $this->property );
		$this->expectOutputRegex( '/name=\"' . papi_get_property_type_key( $this->property->slug ) . '\"' );
		$this->expectOutputRegex( '/value=\"gallery\"/' );
	}

	/**
	* Test property options.
	*
	* @since 1.3.0
	*/

	public function test_property_options() {
		// Test the property
		$this->assertEquals( 'gallery', $this->property->type );
		$this->assertEquals( 'Images', $this->property->title );
		$this->assertEquals( 'papi_images', $this->property->slug );
		$this->assertTrue( $this->property->settings->gallery );
	}

	/**
	* Test save property value.
	*
	* @since 1.3.0
	*/

	public function test_save_property_value() {
		$handler = new Papi_Admin_Meta_Boxes();

		// Create post data.
		$_POST = papi_test_create_property_post_data( array(
			'slug'  => $this->property->slug,
			'type'  => $this->property->type,
			'value' => array( 23 )
		), $_POST );

		// Save the property using the handler.
		$handler->save_property( $this->post_id );

		// Test get the value with papi_field function.
		// Property image can return the post image id if dosen't find the attachment.
		$expected = array( 23 );
		$actual   = papi_field( $this->post_id, $this->property->slug );

		$this->assertEquals( $expected, $actual );
	}

}
