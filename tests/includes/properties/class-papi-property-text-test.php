<?php

/**
 * Unit tests covering property text.
 *
 * @package Papi
 */

class Papi_Property_Text_Test extends Papi_Property_Test_Case {

	public $slug = 'text_test';

	public function get_value() {
		return 'hello';
	}

	public function get_expected() {
		return 'hello';
	}

	public function test_property_options() {
		$this->assertEquals( 'text', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'Text test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_text_test', $this->property->get_option( 'slug' ) );
	}

}
