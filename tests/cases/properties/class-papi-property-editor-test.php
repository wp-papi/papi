<?php

class Papi_Property_Editor_Test extends Papi_Property_Test_Case {

	public $slug = 'editor_test';

	public function get_value() {
		return '<p>a bit of text with html tags hello, world</p>';
	}

	public function get_expected() {
		return "<p>a bit of text with html tags hello, world</p>\n";
	}

	public function test_property_format_value() {
		$this->assertSame( $this->get_expected(), $this->property->format_value( $this->get_value(), '', 0 ) );
	}

	public function test_property_import_value() {
		$this->assertSame( $this->get_value(), $this->property->import_value( $this->get_value(), '', 0 ) );
	}

	public function test_property_options() {
		$this->assertSame( 'editor', $this->property->get_option( 'type' ) );
		$this->assertSame( 'Editor test', $this->property->get_option( 'title' ) );
		$this->assertSame( 'papi_editor_test', $this->property->get_option( 'slug' ) );
	}
}
