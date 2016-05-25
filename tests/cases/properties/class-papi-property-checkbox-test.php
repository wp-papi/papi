<?php

/**
 * @group properties
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
		$this->assertSame( 'array', $this->property->convert_type );
	}

	public function test_property_default_value() {
		$this->assertSame( [], $this->property->default_value );
	}

	public function test_property_format_value() {
		$this->assertSame( ['hello'], $this->property->format_value( 'hello', '', 0 ) );
		$this->assertSame( ['hello'], $this->property->format_value( ['hello'], '', 0 ) );
		$this->assertSame( [2014], $this->property->format_value( ['2014'], '', 0 ) );
		$this->assertSame( [12.3], $this->property->format_value( ['12.3'], '', 0 ) );
		$this->assertSame( [true], $this->property->format_value( ['true'], '', 0 ) );
		$this->assertSame( [false], $this->property->format_value( ['false'], '', 0 ) );
		$this->assertSame( [2014], $this->property->format_value( '2014', '', 0 ) );
		$this->assertSame( [12.3], $this->property->format_value( '12.3', '', 0 ) );
		$this->assertSame( [true], $this->property->format_value( 'true', '', 0 ) );
		$this->assertSame( [false], $this->property->format_value( 'false', '', 0 ) );
		$this->assertEmpty( $this->property->format_value( null, '', 0 ) );
	}

	public function test_property_import_value() {
		$this->assertSame( ['hello'], $this->property->import_value( 'hello', '', 0 ) );
		$this->assertSame( ['hello'], $this->property->import_value( ['hello'], '', 0 ) );
		$this->assertNull( $this->property->import_value( null, '', 0 ) );
		$this->assertNull( $this->property->import_value( true, '', 0 ) );
		$this->assertNull( $this->property->import_value( false, '', 0 ) );
		$this->assertNull( $this->property->import_value( (object) [], '', 0 ) );
		$this->assertNull( $this->property->import_value( 1, '', 0 ) );
	}

	public function test_property_options() {
		$this->assertSame( 'checkbox', $this->property->get_option( 'type' ) );
		$this->assertSame( 'Checkbox test', $this->property->get_option( 'title' ) );
		$this->assertSame( 'papi_checkbox_test', $this->property->get_option( 'slug' ) );
	}

	public function test_property_settings() {
		$this->assertNotEmpty( $this->property->get_setting( 'items' ) );
	}
}
