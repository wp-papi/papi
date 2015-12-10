<?php

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

	public function test_get_property() {
		$this->assertNull( $this->page->get_property( 'fake' ) );

		$property = $this->page->get_property( 'name' );
		$this->assertSame( 'string', $property->get_option( 'type' ) );
		$this->assertSame( 'string', $property->type );
		$this->assertSame( 'papi_name', $property->slug );
		$this->assertSame( 'papi_name', $property->get_option( 'slug' ) );
		$this->assertSame( 'Name', $property->get_option( 'title' ) );
		$this->assertSame( 'Name', $property->title );

		$_GET['page'] = 'papi/option/options/header-option-type';

		$property = $this->page->get_property( 'name' );
		$this->assertSame( 'string', $property->get_option( 'type' ) );
		$this->assertSame( 'string', $property->type );
		$this->assertSame( 'papi_name', $property->slug );
		$this->assertSame( 'papi_name', $property->get_option( 'slug' ) );
		$this->assertSame( 'Name', $property->get_option( 'title' ) );
		$this->assertSame( 'Name', $property->title );

		$_GET['page'] = 'papi/page/modules/top-module-type';
		$this->assertNull( $this->page->get_property( 'name' ) );
	}

	public function test_get_value() {
		$property = $this->page->get_property( 'name' );
		$this->assertEmpty( $property->get_value() );

		update_option( 'name', 'Fredrik' );
		$this->assertSame( 'Fredrik', $this->page->get_value( 'name' ) );

		update_option( 'hello', 'Fredrik' );
		$this->assertNull( $this->page->get_value( 'hello' ) );
	}

	public function test_get_option_type() {
		$page = new Papi_Option_Page();
		$this->assertNull( $page->get_option_type() );

		$page = new Papi_Option_Page();
		$_GET['page'] = 'papi/option/options/properties-option-type';
		$this->assertSame( 'options/properties-option-type', $page->get_option_type()->get_id() );
		unset( $_GET['page'] );

		$page = new Papi_Option_Page();
		$property = $page->get_property( 'name' );
		$this->assertSame( 'options/header-option-type', $page->get_option_type()->get_id() );
	}

	public function test_valid() {
		$this->assertTrue( $this->page->valid() );
	}
}
