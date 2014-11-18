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

class WP_Papi_Property_Post extends WP_UnitTestCase {

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
			'type'  => 'post',
			'title' => 'The big post',
			'slug'  => 'the_big_post'
		) );

		// Test the property
		$this->assertEquals($property->type, 'post');
		$this->assertEquals($property->title, 'The big post');
		$this->assertEquals($property->slug, 'papi_the_big_post');

		// Test default settings
		$this->assertEquals($property->settings->post_type, 'post');

		// Add property value
		add_post_meta( $this->post_id, _papi_remove_papi( $property->slug ), $this->post_id );

		// Add property type value
		$slug_type = _papi_get_property_type_key_f( $property->slug );
		add_post_meta( $this->post_id, $slug_type, $property->type );

		// Test get the value with papi_field function.
		$this->assertEquals( papi_field( $this->post_id, _papi_remove_papi( $property->slug ) ), get_post( $this->post_id ) );
	}

}
