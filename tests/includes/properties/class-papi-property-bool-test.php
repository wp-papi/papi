<?php

/**
 * Unit tests covering property bool.
 *
 * @package Papi
 */
class Papi_Property_Bool_Test extends Papi_Property_Test_Case {

	public $slug = 'bool_test';

	public function get_value() {
		return true;
	}

	public function get_expected() {
		return true;
	}

	public function test_property_convert_type() {
		$this->assertEquals( 'bool', $this->property->convert_type );
	}

	public function test_property_default_value() {
		$this->assertFalse( $this->property->default_value );
	}

	public function test_property_format_value() {
		$this->assertFalse( $this->property->format_value( 'false', '', 0 ) );
		$this->assertFalse( $this->property->format_value( '', '', 0 ) );
		$this->assertFalse( $this->property->format_value( null, '', 0 ) );
		$this->assertFalse( $this->property->format_value( (object) [], '', 0 ) );
		$this->assertFalse( $this->property->format_value( [], '', 0 ) );
		$this->assertTrue( $this->property->format_value( 'true', '', 0 ) );
		$this->assertTrue( $this->property->format_value( true, '', 0 ) );
	}

	public function test_property_import_value() {
		$this->assertFalse( $this->property->import_value( 'false', '', 0 ) );
		$this->assertFalse( $this->property->import_value( '', '', 0 ) );
		$this->assertFalse( $this->property->import_value( null, '', 0 ) );
		$this->assertFalse( $this->property->import_value( (object) [], '', 0 ) );
		$this->assertFalse( $this->property->import_value( [], '', 0 ) );
		$this->assertTrue( $this->property->import_value( 'true', '', 0 ) );
		$this->assertTrue( $this->property->import_value( true, '', 0 ) );
	}

	public function test_property_options() {
		$this->assertEquals( 'bool', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'Bool test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_bool_test', $this->property->get_option( 'slug' ) );
	}

	public function test_property_load_value() {
		$this->assertTrue( $this->property->load_value( '1', '', 0 ) );
		$this->assertFalse( $this->property->load_value( [], '', 0 ) );
	}

	public function test_property_update_value() {
		$this->assertFalse( $this->property->update_value( 'false', '', 0 ) );
		$this->assertFalse( $this->property->update_value( '', '', 0 ) );
		$this->assertFalse( $this->property->update_value( null, '', 0 ) );
		$this->assertFalse( $this->property->update_value( (object) [], '', 0 ) );
		$this->assertFalse( $this->property->update_value( [], '', 0 ) );
		$this->assertTrue( $this->property->update_value( 'true', '', 0 ) );
		$this->assertTrue( $this->property->update_value( true, '', 0 ) );
	}
}
