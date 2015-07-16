<?php

/**
 * Unit tests covering property user.
 *
 * @package Papi
 */

class Papi_Property_User_Test extends Papi_Property_Test_Case {

	public $slug = 'user_test';

	public function get_value() {
		return new WP_User( 1 );
	}

	public function get_expected() {
		return new WP_User( 1 );
	}

	public function test_convert_type() {
		$this->assertEquals( 'int', $this->property->convert_type );
	}

	public function test_format_value() {
		$this->assertEquals( new WP_User( 1 ), $this->property->format_value( '1', '', 0 ) );
		$this->assertEquals( new WP_User( 1 ), $this->property->format_value( 1, '', 0 ) );
	}

	public function test_property_options() {
		$this->assertEquals( 'user', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'User test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_user_test', $this->property->get_option( 'slug' ) );
	}

	public function test_property_settings() {
		$settings = $this->property->get_settings();
		$this->assertEquals( 'Select user', $settings->placeholder );
		$this->assertTrue( $settings->select2 );
	}

}
