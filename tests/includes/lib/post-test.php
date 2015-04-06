<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering post functions.
 *
 * @package Papi
 */

class Papi_Lib_Post_Test extends WP_UnitTestCase {

	/**
	 * Test `papi_from_property_array_slugs` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_post_id() {
		global $post;

		$post_id = $this->factory->post->create();

		$post = get_post( $post_id );
		$this->assertEquals( 1, papi_get_post_id( 1 ) );
		$this->assertEquals( $post_id, papi_get_post_id() );
		$this->assertEquals( $post_id, papi_get_post_id( null ) );
	}

	/**
	 * Test `papi_get_wp_post_type` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_wp_post_type() {
		global $post, $_GET, $_POST;

		$post = null;
		$this->assertEmpty( papi_get_wp_post_type() );
		$_GET = array( 'post_type' => 'post' );
		$this->assertEquals( 'post', papi_get_wp_post_type() );

		$_GET = array( 'page' => 'papi-add-new-page,books' );
		$this->assertEquals( 'books', papi_get_wp_post_type() );

		$_POST = array( 'post_type' => 'page' );
		$this->assertEquals( 'page', papi_get_wp_post_type() );

		$_GET  = array();
		$_POST = array();
	}

}
