<?php

/**
 * Unit tests covering property html.
 *
 * @package Papi
 */

class Papi_Property_Html_Test extends Papi_Property_Test_Case {

	public $slug = 'html_test';

	public function get_value() {
		return;
	}

	public function get_expected() {
		return;
	}

	public function test_property_options() {
		$this->assertEquals( 'html', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'Html test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_html_test', $this->property->get_option( 'slug' ) );
		$this->assertEquals( '<p>Hello, world!</p>', $this->property->get_setting( 'html' ) );

		$property2 = $this->page_type->get_property( 'html_test_2' );
		$this->assertEquals( 'html', $property2->get_option( 'type' ) );
		$this->assertEquals( 'Html test 2', $property2->get_option( 'title' ) );
		$this->assertEquals( 'papi_html_test_2', $property2->get_option( 'slug' ) );
	}

	public function test_html_output() {
		papi_render_property( $this->property );
		$this->expectOutputRegex( '/\<p\>Hello, world!\<\/p\>/' );

		$property2 = $this->page_type->get_property( 'html_test_2' );
		papi_render_property( $property2 );
		$this->expectOutputRegex( '/\<p\>Hello, callable!\<\/p\>/' );
	}

}
