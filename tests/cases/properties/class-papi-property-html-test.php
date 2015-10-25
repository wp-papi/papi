<?php

class Papi_Property_Html_Test extends Papi_Property_Test_Case {

	public $slugs = ['html_test', 'html_test_2'];

	public function get_value() {
		return;
	}

	public function get_expected() {
		return;
	}

	public function test_property_format_value() {
		$this->assertSame( $this->get_expected( 'html_test' ), $this->properties[0]->format_value( $this->get_value( 'html_test' ), '', 0 ) );
		$this->assertSame( $this->get_expected( 'html_test_2' ), $this->properties[1]->format_value( $this->get_value( 'html_test_2' ), '', 0 ) );
	}

	public function test_property_import_value() {
		$this->assertSame( $this->get_expected( 'html_test' ), $this->properties[0]->import_value( $this->get_value( 'html_test' ), '', 0 ) );
		$this->assertSame( $this->get_expected( 'html_test_2' ), $this->properties[1]->import_value( $this->get_value( 'html_test_2' ), '', 0 ) );
	}

	public function test_property_options() {
		$this->assertSame( 'html', $this->properties[0]->get_option( 'type' ) );
		$this->assertSame( 'Html test', $this->properties[0]->get_option( 'title' ) );
		$this->assertSame( 'papi_html_test', $this->properties[0]->get_option( 'slug' ) );
		$this->assertSame( '<p>Hello, world!</p>', $this->properties[0]->get_setting( 'html' ) );

		$this->assertSame( 'html', $this->properties[1]->get_option( 'type' ) );
		$this->assertSame( 'Html test 2', $this->properties[1]->get_option( 'title' ) );
		$this->assertSame( 'papi_html_test_2', $this->properties[1]->get_option( 'slug' ) );
	}

	public function test_property_output() {
		parent::test_property_output();

		papi_render_property( $this->properties[0] );
		$this->expectOutputRegex( '/\<p\>Hello, world!\<\/p\>/' );

		papi_render_property( $this->properties[1] );
		$this->expectOutputRegex( '/\<p\>Hello, callable!\<\/p\>/' );
	}
}
