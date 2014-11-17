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
class WP_Property_Post extends WP_UnitTestCase {

	/**
	 * Test property post options.
	 *
	 * @since 1.0.0
	 */

	public function test_property_post_options () {
		$property = _papi_get_property_options( array(
			'type'  => 'post',
			'title' => 'Post',
			'slug'  => 'post'
		) );

		$this->assertEquals($property->slug, 'papi_post');

		// Test default settings
		$this->assertEquals($property->settings->post_type, 'post');
	}

}
