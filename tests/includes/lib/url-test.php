<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering url functions.
 *
 * @package Papi
 */
class Papi_Lib_Url_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->post_id = $this->factory->post->create();
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id );
	}

	public function test_papi_get_page_new_url() {
		$_SERVER['REQUEST_URI'] = '';
		$url = papi_get_page_new_url( 'page', true, 'page' );
		$this->assertNotFalse( strpos( $url, 'page_type=page&post_type=page' ) );

		$url = papi_get_page_new_url( 'page', true, 'page', ['post_type'] );
		$this->assertNotFalse( strpos( $url, 'page_type=page&post_type=page' ) );
	}

	public function test_papi_get_page_query_strings() {
		$this->assertEmpty( papi_get_page_query_strings() );

		$old_request_uri = $_SERVER['REQUEST_URI'];

		$_SERVER['REQUEST_URI'] = '/?page_id=63';
		$this->assertEquals( '&page_id=63', papi_get_page_query_strings() );

		$_SERVER['REQUEST_URI'] = 'http://wordpress/wp-admin/edit.php?post_type=page&page=papi-add-new-page,page';
		$this->assertEquals( '&post_type=page', papi_get_page_query_strings() );

		$_SERVER['REQUEST_URI'] = 'http://wordpress/wp-admin/edit.php?post_type=page&page';
		$this->assertEquals( '&post_type=page&page', papi_get_page_query_strings() );

		$_SERVER['REQUEST_URI'] = 'http://wordpress/wp-admin/edit.php?post_type=page&page_type=simple-page-type&&';
		$this->assertEquals( '?page_type=simple-page-type', papi_get_page_query_strings( '?', ['post_type'] ) );

		$_SERVER['REQUEST_URI'] = 'http://wordpress/wp-admin/edit.php?&';
		$this->assertEmpty( papi_get_page_query_strings() );

		$_SERVER['REQUEST_URI'] = $old_request_uri;
	}

	public function test_papi_append_post_type_query() {
		global $post;

		$url = papi_append_post_type_query( 'http://wordpress/?post_parent=1', 'post' );
		$this->assertEquals( 'http://wordpress/?post_parent=1&post_type=post', $url  );

		$post = get_post( $this->post_id );
		$url = papi_append_post_type_query( 'http://wordpress/?post_parent=1' );
		$this->assertEquals( 'http://wordpress/?post_parent=1&post_type=post', $url  );
	}

}
