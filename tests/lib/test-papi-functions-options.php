<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Unit tests covering options functionality.
 *
 * @package Papi
 */

class WP_Papi_Functions_Options extends WP_UnitTestCase {

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
		$options = _papi_get_options();
		$this->assertTrue( is_array( $options ) );
		$this->assertEquals( $options['page_types.sort_by'], 'name' );
		$this->assertTrue( $options['post_type.post.show_standard_page'] );
		$this->assertTrue( $options['post_type.page.show_standard_page'] );
	}

	/**
	 * Test _papi_get_option.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_option() {
		$this->assertEquals( _papi_get_option( 'page_types.sort_by' ), 'name' );
		$this->assertTrue( _papi_get_option( 'post_type.post.show_standard_page' ) );
		$this->assertTrue( _papi_get_option( 'post_type.page.show_standard_page' ) );
	}

}
