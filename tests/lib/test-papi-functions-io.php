<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Unit tests covering io functionality.
 *
 * Not all page functions is tested here, some are tested in tests/test-papi-page-type.php.
 *
 * @package Papi
 */

class WP_Papi_Functions_IO extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 *
	 * @since 1.0.0
	 */

	public function setUp() {
		parent::setUp();
	}

	/**
	 * Test _papi_get_options.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_options() {
		$actual = _papi_get_all_files_in_directory();

		$this->assertTrue( is_array( $actual ) );
		$this->assertTrue( empty( $actual ) );

		$actual = _papi_get_all_files_in_directory( dirname( __FILE__ ) . '/../data/page-types' );
		$expected = dirname( __FILE__ ) . '/../data/page-types/simple-page-type.php';

		$this->assertTrue( is_array( $actual ) );
		$this->assertTrue( ! empty($actual ) );
		$this->assertEquals( $expected, $actual[0] );
	}

}
