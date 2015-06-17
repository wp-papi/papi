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
		$_SERVER['REQUEST_URI'] = 'http://site.com/?page=papi/options/header-option-type';

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );
	}

	public function tearDown() {
		parent::tearDown();
		$_SERVER['REQUEST_URI'] = '';
	}

	public function test_papi_delete_option() {
		$this->assertFalse( papi_delete_option( 'fake_slug' ) );
		update_option( 'name', 'Kalle' );
		$this->assertEquals( 'Kalle', papi_option( 'name' ) );
		$this->assertTrue( papi_delete_option( 'name' ) );
		$this->assertNull( papi_option( 'name' ) );
	}

	public function test_papi_option() {
		$this->assertNull( papi_option( 'site' ) );

		update_option( 'name', 'fredrik' );

		$this->assertEquals( 'fredrik', papi_option( 'name' ) );
	}

	public function test_papi_option_shortcode() {
		update_option( 'name', 'fredrik' );

		$this->assertEmpty( papi_option_shortcode( [] ) );
		$this->assertEquals( 'fredrik', papi_option_shortcode( [
			'slug' => 'name'
		] ) );

		$this->assertEquals( '1, 2, 3', papi_option_shortcode( [
			'slug'    => 'numbers',
			'default' => [1, 2, 3]
		] ) );
	}

	public function test_papi_update_option() {
		$this->assertFalse( papi_update_option( 0, 'fake_slug' ) );
		$this->assertFalse( papi_update_option( 'fake_slug' ) );
		$this->assertFalse( papi_update_option( 93099, 'fake_slug' ) );
		$this->assertFalse( papi_update_option( 'fake_slug' ) );
		$this->assertTrue( papi_update_option( 'name', 'Kalle' ) );
		$this->assertEquals( 'Kalle', papi_option( 'name' ) );
	}

	public function test_the_papi_option() {
		update_option( 'name', 'fredrik' );

		the_papi_option( 'name' );
		$this->expectOutputRegex( '/fredrik/' );

		the_papi_option( 'numbers', [1, 2, 3] );
		$this->expectOutputRegex( '/1\, 2\, 3/' );
	}

}
