<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering options functions.
 *
 * @package Papi
 */

class Papi_Lib_Option_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );
	}

	public function test_papi_option() {
		$this->assertNull( papi_option( 'site' ) );

		update_option( 'name', 'fredrik' );

		$this->assertEquals( 'fredrik', papi_option( 'name' ) );
	}

	public function test_papi_field_shortcode() {
		update_option( 'name', 'fredrik' );

		$this->assertEmpty( papi_option_shortcode( [] ) );
		$this->assertEquals( 'fredrik', papi_option_shortcode( [
			'name' => 'name'
		] ) );

		$this->assertEquals( '1, 2, 3', papi_option_shortcode( [
			'name'    => 'numbers',
			'default' => [1, 2, 3]
		] ) );
	}

	public function test_the_papi_field() {
		update_option( 'name', 'fredrik' );

		the_papi_option( 'name' );
		$this->expectOutputRegex( '/fredrik/' );

		the_papi_option( 'numbers', [1, 2, 3] );
		$this->expectOutputRegex( '/1\, 2\, 3/' );
	}

}
