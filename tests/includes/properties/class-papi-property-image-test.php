<?php

/**
 * Unit tests covering property image.
 *
 * @package Papi
 */
class Papi_Property_Image_Test extends Papi_Property_Test_Case {

	public $slug = 'image_test';

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

	public function test_property_import_value() {
		$this->assertEmpty( $this->property->import_value( $this->get_value(), '', 0 ) );
	}

	public function test_property_options() {
		$this->assertEquals( 'image', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'Image test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_image_test', $this->property->get_option( 'slug' ) );
	}

	public function test_property_settings() {
		$this->assertFalse( $this->property->get_setting( 'multiple' ) );
	}
}
