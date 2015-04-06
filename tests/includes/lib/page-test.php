<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering page functions.
 *
 * @package Papi
 */

class Papi_Lib_Page_Test extends WP_UnitTestCase {

	/**
	 * Test `current_page` function.
	 *
	 * @since 1.0.0
	 */

	public function test_current_page() {
		$this->assertNull( current_page() );
	}

	/**
	 * Test `papi_get_file_data` functions.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_number_of_pages() {
		$this->assertEquals( 0, papi_get_number_of_pages( 'simple-page-type' ) );
		$this->assertEquals( 0, papi_get_number_of_pages( null ) );
		$this->assertEquals( 0, papi_get_number_of_pages( true ) );
		$this->assertEquals( 0, papi_get_number_of_pages( false ) );
		$this->assertEquals( 0, papi_get_number_of_pages( array() ) );
		$this->assertEquals( 0, papi_get_number_of_pages( new stdClass() ) );
		$this->assertEquals( 0, papi_get_number_of_pages( 1 ) );
	}

	/**
	 * Test `papi_get_post_type` functions.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_post_types() {
		$actual = papi_get_post_types();

		foreach ( $actual as $key => $value ) {
			if ( $value !== 'page' ) {
				unset( $actual[$key] );
			}
		}

		$this->assertEquals( array( 'page' ), array_values( $actual ) );
	}
}
