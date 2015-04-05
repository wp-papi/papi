<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests to test Papi_Page.
 *
 * @package Papi
 */

class WP_Test_Papi_Page extends WP_UnitTestCase {

	/**
	 * Setup test.
	 *
	 * @since 1.3.0
	 */

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return array( 1, papi_test_get_files_path( '/page-types' ) );
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

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );

		$page = papi_get_page( $this->post_id );

		$this->assertEquals( $page->get_page_type()->name, 'Simple page' );
	}

	/**
	 * Test get_permalink method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_permalink() {
		$permalink = $this->page->get_permalink();
		$this->assertFalse( empty( $permalink ) );
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

	/**
	 * Test get_value method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_value() {
		update_post_meta( $this->post_id, 'name', 'Fredrik' );
		update_post_meta( $this->post_id, papi_f( papi_get_property_type_key( 'name' ) ), 'name');

		$this->assertEquals( 'Fredrik', $this->page->get_value( 'name' ) );
	}

	/**
	 * Test __get method.
	 *
	 * @since 1.3.0
	 */

	public function test__get() {
		update_post_meta( $this->post_id, 'name', '' );

		$this->assertNull( $this->page->name );
	}
}
