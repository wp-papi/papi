<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering property string.
 *
 * @package Papi
 */

class Papi_Property_String_Test extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 *
	 * @since 1.0.0
	 */

	public function setUp() {
		parent::setUp();

		$this->post_id = $this->factory->post->create();

		$this->property = papi_property( [
			'type'  => 'string',
			'title' => 'Name',
			'slug'  => 'name'
		] );
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
		$this->expectOutputRegex( '/data\-property=\"' . $this->property->type . '\"/' );
	}

	/**
	 * Test property options.
	 *
	 * @since 1.0.0
	 */

	public function test_property_options() {
		$this->assertEquals( 'string', $this->property->type );
		$this->assertEquals( 'Name', $this->property->title );
		$this->assertEquals( 'papi_name', $this->property->slug );
	}

	/**
	 * Test save property value.
	 *
	 * @since 1.0.0
	 */

	public function test_save_property_value() {
		$handler = new Papi_Admin_Post_Handler();

		// Create post data.
		$_POST = papi_test_create_property_post_data( [
			'slug'  => $this->property->slug,
			'type'  => $this->property,
			'value' => 'fredrik'
		], $_POST );

		// Save the property using the handler.
		$handler->save_property( $this->post_id );

		// Test get the value with papi_field function.
		$expected = 'fredrik';
		$actual   = papi_field( $this->post_id, $this->property->slug );

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test `allow_html` setting.
	 *
	 * @since 1.3.0
	 */

	public function test_setting_allow_html() {
		$property = papi_property( [
			'allow_html' => true,
			'type'       => 'string',
			'title'      => 'Name',
			'slug'       => 'name'
		] );

		$handler  = new Papi_Admin_Post_Handler();
		$expected = '<p>Hello, world</p>';

		// Create post data.
		$_POST = papi_test_create_property_post_data( [
			'slug'  => $property->slug,
			'type'  => $property,
			'value' => $expected
		], $_POST );

		$handler->save_property( $this->post_id );
		$actual = papi_field( $this->post_id, $property->slug );
		$this->assertEquals( $expected, $actual );
	}

}
