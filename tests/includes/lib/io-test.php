<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering io functions.
 *
 * @package Papi
 */

class Papi_Lib_IO_Test extends WP_UnitTestCase {

	public function test_papi_get_all_files_in_directory() {
		$this->assertEmpty( papi_get_all_files_in_directory( 1 ) );
		$this->assertEmpty( papi_get_all_files_in_directory( true ) );
		$this->assertEmpty( papi_get_all_files_in_directory( false ) );
		$this->assertEmpty( papi_get_all_files_in_directory( [] ) );
		$this->assertEmpty( papi_get_all_files_in_directory( (object) [] ) );
		$this->assertEmpty( papi_get_all_files_in_directory( '' ) );
		$this->assertEmpty( papi_get_all_files_in_directory() );
		$this->assertTrue( is_array( papi_get_all_files_in_directory() ) );

		$actual = papi_get_all_files_in_directory( PAPI_FIXTURE_DIR . '/page-types' );
		$expected = PAPI_FIXTURE_DIR . '/page-types/simple-page-type.php';

		$this->assertTrue( is_array( $actual ) );
		$this->assertTrue( ! empty( $actual ) );
		$this->assertTrue( in_array( $expected, $actual ) );
	}

	public function test_papi_get_all_page_type_files() {
		$this->assertEmpty( papi_get_all_page_type_files() );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$actual = papi_get_all_page_type_files();
		$this->assertFalse( empty( $actual ) );
		$this->assertTrue( is_array( $actual ) );
	}

	public function test_papi_get_file_path() {
		$this->assertNull( papi_get_file_path( 1 ) );
		$this->assertNull( papi_get_file_path( null ) );
		$this->assertNull( papi_get_file_path( true ) );
		$this->assertNull( papi_get_file_path( false ) );
		$this->assertNull( papi_get_file_path( [] ) );
		$this->assertNull( papi_get_file_path( (object) [] ) );
		$this->assertNull( papi_get_file_path( '' ) );
		$this->assertNull( papi_get_file_path( 'simple-page-type' ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$path1 = papi_get_file_path( 'simple-page-type' );
		$path2 = papi_get_file_path( 'simple-page-type.php' );

		$this->assertTrue( strpos( $path1, 'simple-page-type.php' ) !== false );
		$this->assertTrue( strpos( $path2, 'simple-page-type.php' ) !== false );
	}

	public function test_papi_get_page_type_base_path() {
		$this->assertNull( papi_get_page_type_base_path( 1 ) );
		$this->assertNull( papi_get_page_type_base_path( true ) );
		$this->assertNull( papi_get_page_type_base_path( false ) );
		$this->assertNull( papi_get_page_type_base_path( [] ) );
		$this->assertNull( papi_get_page_type_base_path( (object) [] ) );
		$this->assertNull( papi_get_page_type_base_path( '' ) );
		$this->assertEquals( 'simple-page-type', papi_get_page_type_base_path( 'simple-page-type' ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$path = papi_get_file_path( 'simple-page-type' );
		$this->assertEquals( 'simple-page-type', papi_get_page_type_base_path( $path ) );

		$this->assertNull( papi_get_page_type_base_path( '' ) );
		$this->assertNull( papi_get_page_type_base_path( null ) );
	}

}
