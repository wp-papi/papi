<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests to test Papi_Page.
 *
 * @package Papi
 */

class WP_Papi_Page extends WP_UnitTestCase {

	/**
	 * Setup test.
	 *
	 * @since 1.3.0
	 */

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return array( 1, dirname( __FILE__ ) . '/data/page-types' );
		} );

		$this->post_id = $this->factory->post->create();

		$this->page = papi_get_page( $this->post_id );
	}

	/**
	 * Tear down test.
	 *
	 * @since 1.3.0
	 */

	public function tearDown() {
		unset( $this->post_id );
		unset( $this->page );
	}

	/**
	 * Test get_page_type method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_page_type() {
		$this->assertEmpty( $this->page->get_page_type() );

		add_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );

		$page = papi_get_page( $this->post_id );

		$this->assertEquals( $page->get_page_type()->name, 'Simple page' );
	}

	/**
	 * Test get_permalink method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_permalink() {
		$this->assertFalse( empty( $this->page->get_permalink() ) );
	}

	/**
	 * Test get_post method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_post() {
		$this->assertTrue( is_object( $this->page->get_post() ) );
		$this->assertEquals( $this->post_id, $this->page->get_post()->ID );
	}

	/**
	 * Test get_status method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_status() {
		$this->assertEquals( 'publish', $this->page->get_status() );
	}
}
