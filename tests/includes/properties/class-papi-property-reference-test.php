<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering property reference.
 *
 * @package Papi
 */

class Papi_Property_Reference_Test extends WP_UnitTestCase {

	/**
	 * Setup the test and.
	 *
	 * @since 1.2.0
	 */

	public function setUp() {
		parent::setUp();

		$_POST = array();

		$this->post_id = $this->factory->post->create();

		$this->property = papi_property( array(
			'type'  => 'reference',
			'title' => 'References',
			'slug'  => 'references'
		) );
	}

	/**
	 * Tear down test.
	 *
	 * @since 1.3.0
	 */

	public function tearDown() {
		parent::tearDown();
		$_POST = array();
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
	 * @since 1.2.0
	 */

	public function test_property_options() {
		// Test the property
		$this->assertEquals( 'reference', $this->property->type );
		$this->assertEquals( 'References', $this->property->title );
		$this->assertEquals( 'papi_references', $this->property->slug );

		// Test default settings
		$this->assertEquals( true, is_array( $this->property->settings->slug ) );
		$this->assertEquals( true, is_array( $this->property->settings->page_type ) );
	}

	/**
	 * Test save property value.
	 *
	 * @since 1.2.0
	 */

	public function test_save_property_value() {
		$handler = new Papi_Admin_Meta_Boxes();

		// Create post data.
		$_POST = papi_test_create_property_post_data( array(
			'slug'  => $this->property->slug,
			'type'  => $this->property,
			'value' => null
		), $_POST );

		// Save the property using the handler.
		$handler->save_property( $this->post_id );

		// Test get the value with papi_field function.
		$expected = null;
		$actual   = papi_field( $this->post_id, $this->property->slug );

		$this->assertEquals( $expected, $actual );
	}

}
