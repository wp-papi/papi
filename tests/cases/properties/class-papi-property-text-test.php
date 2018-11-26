<?php

/**
 * @group properties
 */
class Papi_Property_Text_Test extends Papi_Property_Test_Case {

	public $slugs = ['text_test', 'text_html_test'];

	public function get_value() {
		$args = func_get_args();
		switch ( $args[0] ) {
			case 'text_html_test':
				return '<p>hej</p>';
			default:
				return 'hello';
		}
	}

	public function get_expected() {
		$args = func_get_args();
		switch ( $args[0] ) {
			case 'text_html_test':
				return '<p>hej</p>';
			default:
				return 'hello';
		}
	}

	public function test_property_format_value() {
		$this->assertSame( $this->get_expected( 'text_test' ), $this->properties[0]->format_value( $this->get_value( 'text_test' ), '', 0 ) );
		$this->assertSame( $this->get_expected( 'text_html_test' ), $this->properties[1]->format_value( $this->get_value( 'text_html_test' ), '', 0 ) );
		$this->assertSame( 'Hello', $this->properties[0]->format_value( '<p>Hello</p>', '', 0 ) );
		$this->assertSame( '<p>Hello</p>', $this->properties[1]->format_value( '<p>Hello</p>', '', 0 ) );
		$this->assertSame( "Hello<br />\nWorld", $this->properties[0]->format_value( "Hello\nWorld", '', 0 ) );
	}

	public function test_property_options() {
		$this->assertSame( 'text', $this->properties[0]->get_option( 'type' ) );
		$this->assertSame( 'Text test', $this->properties[0]->get_option( 'title' ) );
		$this->assertSame( 'papi_text_test', $this->properties[0]->get_option( 'slug' ) );

		$this->assertSame( 'text', $this->properties[1]->get_option( 'type' ) );
		$this->assertSame( 'Text html test', $this->properties[1]->get_option( 'title' ) );
		$this->assertSame( 'papi_text_html_test', $this->properties[1]->get_option( 'slug' ) );
	}
}
