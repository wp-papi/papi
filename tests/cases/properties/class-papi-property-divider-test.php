<?php

class Papi_Property_Divider_Test extends Papi_Property_Test_Case {

	public $slug = 'divider_test';

	public function get_value() {
		return;
	}

	public function get_expected() {
		return;
	}

	public function test_property_options() {
		$this->assertSame( 'divider', $this->property->get_option( 'type' ) );
		$this->assertSame( 'Divider test', $this->property->get_option( 'title' ) );
		$this->assertSame( 'papi_divider_test', $this->property->get_option( 'slug' ) );
	}
}
