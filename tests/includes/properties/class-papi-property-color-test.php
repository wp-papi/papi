<?php

/**
 * Unit tests covering property color.
 *
 * @package Papi
 */
class Papi_Property_Color_Test extends Papi_Property_Test_Case {

	public $slug = 'color_test';

	public function get_value() {
		return '#000000';
	}

	public function get_expected() {
		return '#000000';
	}

	public function test_property_format_value() {
		$this->assertEquals( '#000000', $this->property->format_value( '#000000', '', 0 ) );
		$this->assertEquals( '#ffffff', $this->property->format_value( '#ffffff', '', 0 ) );
		$this->assertEmpty( $this->property->format_value( null, '', 0 ) );
	}

	public function test_property_import_value() {
		$this->assertEquals( '#000000', $this->property->import_value( '#000000', '', 0 ) );
		$this->assertEquals( '#ffffff', $this->property->import_value( '#ffffff', '', 0 ) );
		$this->assertEmpty( $this->property->import_value( null, '', 0 ) );
	}

	public function test_property_options() {
		$this->assertEquals( 'color', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'Color test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_color_test', $this->property->get_option( 'slug' ) );
	}
}
