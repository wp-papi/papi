<?php

/**
 * Unit tests covering property link.
 *
 * @package Papi
 */
class Papi_Property_Link_Test extends Papi_Property_Test_Case {

	public $slug = 'link_test';

	public function get_value() {
		return [
            'url'    => 'http://example.org',
            'title'  => 'Example site',
            'target' => true
        ];
	}

	public function get_expected() {
		return (object) [
            'url'    => 'http://example.org',
            'title'  => 'Example site',
            'target' => '_blank'
        ];
	}

	public function test_property_convert_type() {
		$this->assertEquals( 'object', $this->property->convert_type );
	}

	public function test_property_default_value() {
		$this->assertEquals( [], $this->property->default_value );
	}

	public function test_property_options() {
		$this->assertEquals( 'link', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'Link test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_link_test', $this->property->get_option( 'slug' ) );
	}

}
