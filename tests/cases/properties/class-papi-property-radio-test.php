<?php

class Papi_Property_Radio_Test extends Papi_Property_Test_Case {

	public $slug = 'radio_test';

	public function get_value() {
		return '#ffffff';
	}

	public function get_expected() {
		return '#ffffff';
	}

	public function test_property_options() {
		$this->assertSame( 'radio', $this->property->get_option( 'type' ) );
		$this->assertSame( 'Radio test', $this->property->get_option( 'title' ) );
		$this->assertSame( 'papi_radio_test', $this->property->get_option( 'slug' ) );
	}

	public function test_property_format_value() {
		$this->assertSame( 2014, $this->property->format_value( '2014', '', 0 ) );
		$this->assertSame( 12.3, $this->property->format_value( '12.3', '', 0 ) );
		$this->assertSame( true, $this->property->format_value( 'true', '', 0 ) );
		$this->assertSame( false, $this->property->format_value( 'false', '', 0 ) );
	}
}
