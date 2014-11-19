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

		$this->post_id = $this->factory->post->create();

		$this->property = papi_property( array(
			'type'     => 'repeater',
			'title'    => 'Books',
			'slug'     => 'books',
			'settings' => array(
				'items' => array(
					papi_property(array(
						'type'  => 'string',
						'title' => 'Book name',
						'slug'  => 'book_name'
					))
				)
			)
		) );
	}

	/**
	 * Test property options.
	 *
	 * @since 1.0.0
	 */

	public function test_property_options () {


		// Test the property
		$this->assertEquals($this->property->type, 'repeater');
		$this->assertEquals($this->property->slug, 'papi_books');
		$this->assertFalse( empty( $this->property->settings->items ) );

		// Test the first item in the repeater
		$this->assertEquals( $this->property->settings->items[0]->type, 'string');
		$this->assertEquals( $this->property->settings->items[0]->slug, 'papi_book_name' );
		$this->assertEquals( $this->property->settings->items[0]->title, 'Book name' );
	}

	/**
	 * Test save property value.
	 *
	 * @since 1.0.0
	 */

	public function test_save_property_value () {
		// Generate correct property meta key and property type meta key for string property.
		$value_slug      = _papi_remove_papi( $this->property->settings->items[0]->slug );
		$value_type_slug = _papi_get_property_type_key( $value_slug );

		// Save the property
		_papi_property_update_value( array(
			'post_id' => $this->post_id,
			'slug'    => $this->property->slug,
			'type'    => $this->property->type,
			'value'   => array(
				array(
					$value_slug      => 'Harry Potter',
					$value_type_slug => $this->property->settings->items[0]->type
				)
			)
		) );

		// Test get the value with papi_field function.
		$this->assertEquals( papi_field( $this->post_id, $this->property->slug ), array( array( $value_slug => 'Harry Potter' ) ) );
	}

}
