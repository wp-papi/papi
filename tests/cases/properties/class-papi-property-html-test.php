<?php

/**
 * @group properties
 */
class Papi_Property_Html_Test extends Papi_Property_Test_Case {

	public $slugs = ['html_test', 'html_test_2', 'html_save_test'];

	public function get_value() {
		$args = func_get_args();
		switch ( $args[0] ) {
			case 'html_save_test':
				return '<p>Hello, world!</p>';
			default:
				return;
		}
	}

	public function get_update_value() {
		$args = func_get_args();
		switch ( $args[0] ) {
			case 'html_save_test':
				return '<p>Hello, world 2!</p>';
			default:
				return;
		}
	}

	public function get_expected() {
		$args = func_get_args();
		switch ( $args[0] ) {
			case 'html_save_test':
				return '<p>Hello, world!</p>';
			default:
				return;
		}
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

		$this->assertSame( 'html', $this->properties[2]->get_option( 'type' ) );
		$this->assertSame( 'Html save test', $this->properties[2]->get_option( 'title' ) );
		$this->assertSame( 'papi_html_save_test', $this->properties[2]->get_option( 'slug' ) );
	}

	public function test_property_output() {
		parent::test_property_output();

		papi_render_property( $this->properties[0] );
		$this->expectOutputRegex( '/\<p\>Hello, world!\<\/p\>/' );

		papi_render_property( $this->properties[1] );
		$this->expectOutputRegex( '/\<p\>Hello, callable!\<\/p\>/' );

		papi_render_property( $this->properties[2] );
		$this->expectOutputRegex( '/\<p\>Hello, world!\<\/p\>/' );
	}
}
