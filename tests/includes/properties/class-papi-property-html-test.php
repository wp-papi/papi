<?php

/**
 * Unit tests covering property html.
 *
 * @package Papi
 */
class Papi_Property_Html_Test extends Papi_Property_Test_Case {

	public $slugs = ['html_test', 'html_test_2'];

	public function get_value() {
		return;
	}

	public function get_expected() {
		return;
	}

	public function test_property_options() {
		$this->assertEquals( 'html', $this->properties[0]->get_option( 'type' ) );
		$this->assertEquals( 'Html test', $this->properties[0]->get_option( 'title' ) );
		$this->assertEquals( 'papi_html_test', $this->properties[0]->get_option( 'slug' ) );
		$this->assertEquals( '<p>Hello, world!</p>', $this->properties[0]->get_setting( 'html' ) );

		$this->assertEquals( 'html', $this->properties[1]->get_option( 'type' ) );
		$this->assertEquals( 'Html test 2', $this->properties[1]->get_option( 'title' ) );
		$this->assertEquals( 'papi_html_test_2', $this->properties[1]->get_option( 'slug' ) );
	}

	public function test_property_output() {
		parent::test_property_output();

		papi_render_property( $this->properties[0] );
		$this->expectOutputRegex( '/\<p\>Hello, world!\<\/p\>/' );

		papi_render_property( $this->properties[1] );
		$this->expectOutputRegex( '/\<p\>Hello, callable!\<\/p\>/' );
	}

}
