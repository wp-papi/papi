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
	 */
	public function test_papi_get_post_id() {
		global $post;

		$post_id = $this->factory->post->create();

		$post = get_post( $post_id );
		$this->assertEquals( 1, papi_get_post_id( 1 ) );
		$this->assertEquals( $post_id, papi_get_post_id() );
		$this->assertEquals( $post_id, papi_get_post_id( null ) );

		$this->assertEquals( $post_id, papi_get_post_id( $post ) );
		$this->assertEquals( 1, papi_get_post_id( '1' ) );

		$post = null;

		$_GET = ['post' => $post_id];
		$this->assertEquals( $post_id, papi_get_post_id() );
		unset( $_GET );

		$_GET = ['post' => [1, 2, 3]];
		$this->assertEquals( 0, papi_get_post_id() );
		unset( $_GET );

		$_GET = ['page_id' => $post_id];
		$this->assertEquals( $post_id, papi_get_post_id() );
		unset( $_GET );
	}

	/**
	 * Test `papi_get_post_type` function.
	 */
	public function test_papi_get_post_type() {
		global $post;

		$this->assertEmpty( papi_get_post_type() );

		$_GET = ['post_type' => 'post'];
		$this->assertEquals( 'post', papi_get_post_type() );

		$_GET = ['page' => 'papi-add-new-page,books'];
		$this->assertEquals( 'books', papi_get_post_type() );

		$_GET = ['page' => 'papi-add-new-page,dash-post'];
		$this->assertEquals( 'dash-post', papi_get_post_type() );

		$_GET = ['page' => 'papi-add-new-page,und_post'];
		$this->assertEquals( 'und_post', papi_get_post_type() );

		$_GET = ['page' => 'papi-add-new-page,3414'];
		$this->assertEquals( '3414', papi_get_post_type() );

		$_GET = ['page' => 'papi-add-new-page,dash13'];
		$this->assertEquals( 'dash13', papi_get_post_type() );

		$_GET = ['page' => ''];
		$this->assertEmpty( papi_get_post_type() );

		$_GET = ['page' => 'papi-add-new-page,'];
		$this->assertEmpty( papi_get_post_type() );
		unset( $_GET );

		$_POST = ['post_type' => 'page'];
		$this->assertEquals( 'page', papi_get_post_type() );
		unset( $_POST );

		$_SERVER['REQUEST_URI'] = 'wordpress/wp-admin/post-new.php';
		$this->assertEquals( 'post', papi_get_post_type() );
		$_SERVER['REQUEST_URI'] = '';

		$post_id = $this->factory->post->create();
		$post = get_post( $post_id );
		$this->assertEquals( 'post', papi_get_post_type() );
	}

}
