<?php

/**
 * Unit tests covering property file.
 *
 * @package Papi
 */
class Papi_Property_File_Test extends Papi_Property_Test_Case {

	public $slug = 'file_test';

	public function get_value() {
		return 23;
	}

	public function get_expected() {
		return 23;
	}

	public function test_property_convert_type() {
		$this->assertEquals( 'object', $this->property->convert_type );
	}

	public function test_property_default_value() {
		$this->assertEquals( [], $this->property->default_value );
	}

	public function test_property_format_value() {
		$this->assertEquals( $this->get_expected(), $this->property->format_value( $this->get_value(), '', 0 ) );
	}

	public function test_property_import_value() {
		$this->assertEquals( 0, $this->property->import_value( $this->get_value(), '', 0 ) );
	}

	public function test_property_options() {
		$this->assertEquals( 'file', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'File test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_file_test', $this->property->get_option( 'slug' ) );
	}

	public function test_property_settings() {
		$this->assertFalse( $this->property->get_setting( 'multiple' ) );
	}

}
