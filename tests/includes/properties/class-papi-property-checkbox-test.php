<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering property checkbox.
 *
 * @package Papi
 */

class Papi_Property_Checkbox_Test extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 *
	 * @since 1.0.0
	 */

	public function setUp() {
		parent::setUp();

		$_POST = [];

		$this->post_id = $this->factory->post->create();

		$this->property = papi_property( [
			'type'     => 'checkbox',
			'title'    => 'Color',
			'slug'     => 'color',
			'settings' => [
				'items' => [
					'White' => '#ffffff',
					'Black' => '#000000'
				]
			]
		] );
	}

	/**
	 * Tear down test.
	 *
	 * @since 1.3.0
	 */

	public function tearDown() {
		parent::tearDown();
		$_POST = [];
		unset( $this->post_id, $this->property );
	}

	/**
	 * Test `format_value` method.
	 *
	 * @since 1.3.0
	 */

	public function test_format_value() {
		$property_class = papi_get_property_type( 'checkbox' );
		$this->assertEquals( [ 'hello' ], $property_class->format_value( 'hello', '', 0 ) );
		$this->assertEquals( [ 'hello' ], $property_class->format_value( [ 'hello' ], '', 0 ) );
		$this->assertEmpty( $property_class->format_value( null, '', 0 ) );
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
		$this->assertEquals( 'checkbox', $this->property->type );
		$this->assertEquals( 'papi_color', $this->property->slug );
		$this->assertFalse( empty( $this->property->settings->items ) );
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
			'value' => '#ffffff'
		], $_POST );

		$handler->save_property( $this->post_id );

		$expected = [ '#ffffff' ];
		$actual = papi_field( $this->post_id, $this->property->slug );

		$this->assertEquals( $expected, $actual );
	}

}
