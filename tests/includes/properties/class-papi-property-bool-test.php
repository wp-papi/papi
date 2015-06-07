<?php

/**
 * Unit tests covering property bool.
 *
 * @package Papi
 */

class Papi_Property_Bool_Test extends Papi_Property_Test_Case {

	public $slug = 'bool_test';

	public function test_convert_type() {
		$this->assertEquals( 'bool', $this->property->convert_type );
	}

	public function test_default_value() {
		$this->assertFalse( $this->property->default_value );
	}

	public function get_value() {
		return true;
	}

	public function get_expected() {
		return true;
	}

	public function test_format_value() {
		$this->assertFalse( $this->property->format_value( 'false', '', 0 ) );
		$this->assertFalse( $this->property->format_value( '', '', 0 ) );
		$this->assertFalse( $this->property->format_value( null, '', 0 ) );
		$this->assertFalse( $this->property->format_value( (object) [], '', 0 ) );
		$this->assertFalse( $this->property->format_value( [], '', 0 ) );
		$this->assertTrue( $this->property->format_value( 'true', '', 0 ) );
		$this->assertTrue( $this->property->format_value( true, '', 0 ) );
	}

	public function test_property_options() {
		$this->assertEquals( 'bool', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'Bool test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_bool_test', $this->property->get_option( 'slug' ) );
	}

}
