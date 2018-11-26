<?php

/**
 * @group properties
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
		$this->assertSame( $this->get_expected( 'string_test' ), $this->properties[0]->format_value( $this->get_value( 'string_test' ), '', 0 ) );
		$this->assertSame( $this->get_expected( 'string_html_test' ), $this->properties[1]->format_value( $this->get_value( 'string_html_test' ), '', 0 ) );
		$this->assertSame( 'Hello', $this->properties[0]->format_value( '<p>Hello</p>', '', 0 ) );
		$this->assertSame( '<p>Hello</p>', $this->properties[1]->format_value( '<p>Hello</p>', '', 0 ) );
	}

	public function test_property_options() {
		$this->assertSame( 'string', $this->properties[0]->get_option( 'type' ) );
		$this->assertSame( 'String test', $this->properties[0]->get_option( 'title' ) );
		$this->assertSame( 'papi_string_test', $this->properties[0]->get_option( 'slug' ) );

		$this->assertSame( 'string', $this->properties[1]->get_option( 'type' ) );
		$this->assertSame( 'String html test', $this->properties[1]->get_option( 'title' ) );
		$this->assertSame( 'papi_string_html_test', $this->properties[1]->get_option( 'slug' ) );
	}
}
