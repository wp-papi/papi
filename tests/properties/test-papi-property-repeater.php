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
	}

	/**
	 * Test property options.
	 *
	 * @since 1.0.0
	 */

	public function test_property_options () {
		$property = papi_property( array(
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

		// Test the property
		$this->assertEquals($property->type, 'repeater');
		$this->assertEquals($property->slug, 'papi_books');
		$this->assertFalse( empty( $property->settings->items ) );

		// Test the first item in the repeater
		$this->assertEquals( $property->settings->items[0]->type, 'string');
		$this->assertEquals( $property->settings->items[0]->slug, 'papi_book_name' );
		$this->assertEquals( $property->settings->items[0]->title, 'Book name' );

		// Generate correct property meta key and property type meta key.
		$meta_key      = _papi_remove_papi( $property->settings->items[0]->slug );
		$meta_type_key = _papi_get_property_type_key( $meta_key );

		// Add property value.
		add_post_meta( $this->post_id, _papi_remove_papi( $property->slug ), array( array( $meta_key => 'Harry Potter',  $meta_type_key => $property->settings->items[0]->type ) ) );

		// Add property type value
		$slug_type = _papi_get_property_type_key_f( $property->slug );
		add_post_meta( $this->post_id, $slug_type, $property->type );

		// Test get the value with papi_field function.
		$this->assertEquals( papi_field( $this->post_id, _papi_remove_papi( $property->slug ) ), array( array( $meta_key => 'Harry Potter' ) ) );
	}

}
