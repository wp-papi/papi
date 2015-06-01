<?php

/**
 * Unit tests covering property checkbox.
 *
 * @package Papi
 */

class Papi_Property_Checkbox_Test extends Papi_Property_Test_Case {

	public $slug = 'checkbox_test';

	public function get_value() {
		return '#ffffff';
	}

	public function get_expected() {
		return [ '#ffffff' ];
	}

	public function test_format_value() {
		$this->assertEquals( [ 'hello' ], $this->property->format_value( 'hello', '', 0 ) );
		$this->assertEquals( [ 'hello' ], $this->property->format_value( [ 'hello' ], '', 0 ) );
		$this->assertEmpty( $this->property->format_value( null, '', 0 ) );
	}

	public function test_property_options() {
		$this->assertEquals( 'checkbox', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'Checkbox test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_checkbox_test', $this->property->get_option( 'slug' ) );
	}

	public function test_property_settings() {
		$this->assertNotEmpty( $this->property->get_setting('items') );
	}

}
