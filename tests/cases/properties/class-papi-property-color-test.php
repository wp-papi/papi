<?php

class Papi_Property_Color_Test extends Papi_Property_Test_Case {

	public $slug = 'color_test';

	public function get_value() {
		return '#000000';
	}

	public function get_expected() {
		return '#000000';
	}

	public function test_property_format_value() {
		$this->assertSame( '#000000', $this->property->format_value( '#000000', '', 0 ) );
		$this->assertSame( '#ffffff', $this->property->format_value( '#ffffff', '', 0 ) );
		$this->assertEmpty( $this->property->format_value( null, '', 0 ) );
	}

	public function test_property_import_value() {
		$this->assertSame( '#000000', $this->property->import_value( '#000000', '', 0 ) );
		$this->assertSame( '#ffffff', $this->property->import_value( '#ffffff', '', 0 ) );
		$this->assertEmpty( $this->property->import_value( null, '', 0 ) );
	}

	public function test_property_options() {
		$this->assertSame( 'color', $this->property->get_option( 'type' ) );
		$this->assertSame( 'Color test', $this->property->get_option( 'title' ) );
		$this->assertSame( 'papi_color_test', $this->property->get_option( 'slug' ) );
	}
}
