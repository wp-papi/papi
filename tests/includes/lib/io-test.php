<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering io functions.
 *
 * @package Papi
 */

class Papi_Lib_IO_Test extends WP_UnitTestCase {

	/**
	 * Test `papi_get_all_files_in_directory` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_all_files_in_directory() {
		$this->assertEmpty( papi_get_all_files_in_directory() );
		$actual = papi_get_all_files_in_directory();

		$this->assertTrue( is_array( $actual ) );
		$this->assertTrue( empty( $actual ) );

		$actual = papi_get_all_files_in_directory( papi_test_get_fixtures_path( '/page-types' ) );
		$expected = papi_test_get_fixtures_path( '/page-types/simple-page-type.php' );

		$this->assertTrue( is_array( $actual ) );
		$this->assertTrue( ! empty( $actual ) );
		$this->assertTrue( in_array( $expected, $actual ) );
	}

	/**
	 * Test `papi_get_all_page_type_files` function.
	 *
	 * @since 1.3.0
	 */

	public function test_papi_get_all_page_type_files() {
		$this->assertEmpty( papi_get_all_page_type_files() );

		tests_add_filter( 'papi/settings/directories', function () {
			return [ 1,  papi_test_get_fixtures_path( '/page-types' ) ];
		} );

		$actual = papi_get_all_page_type_files();
		$this->assertFalse( empty( $actual ) );
		$this->assertTrue( is_array( $actual ) );
	}

	/**
	 * Test `papi_get_file_path` function.
	 *
	 * @since 1.3.0
	 */

	public function test_papi_get_file_path() {
		$this->assertNull( papi_get_file_path( 'simple-page-type' ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [ 1,  papi_test_get_fixtures_path( '/page-types' ) ];
		} );

		$path1 = papi_get_file_path( 'simple-page-type' );
		$path2 = papi_get_file_path( 'simple-page-type.php' );

		$this->assertTrue( strpos( $path1, 'simple-page-type.php' ) !== false );
		$this->assertTrue( strpos( $path2, 'simple-page-type.php' ) !== false );
	}

	/**
	 * Test `papi_get_page_type_base_path` function.
	 *
	 * @since 1.3.0
	 */

	public function test_papi_get_page_type_base_path() {
		$this->assertEquals( 'simple-page-type', papi_get_page_type_base_path( 'simple-page-type' ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [ 1,  papi_test_get_fixtures_path( '/page-types' ) ];
		} );

		$path = papi_get_file_path( 'simple-page-type' );
		$this->assertEquals( 'simple-page-type', papi_get_page_type_base_path( $path ) );

		$this->assertNull( papi_get_page_type_base_path( '' ) );
		$this->assertNull( papi_get_page_type_base_path( null ) );
	}

}
