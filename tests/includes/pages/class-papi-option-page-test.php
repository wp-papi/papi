<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Post_Page` class.
 *
 * @package Papi
 */

class Papi_Option_Page_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->page = papi_get_page( 0, 'option' );

		$_GET = [];
	}

	public function tearDown() {
		parent::tearDown();
		unset( $_GET, $this->page );
	}

	public function test_is() {
		$this->assertTrue( $this->page->is( 'option' ) );
	}

	public function test_get_property() {
		$this->assertNull( $this->page->get_property( 'fake' ) );

		$property = $this->page->get_property( 'name' );
		$this->assertEquals( 'string', $property->get_option( 'type' ) );
		$this->assertEquals( 'string', $property->type );
		$this->assertEquals( 'papi_name', $property->slug );
		$this->assertEquals( 'papi_name', $property->get_option( 'slug' ) );
		$this->assertEquals( 'Name', $property->get_option( 'title' ) );
		$this->assertEquals( 'Name', $property->title );

		$_GET['page'] = 'papi/options/header-option-type';

		$property = $this->page->get_property( 'name' );
		$this->assertEquals( 'string', $property->get_option( 'type' ) );
		$this->assertEquals( 'string', $property->type );
		$this->assertEquals( 'papi_name', $property->slug );
		$this->assertEquals( 'papi_name', $property->get_option( 'slug' ) );
		$this->assertEquals( 'Name', $property->get_option( 'title' ) );
		$this->assertEquals( 'Name', $property->title );

		$this->assertempty( $property->get_value() );
	}

	public function test_valid() {
		$this->assertTrue( $this->page->valid() );
	}

}
