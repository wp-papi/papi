<?php

/**
 * Unit tests covering property text.
 *
 * @package Papi
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

	public function test_property_options() {
		$this->assertEquals( 'text', $this->properties[0]->get_option( 'type' ) );
		$this->assertEquals( 'Text test', $this->properties[0]->get_option( 'title' ) );
		$this->assertEquals( 'papi_text_test', $this->properties[0]->get_option( 'slug' ) );

		$this->assertEquals( 'text', $this->properties[1]->get_option( 'type' ) );
		$this->assertEquals( 'Text html test', $this->properties[1]->get_option( 'title' ) );
		$this->assertEquals( 'papi_text_html_test', $this->properties[1]->get_option( 'slug' ) );
	}

}
