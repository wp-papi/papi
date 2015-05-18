<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering property editor.
 *
 * @package Papi
 */

class Papi_Property_Editor_Test extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 *
	 * @since 1.3.0
	 */

	public function setUp() {
		parent::setUp();

		$this->post_id = $this->factory->post->create();

		$this->property = papi_property( [
			'type'  => 'editor',
			'title' => 'Text',
			'slug'  => 'editor'
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
	 * @since 1.3.0
	 */

	public function test_property_options() {
		$this->assertEquals( 'editor', $this->property->type );
		$this->assertEquals( 'Text', $this->property->title );
		$this->assertEquals( 'papi_editor', $this->property->slug );
	}

	/**
	 * Test save property value.
	 *
	 * @since 1.3.0
	 */

	public function test_save_property_value() {
		$handler = new Papi_Admin_Post_Handler();

		// Create post data.
		$_POST = papi_test_create_property_post_data( [
			'slug'  => $this->property->slug,
			'type'  => $this->property,
			'value' => '<p>a bit of text with html tags hello, world</p>'
		], $_POST );

		// Save the property using the handler.
		$handler->save_property( $this->post_id );

		// Test get the value with papi_field function.
		$expected = "<p>a bit of text with html tags hello, world</p>\n";
		$actual   = papi_field( $this->post_id, $this->property->slug );

		$this->assertEquals( $expected, $actual );
	}

}
