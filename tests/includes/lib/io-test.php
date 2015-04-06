<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering io functionality.
 *
 * Not all page functions is tested here, some are tested in tests/test-papi-page-type.php.
 *
 * @package Papi
 */

class Papi_Lib_IO_Test extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 *
	 * @since 1.0.0
	 */

	public function setUp() {
		parent::setUp();
	}

	/**
	 * Test papi_get_options.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_options() {
		$actual = papi_get_all_files_in_directory();

		$this->assertTrue( is_array( $actual ) );
		$this->assertTrue( empty( $actual ) );

		$actual = papi_get_all_files_in_directory( papi_test_get_fixtures_path( '/page-types' ) );
		$expected = papi_test_get_fixtures_path( '/page-types/simple-page-type.php' );

		$this->assertTrue( is_array( $actual ) );
		$this->assertTrue( ! empty( $actual ) );
		$this->assertTrue( in_array( $expected, $actual ) );
	}

}
