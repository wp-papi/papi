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
            'target' => '_blank'
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

	public function test_property_import_value() {
		$expected = [
			'papi_link_test_url'    => 'http://example.org',
			'papi_link_test_title'  => 'Example site',
			'papi_link_test_target' => '_blank',
			'papi_link_test'        => 1
		];
		$this->assertEquals( $expected, $this->property->import_value( $this->get_value(), $this->property->slug, 0 ) );
		$this->assertEquals( $expected, $this->property->import_value( (object) $this->get_value(), $this->property->slug, 0 ) );

		$this->assertNull( $this->property->import_value( null, '', 0 ) );
		$this->assertNull( $this->property->import_value( true, '', 0 ) );
		$this->assertNull( $this->property->import_value( false, '', 0 ) );
		$this->assertNull( $this->property->import_value( 1, '', 0 ) );
		$this->assertNull( $this->property->import_value( 'test', '', 0 ) );
	}

	public function test_property_options() {
		$this->assertEquals( 'link', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'Link test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_link_test', $this->property->get_option( 'slug' ) );
	}

}
