<?php

class Papi_Property_Hidden_Test extends Papi_Property_Test_Case {

	public $slug = 'hidden_test';

	public function get_value() {
		return 'hidden value';
	}

	public function get_expected() {
		return 'hidden value';
	}

	public function test_property_options() {
		$this->assertSame( 'hidden', $this->property->get_option( 'type' ) );
		$this->assertSame( 'Hidden test', $this->property->get_option( 'title' ) );
		$this->assertSame( 'papi_hidden_test', $this->property->get_option( 'slug' ) );
	}
}
