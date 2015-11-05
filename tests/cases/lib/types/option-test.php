<?php

class Papi_Lib_Types_Option_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		global $current_screen;

		$current_screen = WP_Screen::get( 'admin_init' );

		$_SERVER['REQUEST_URI'] = 'http://site.com/?page=papi/options/header-option-type';

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );
	}

	public function tearDown() {
		parent::tearDown();
		$_SERVER['REQUEST_URI'] = '';

		global $current_screen;
		$current_screen = null;
	}

	public function test_papi_is_option_type() {
		$this->assertTrue( papi_is_option_type( new Papi_Option_Type ) );
		$this->assertFalse( papi_is_option_type( new Papi_Page_Type ) );
		$this->assertFalse( papi_is_option_type( true ) );
		$this->assertFalse( papi_is_option_type( false ) );
		$this->assertFalse( papi_is_option_type( null ) );
		$this->assertFalse( papi_is_option_type( 1 ) );
		$this->assertFalse( papi_is_option_type( 0 ) );
		$this->assertFalse( papi_is_option_type( '' ) );
		$this->assertFalse( papi_is_option_type( [] ) );
		$this->assertFalse( papi_is_option_type( (object) [] ) );
	}

	public function test_papi_option_type_exists() {
		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->assertFalse( papi_option_type_exists( 'hello' ) );
		$this->assertFalse( papi_option_type_exists( 'empty-page-type' ) );
		$this->assertTrue( papi_option_type_exists( 'options/header-option-type' ) );
	}
}
