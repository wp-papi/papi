<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Unit tests covering page functionality.
 *
 * Not all page functions is tested here, some are tested in tests/test-papi-page-type.php.
 *
 * @package Papi
 */

class WP_Papi_Functions_Page extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 *
	 * @since 1.0.0
	 */

	public function setUp() {
		parent::setUp();
	}

	/**
	 * Test _papi_get_file_data.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_number_of_pages() {
		$this->assertEquals( 0, _papi_get_number_of_pages( 'simple-page-type' ) );
	}

	/**
	 * Test _papi_get_post_type.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_post_types() {
		$this->assertEquals( array( 'page' ), _papi_get_post_types() );
	}

	/**
	 * Test _papi_is_page_type_allowed.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_is_page_type_allowed() {
		$this->assertTrue( _papi_is_page_type_allowed( 'page' ) );
	}

}
