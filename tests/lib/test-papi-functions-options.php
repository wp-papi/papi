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
		$this->assertTrue( $options['post_type_post_show_standard_page'] );
		$this->assertTrue( $options['post_type_page_show_standard_page'] );
		$this->assertFalse( isset( $options[true] ) );
		$this->assertFalse( isset( $options[false] ) );
		$this->assertFalse( isset( $options[null] ) );
		$this->assertFalse( isset( $options[1] ) );
	}

	/**
	 * Test _papi_get_option.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_option() {
		$this->assertTrue( _papi_get_option( 'post_type_post_show_standard_page' ) );
		$this->assertTrue( _papi_get_option( 'post_type_page_show_standard_page' ) );
		$this->assertEmpty( _papi_get_option( true ) );
		$this->assertEmpty( _papi_get_option( false ) );
		$this->assertEmpty( _papi_get_option( null ) );
		$this->assertEmpty( _papi_get_option( 1 ) );
		$this->assertEmpty( _papi_get_option( array() ) );
		$this->assertEmpty( _papi_get_option( new stdClass() ) );
	}

}
