<?php

/**
 * Unit tests covering property editor.
 *
 * @package Papi
 */

class Papi_Property_Editor_Test extends Papi_Property_Test_Case {

	public $slug = 'editor_test';

	public function get_value() {
		return '<p>a bit of text with html tags hello, world</p>';
	}

	public function get_expected() {
		return "<p>a bit of text with html tags hello, world</p>\n";
	}

	public function test_property_options() {
		$this->assertEquals( 'editor', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'Editor test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_editor_test', $this->property->get_option( 'slug' ) );
	}

}
