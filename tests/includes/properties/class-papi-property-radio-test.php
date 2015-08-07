<?php

/**
 * Unit tests covering property radio.
 *
 * @package Papi
 */
class Papi_Property_Radio_Test extends Papi_Property_Test_Case {

	public $slug = 'radio_test';

	public function get_value() {
		return '#ffffff';
	}

	public function get_expected() {
		return '#ffffff';
	}

	public function test_property_options() {
		$this->assertEquals( 'radio', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'Radio test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_radio_test', $this->property->get_option( 'slug' ) );
	}
}
