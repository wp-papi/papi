<?php

class Papi_Lib_Core_Url_Test extends WP_UnitTestCase {

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

	public function test_papi_append_post_type_query() {
		global $post;

		$url = papi_append_post_type_query( 'http://wordpress/?post_parent=1', 'post' );
		$this->assertSame( 'http://wordpress/?post_parent=1&post_type=post', $url );

		$post = get_post( $this->post_id );
		$url = papi_append_post_type_query( 'http://wordpress/?post_parent=1' );
		$this->assertSame( 'http://wordpress/?post_parent=1&post_type=post', $url );

		$url = papi_append_post_type_query( 'http://wordpress/?post_parent=1&post_type=post' );
		$this->assertSame( 'http://wordpress/?post_parent=1&post_type=post', $url );
	}

	public function test_papi_append_post_type_query_fail() {
		$url = papi_append_post_type_query( 'http://wordpress/?post_parent=1' );
		$this->assertSame( 'http://wordpress/?post_parent=1&post_type=post', $url );
	}

	public function test_papi_include_query_strings() {
		$this->assertEmpty( papi_include_query_strings() );

		$old_get = $_GET;

		$_GET = [
			'foo' => 'bar',
			'baz' => 'boom',
			'cow' => 'milk',
			'php' => 'hypertext processor'
		];

		$this->assertEmpty( papi_include_query_strings( '?' ) );

		$this->assertEmpty( papi_include_query_strings( '?', ['missing'] ) );

		$this->assertSame( '?foo=bar', papi_include_query_strings( '?', ['foo'] ) );

		$this->assertSame( '&baz=boom', papi_include_query_strings( '&', ['baz'] ) );

		$this->assertSame( '?foo=bar&baz=boom', papi_include_query_strings( '?', ['foo', 'baz'] ) );

		$this->assertSame( '?php=hypertext+processor', papi_include_query_strings( '?', ['php'] ) );

		$_GET = $old_get;
	}
}
