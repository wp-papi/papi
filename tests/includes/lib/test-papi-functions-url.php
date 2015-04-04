<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering url functionality.
 *
 * @package Papi
 */

class WP_Papi_Functions_Url extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 *
	 * @since 1.0.0
	 */

	public function setUp() {
		parent::setUp();
	}

	/**
	 * Test papi_get_new_url.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_page_new_url() {
		$actual = papi_get_page_new_url( 'page', true, 'page' );
		$this->assertTrue( strpos( $actual, 'page_type=page&post_type=page' ) !== false );
	}

	/**
	 * Test papi_append_post_type_query.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_append_post_type_query() {
		$this->assertEquals( 'http://dev.papi.com/?post_parent=1&post_type=post', papi_append_post_type_query( 'http://dev.papi.com/?post_parent=1', 'post' ) );
	}
}
