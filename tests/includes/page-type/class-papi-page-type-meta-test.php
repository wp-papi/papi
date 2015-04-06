<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering page type meta functionality.
 *
 * @package Papi
 */

class Papi_Page_Type_Meta_Test extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 *
	 * @since 1.3.0
	 */

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return array( 1,  papi_test_get_fixtures_path( '/page-types' ) );
		} );

		$this->post_id = $this->factory->post->create();

		$this->faq_page_type    = papi_get_page_type_by_id( 'faq-page-type' );
		$this->simple_page_type = papi_get_page_type_by_id( 'simple-page-type' );
	}

	/**
	 * Test current_user_is_allowed method.
	 *
	 * @since 1.3.0
	 */

	public function test_current_user_is_allowed() {
		$this->assertTrue( $this->simple_page_type->current_user_is_allowed() );
		$this->assertFalse( $this->faq_page_type->current_user_is_allowed() );
	}

	/**
	 * Test get_thumbnail method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_thumbnail() {
		$this->assertEmpty( $this->simple_page_type->get_thumbnail() );
		$this->assertEquals( 'faq.png', $this->faq_page_type->get_thumbnail() );
	}

	/**
	 * Test has_post_type method.
	 *
	 * @since 1.3.0
	 */

	public function test_has_post_type() {
		$this->assertTrue( $this->simple_page_type->has_post_type( 'page' ) );
		$this->assertTrue( $this->faq_page_type->has_post_type( 'faq' ) );
	}

}
