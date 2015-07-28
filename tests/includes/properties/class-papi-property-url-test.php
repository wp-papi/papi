<?php

/**
 * Unit tests covering property Url.
 *
 * @package Papi
 */

class Papi_Property_Url_Test extends Papi_Property_Test_Case {

	public $slug = 'url_test';

	public function get_value() {
		return 'http://github.com';
	}

	public function get_expected() {
		return 'http://github.com';
	}

	public function test_property_load_value() {
		$this->assertEquals( 'http://wordpress.org', $this->property->load_value( 'http://wordpress.org', '', 0 ) );
		$this->assertNull( $this->property->load_value( 'hello', '', 0 ) );
		$this->assertNull( $this->property->load_value( null, '', 0 ) );
	}

	public function test_property_options() {
		$this->assertEquals( 'url', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'Url test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_url_test', $this->property->get_option( 'slug' ) );
	}

	public function test_property_update_value() {
		$this->assertEquals( 'http://wordpress.org', $this->property->update_value( 'http://wordpress.org', '', 0 ) );
		$this->assertNull( $this->property->update_value( 'hello', '', 0 ) );
		$this->assertNull( $this->property->update_value( null, '', 0 ) );
	}

}
