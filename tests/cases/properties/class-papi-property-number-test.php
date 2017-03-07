<?php

/**
 * @group properties
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
		$this->assertSame( '', $settings->max );
		$this->assertSame( '', $settings->min );
		$this->assertSame( 'any', $settings->step );
		$this->assertSame( 'number', $settings->type );
	}

	public function test_property_convert_type() {
		$this->assertSame( 'int', $this->property->convert_type );
	}

	public function test_property_format_value() {
		$this->assertSame( 42, $this->property->format_value( '42', '', 0 ) );
		$this->assertSame( 0, $this->property->format_value( 'hello', '', 0 ) );
		$this->assertSame( 0, $this->property->format_value( null, '', 0 ) );
	}

	public function test_property_options() {
		$this->assertSame( 'number', $this->property->get_option( 'type' ) );
		$this->assertSame( 'Number test', $this->property->get_option( 'title' ) );
		$this->assertSame( 'papi_number_test', $this->property->get_option( 'slug' ) );
	}
}
