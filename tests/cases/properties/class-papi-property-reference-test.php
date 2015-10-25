<?php

class Papi_Property_Reference_Test extends Papi_Property_Test_Case {

	public $slug = 'reference_test';

	public function get_value() {
		return;
	}

	public function get_expected() {
		return;
	}

	public function test_property_options() {
		$this->assertSame( 'reference', $this->property->get_option( 'type' ) );
		$this->assertSame( 'Reference test', $this->property->get_option( 'title' ) );
		$this->assertSame( 'papi_reference_test', $this->property->get_option( 'slug' ) );
	}

	public function test_property_settings() {
		$this->assertTrue( is_array( $this->property->get_setting( 'slug' ) ) );
		$this->assertTrue( is_array( $this->property->get_setting( 'page_type' ) ) );
	}
}
