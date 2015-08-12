<?php

/**
 * Unit tests covering property string.
 *
 * @package Papi
 */
class Papi_Property_String_Test extends Papi_Property_Test_Case {

	public $slugs = ['string_test', 'string_html_test'];

	public function get_value() {
		$args = func_get_args();
		switch ( $args[0] ) {
			case 'string_html_test':
				return '<p>hej</p>';
			default:
				return 'hello';
		}
	}

	public function get_expected() {
		$args = func_get_args();
		switch ( $args[0] ) {
			case 'string_html_test':
				return '<p>hej</p>';
			default:
				return 'hello';
		}
	}

	public function test_property_format_value() {
		$this->assertEquals( 'Hello', $this->properties[0]->format_value( '<p>Hello</p>', '', 0 ) );
		$this->assertEquals( '<p>Hello</p>', $this->properties[1]->format_value( '<p>Hello</p>', '', 0 ) );
	}

	public function test_property_options() {
		$this->assertEquals( 'string', $this->properties[0]->get_option( 'type' ) );
		$this->assertEquals( 'String test', $this->properties[0]->get_option( 'title' ) );
		$this->assertEquals( 'papi_string_test', $this->properties[0]->get_option( 'slug' ) );

		$this->assertEquals( 'string', $this->properties[1]->get_option( 'type' ) );
		$this->assertEquals( 'String html test', $this->properties[1]->get_option( 'title' ) );
		$this->assertEquals( 'papi_string_html_test', $this->properties[1]->get_option( 'slug' ) );
	}

}
