<?php

/**
 * Unit tests covering property number.
 *
 * @package Papi
 */
class Papi_Property_Number_Test extends Papi_Property_Test_Case {

	public $slug = 'number_test';

	public function get_value() {
		return 42;
	}

	public function get_expected() {
		return 42;
	}

	public function test_property_get_default_settings() {
		$settings = (object) $this->property->get_default_settings();
		$this->assertEquals( '', $settings->max );
		$this->assertEquals( '', $settings->min );
		$this->assertEquals( '', $settings->step );
		$this->assertEquals( 'number', $settings->type );
	}

	public function test_property_convert_type() {
		$this->assertEquals( 'int', $this->property->convert_type );
	}

	public function test_property_format_value() {
		$this->assertEquals( 42, $this->property->format_value( '42', '', 0 ) );
		$this->assertEquals( 0, $this->property->format_value( 'hello', '', 0 ) );
		$this->assertEquals( 0, $this->property->format_value( null, '', 0 ) );
	}

	public function test_property_options() {
		$this->assertEquals( 'number', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'Number test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_number_test', $this->property->get_option( 'slug' ) );
	}
}
