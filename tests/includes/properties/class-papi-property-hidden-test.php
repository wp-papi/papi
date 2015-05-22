<?php

/**
 * Unit tests covering property hidden.
 *
 * @package Papi
 */

class Papi_Property_Hidden_Test extends Papi_Property_Test_Case {

	public $slug = 'hidden_test';

	public function get_value() {
		return 'hidden value';
	}

	public function get_expected() {
		return 'hidden value';
	}

	public function test_property_options() {
		$this->assertEquals( 'hidden', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'Hidden test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_hidden_test', $this->property->get_option( 'slug' ) );
	}

}
