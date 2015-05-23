<?php

/**
 * Unit tests covering property string.
 *
 * @package Papi
 */

class Papi_Property_String_Test extends Papi_Property_Test_Case {

	public $slug = 'string_test';

	public function get_value() {
		return 'hello';
	}

	public function get_expected() {
		return 'hello';
	}

	public function test_property_options() {
		$this->assertEquals( 'string', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'String test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_string_test', $this->property->get_option( 'slug' ) );
	}

}
