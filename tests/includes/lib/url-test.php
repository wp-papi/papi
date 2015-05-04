<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering url functions.
 *
 * @package Papi
 */

class Papi_Lib_Url_Test extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 *
	 * @since 1.0.0
	 */

	public function setUp() {
		parent::setUp();
		$this->post_id = $this->factory->post->create();
	}

	/**
	 * Tear down test.
	 *
	 * @since 1.3.0
	 */

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id );
	}

	/**
	 * Test `papi_get_new_url` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_page_new_url() {
		$url = papi_get_page_new_url( 'page', true, 'page' );
		$this->assertNotFalse( strpos( $url, 'page_type=page&post_type=page' ) );

		$url = papi_get_page_new_url( 'page', true, 'page', array( 'post_type' ) );
		$this->assertNotFalse( strpos( $url, 'page_type=page&post_type=page' ) );
	}

	/**
	 * Test `papi_get_page_query_strings` function.
	 *
	 * @since 1.3.0
	 */

	public function test_papi_get_page_query_strings() {
		$qs = papi_get_page_query_strings();
		$this->assertEmpty( $qs );

		$_SERVER['REQUEST_URI'] = '/?page_id=63';
		$qs = papi_get_page_query_strings();
		$this->assertEquals( '&page_id=63', $qs );

		$_SERVER['REQUEST_URI'] = 'http://wordpress/wp-admin/edit.php?post_type=page&page=papi-add-new-page,page';
		$qs = papi_get_page_query_strings();
		$this->assertEquals( '&post_type=page', $qs );

		$_SERVER['REQUEST_URI'] = 'http://wordpress/wp-admin/edit.php?post_type=page&page';
		$qs = papi_get_page_query_strings();
		$this->assertEquals( '&post_type=page&page', $qs );

		$_SERVER['REQUEST_URI'] = 'http://wordpress/wp-admin/edit.php?post_type=page&page_type=simple-page-type&&';
		$qs = papi_get_page_query_strings( '?', array( 'post_type' ) );
		$this->assertEquals( '?page_type=simple-page-type', $qs );
	}

	/**
	 * Test `papi_append_post_type_query` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_append_post_type_query() {
		global $post;

		$url = papi_append_post_type_query( 'http://wordpress/?post_parent=1', 'post' );
		$this->assertEquals( 'http://wordpress/?post_parent=1&post_type=post', $url  );

		$post = get_post( $this->post_id );
		$url = papi_append_post_type_query( 'http://wordpress/?post_parent=1' );
		$this->assertEquals( 'http://wordpress/?post_parent=1&post_type=post', $url  );
	}

}
