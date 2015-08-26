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
		return ['#ffffff'];
	}

	public function test_property_convert_type() {
		$this->assertEquals( 'array', $this->property->convert_type );
	}

	public function test_property_default_value() {
		$this->assertEquals( [], $this->property->default_value );
	}

	public function test_property_format_value() {
		$this->assertEquals( [ 'hello' ], $this->property->format_value( 'hello', '', 0 ) );
		$this->assertEquals( [ 'hello' ], $this->property->format_value( [ 'hello' ], '', 0 ) );
		$this->assertEmpty( $this->property->format_value( null, '', 0 ) );
	}

	public function test_property_import_value() {
		$this->assertEquals( ['hello'], $this->property->import_value( 'hello', '', 0 ) );
		$this->assertEquals( ['hello'], $this->property->import_value( ['hello'], '', 0 ) );
		$this->assertNull( $this->property->import_value( null, '', 0 ) );
		$this->assertNull( $this->property->import_value( true, '', 0 ) );
		$this->assertNull( $this->property->import_value( false, '', 0 ) );
		$this->assertNull( $this->property->import_value( (object) [], '', 0 ) );
		$this->assertNull( $this->property->import_value( 1, '', 0 ) );
	}

	public function test_property_options() {
		$this->assertEquals( 'checkbox', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'Checkbox test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_checkbox_test', $this->property->get_option( 'slug' ) );
	}

	public function test_property_settings() {
		$this->assertNotEmpty( $this->property->get_setting( 'items' ) );
	}
}
