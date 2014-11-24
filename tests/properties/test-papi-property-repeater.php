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

class WP_Papi_Property_Repeater extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 *
	 * @since 1.0.0
	 */

	public function setUp() {
		parent::setUp();

		$_POST = array();

		$this->post_id = $this->factory->post->create();

		$this->property = papi_property( array(
			'type'     => 'repeater',
			'title'    => 'Books',
			'slug'     => 'books',
			'settings' => array(
				'items' => array(
					papi_property( array(
						'type'  => 'string',
						'title' => 'Book name',
						'slug'  => 'book_name'
					) ),
					papi_property( array(
						'type'  => 'bool',
						'title' => 'Is open?',
						'slug'  => 'is_open'
					) )
				)
			)
		) );
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
		$handler = new Papi_Admin_Meta_Boxes();

		// Generate correct property meta key and property type meta key for string property.
		$value_slug1         = _papi_remove_papi( $this->property->settings->items[0]->slug );
		$value_type_slug1    = _papi_get_property_type_key( $value_slug1 );
		$value_slug2         = _papi_remove_papi( $this->property->settings->items[1]->slug );
		$value_type_slug2    = _papi_get_property_type_key( $value_slug2 );
		$property_type_slug  = _papi_html_name( _papi_get_property_type_key( $this->property->slug ) );

		// Create the repeater item
		$item = array();
		$item[$value_slug1] = 'Harry Potter';
		$item[$value_type_slug1] = $this->property->settings->items[0]->type;

		$item[$value_slug2] = '';
		$item[$value_type_slug2] = $this->property->settings->items[1]->type;

		$values = array( $item );

		$properties = array_map( function ($item) {
			foreach ($item as $key => $val) {
				if (_papi_is_property_type_key($key)) {
					continue;
				}
				$item[$key] = '';
			}
			return $item;
		}, $values);

		// Create post data.
		$_POST = _papi_test_create_property_post_data( array(
			'slug'  => $this->property->slug,
			'type'  => $this->property->type,
			'value' => $values
		), $_POST );

		$handler->save_property( $this->post_id );

		// Property repeater will save this value that tells how many columns there is on a row.
		// The test needs to save this value manually.
		update_post_meta( $this->post_id, _papi_f( $this->property->slug . '_columns' ), count( $this->property->settings->items ) );

		// Properties
		$properties_html_name         = _papi_ff( _papify( $this->property->slug ) . '_properties' );
		$_POST[$properties_html_name] = htmlentities( json_encode( $properties ) );

		// Rows
		$rows_html_name         = _papi_ff( _papify( $this->property->slug ) . '_rows' );
		$_POST[$rows_html_name] = 1;

		$expected = array( array( 'book_name' => 'Harry Potter', 'is_open' => false ) );
		$actual   = papi_field( $this->post_id, $this->property->slug );

		$this->assertEquals( $expected, $actual );
	}

}
