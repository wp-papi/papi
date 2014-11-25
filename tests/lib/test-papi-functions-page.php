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
		$this->assertEquals( 0, _papi_get_number_of_pages( null ) );
		$this->assertEquals( 0, _papi_get_number_of_pages( true ) );
		$this->assertEquals( 0, _papi_get_number_of_pages( false ) );
		$this->assertEquals( 0, _papi_get_number_of_pages( array() ) );
		$this->assertEquals( 0, _papi_get_number_of_pages( new stdClass() ) );
		$this->assertEquals( 0, _papi_get_number_of_pages( 1 ) );
	}

	/**
	 * Test _papi_get_post_type.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_post_types() {
		$actual = _papi_get_post_types();

		// Remove all post types can be in your own WordPress site.
		foreach ( $actual as $key => $value ) {
			if ( $value !== 'page' ) {
				unset( $actual[$key] );
			}
		}

		$this->assertEquals( array( 'page' ), array_values( $actual ) );
	}

	/**
	 * Test _papi_is_page_type_allowed.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_is_page_type_allowed() {
		$this->assertTrue( _papi_is_page_type_allowed( 'page' ) );
		$this->assertFalse( _papi_is_page_type_allowed( true ) );
		$this->assertFalse( _papi_is_page_type_allowed( false ) );
		$this->assertFalse( _papi_is_page_type_allowed( null ) );
		$this->assertFalse( _papi_is_page_type_allowed( 1 ) );
		$this->assertFalse( _papi_is_page_type_allowed( array() ) );
		$this->assertFalse( _papi_is_page_type_allowed( new stdClass() ) );
	}

}
