<?php

/**
 * @group properties
 */
class Papi_Property_Sidebar_Test extends Papi_Property_Test_Case {

	public $slug = 'sidebar_test';

	public function assert_values( $expected, $actual, $slug ) {
		$this->assertSame( $expected, $actual );
	}

	public function get_value() {
		return 'home_right_1';
	}

	public function get_expected() {
		return 'home_right_1';
	}

	public function test_property_convert_type() {
		$this->assertSame( 'string', $this->property->convert_type );
	}

	public function test_property_options() {
		$this->assertSame( 'sidebar', $this->property->get_option( 'type' ) );
		$this->assertSame( 'Sidebar test', $this->property->get_option( 'title' ) );
		$this->assertSame( 'papi_sidebar_test', $this->property->get_option( 'slug' ) );
	}

	public function test_property_settings() {
		$settings = $this->property->get_settings();
		$this->assertSame( 'Select sidebar', $settings->placeholder );
		$this->assertTrue( $settings->select2 );
	}
}
