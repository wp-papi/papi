<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering property repeater.
 *
 * @package Papi
 */

class Papi_Property_Repeater_Test extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 *
	 * @since 1.0.0
	 */

	public function setUp() {
		parent::setUp();

		$_POST = array();

		$this->post_id = $this->factory->post->create();

		$this->property = papi_property( [
			'type'     => 'repeater',
			'title'    => 'Books',
			'slug'     => 'books',
			'settings' => [
				'items' => [
					papi_property( [
						'type'  => 'string',
						'title' => 'Book name',
						'slug'  => 'book_name'
					] ),
					papi_property( [
						'type'  => 'bool',
						'title' => 'Is open?',
						'slug'  => 'is_open'
					] )
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
	 * @since 1.0.0
	 */

	public function test_property_options() {
		// Test the property
		$this->assertEquals( 'repeater', $this->property->type );
		$this->assertEquals( 'papi_books', $this->property->slug );
		$this->assertFalse( empty( $this->property->settings->items ) );

		// Test the first item in the repeater
		$this->assertEquals( 'string', $this->property->settings->items[0]->type );
		$this->assertEquals( 'papi_book_name', $this->property->settings->items[0]->slug );
		$this->assertEquals( 'Book name', $this->property->settings->items[0]->title );

		// Test the second item in the repeater
		$this->assertEquals( 'bool', $this->property->settings->items[1]->type );
		$this->assertEquals( 'papi_is_open', $this->property->settings->items[1]->slug );
		$this->assertEquals( 'Is open?', $this->property->settings->items[1]->title );
	}

	/**
	 * Test save property value.
	 *
	 * @since 1.0.0
	 */

	public function test_save_property_value() {
		$handler = new Papi_Admin_Post_Handler();

		// Generate correct property meta key and property type meta key for string property.
		$value_slug1         = papi_remove_papi( $this->property->settings->items[0]->slug );
		$value_type_slug1    = papi_get_property_type_key( $value_slug1 );
		$value_slug2         = papi_remove_papi( $this->property->settings->items[1]->slug );
		$value_type_slug2    = papi_get_property_type_key( $value_slug2 );

		// Create the repeater item
		$item = array();
		$item[$value_slug1] = 'Harry Potter';
		$item[$value_type_slug1] = $this->property->settings->items[0];

		$item[$value_slug2] = '';
		$item[$value_type_slug2] = $this->property->settings->items[1];

		$values = array( $item );

		// Create post data.
		$_POST = papi_test_create_property_post_data( array(
			'slug'  => $this->property->slug,
			'type'  => $this->property,
			'value' => $values
		), $_POST );

		$handler->save_property( $this->post_id );

		// Rows
		$rows_html_name         = papi_ff( papify( $this->property->slug ) . '_rows' );
		$_POST[$rows_html_name] = 1;

		$expected = array( array( 'book_name' => 'Harry Potter', 'is_open' => false ) );
		$actual   = papi_field( $this->post_id, $this->property->slug, null, array(
			'property' => Papi_Property::create( (array) $this->property )
		) );

		$this->assertEquals( $expected, $actual );
	}

}
