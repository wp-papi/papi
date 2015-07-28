<?php

/**
 * Unit tests covering property dropdown.
 *
 * @package Papi
 */

class Papi_Property_Dropdown_Test extends Papi_Property_Test_Case {

	public $slugs = ['dropdown_test', 'dropdown_test_2'];

	public function get_value() {
		return '#ffffff';
	}

	public function get_expected() {
		return '#ffffff';
	}

	public function test_property_options() {
		$this->assertEquals( 'dropdown', $this->properties[0]->get_option( 'type' ) );
		$this->assertEquals( 'Dropdown test', $this->properties[0]->get_option( 'title' ) );
		$this->assertEquals( 'papi_dropdown_test', $this->properties[0]->get_option( 'slug' ) );

		$this->assertEquals( 'dropdown', $this->properties[1]->get_option( 'type' ) );
		$this->assertEquals( 'Dropdown test 2', $this->properties[1]->get_option( 'title' ) );
		$this->assertEquals( 'papi_dropdown_test_2', $this->properties[1]->get_option( 'slug' ) );
	}

	public function test_property_settings() {
		$settings = $this->properties[0]->get_settings();
		$this->assertEmpty( $settings->placeholder );
		$this->assertEquals( '#ffffff', $settings->items['White'] );
		$this->assertEquals( '#000000', $settings->items['Black'] );
		$this->assertTrue( $settings->select2 );

		$settings = $this->properties[1]->get_settings();
		$this->assertEquals( 'Pick one', $settings->placeholder );
		$this->assertEquals( '#ffffff', $settings->items['White'] );
		$this->assertEquals( '#000000', $settings->items['Black'] );
		$this->assertTrue( $settings->select2 );
	}

}
