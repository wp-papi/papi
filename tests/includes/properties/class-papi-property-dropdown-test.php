<?php

/**
 * Unit tests covering property dropdown.
 *
 * @package Papi
 */

class Papi_Property_Dropdown_Test extends Papi_Property_Test_Case {

	public $slug = 'dropdown_test';

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
	}

}
