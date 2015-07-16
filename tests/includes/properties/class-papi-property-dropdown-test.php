<?php

/**
 * Unit tests covering property dropdown.
 *
 * @package Papi
 */

class Papi_Property_Dropdown_Test extends Papi_Property_Test_Case {

	public $slug = 'dropdown_test';

	public function setUp() {
		parent::setUp();
		$this->property2 = $this->page_type->get_property( 'dropdown_test_2' );
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->property2 );
	}

	public function get_value() {
		return '#ffffff';
	}

	public function get_expected() {
		return '#ffffff';
	}

	public function test_property_options() {
		$this->assertEquals( 'dropdown', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'Dropdown test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_dropdown_test', $this->property->get_option( 'slug' ) );

		$this->assertEquals( 'dropdown', $this->property2->get_option( 'type' ) );
		$this->assertEquals( 'Dropdown test 2', $this->property2->get_option( 'title' ) );
		$this->assertEquals( 'papi_dropdown_test_2', $this->property2->get_option( 'slug' ) );
	}

	public function test_property_settings() {
		$settings = $this->property->get_settings();
		$this->assertEmpty( $settings->placeholder );
		$this->assertEquals( '#ffffff', $settings->items['White'] );
		$this->assertEquals( '#000000', $settings->items['Black'] );
		$this->assertTrue( $settings->select2 );

		$settings = $this->property2->get_settings();
		$this->assertEquals( 'Pick one', $settings->placeholder );
		$this->assertEquals( '#ffffff', $settings->items['White'] );
		$this->assertEquals( '#000000', $settings->items['Black'] );
		$this->assertTrue( $settings->select2 );
	}

}
