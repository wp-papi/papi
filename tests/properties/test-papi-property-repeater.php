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

class WP_Papi_Property_Repeater extends WP_UnitTestCase {

	/**
	 * Setup the test and register the page types directory.
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
	}

	/**
	 * Test save property value.
	 *
	 * @since 1.0.0
	 */

	public function test_save_property_value() {
		$handler = new Papi_Admin_Meta_Boxes();

		// Generate correct property meta key and property type meta key for string property.
		$value_slug         = _papi_remove_papi( $this->property->settings->items[0]->slug );
		$value_type_slug    = _papi_get_property_type_key( $value_slug );
		$property_type_slug = _papi_html_name( _papi_get_property_type_key( $this->property->slug ) );

		// Create the repeater item
		$item = array();
		$item[$value_slug] = 'Harry Potter';
		$item[$value_type_slug] = $this->property->settings->items[0]->type;

		// Create post data.
		$_POST = _papi_test_create_property_post_data(array(
			'slug'  => $this->property->slug,
			'type'  => $this->property->type,
			'value' => array( $item )
		), $_POST);

		$handler->save_property( $this->post_id );

		$item = new stdClass;
		$item->book_name = 'Harry Potter';
		$expected = array( $item );

		$actual = papi_field( $this->post_id, $this->property->slug );
		$this->assertEquals( $expected, $actual );
	}

}
