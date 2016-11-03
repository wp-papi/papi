<?php

/**
 * @group properties
 */
class Papi_Property_Dropdown_Test extends Papi_Property_Test_Case {

	public $slugs = ['dropdown_test', 'dropdown_test_2', 'dropdown_test_3'];

	public function get_value() {
		$args = func_get_args();
		$args[0] = isset( $args[0] ) ? $args[0] : $this->slugs[0];
		switch ( $args[0] ) {
			case 'dropdown_test_3':
				return [1, 2, 3];
			default:
				return '#ffffff';
		}
	}

	public function get_expected() {
		$args = func_get_args();
		$args[0] = isset( $args[0] ) ? $args[0] : $this->slugs[0];
		switch ( $args[0] ) {
			case 'dropdown_test_3':
				return [1, 2, 3];
			default:
				return '#ffffff';
		}
	}

	public function test_property_convert_type() {
		$this->assertSame( 'mixed', $this->properties[0]->convert_type );
		$this->assertSame( 'mixed', $this->properties[1]->convert_type );
		$this->assertSame( 'mixed', $this->properties[2]->convert_type );
	}

	public function test_property_format_value() {
		$this->assertSame( '#ffffff', $this->properties[0]->format_value( '#ffffff', '', 0 ) );
		$this->assertSame( '#000000', $this->properties[1]->format_value( '#000000', '', 0 ) );
		$this->assertSame( '2014', $this->properties[1]->format_value( '2014', '', 0 ) );
		$this->assertSame( '12.3', $this->properties[1]->format_value( '12.3', '', 0 ) );
		$this->assertSame( 'true', $this->properties[1]->format_value( 'true', '', 0 ) );
		$this->assertSame( 'false', $this->properties[1]->format_value( 'false', '', 0 ) );
		$this->assertSame( [1, 2, 3], $this->properties[1]->format_value( [1, 2, 3], '', 0 ) );
		$this->assertSame( ['1', '2', '3'], $this->properties[1]->format_value( ['1', '2', '3'], '', 0 ) );
		$this->assertEmpty( $this->properties[0]->format_value( null, '', 0 ) );
		$this->assertEmpty( $this->properties[1]->format_value( null, '', 0 ) );
		$this->assertEmpty( $this->properties[2]->format_value( null, '', 0 ) );
	}

	public function test_property_import_value() {
		$this->assertSame( '#ffffff', $this->properties[0]->import_value( '#ffffff', '', 0 ) );
		$this->assertSame( '#000000', $this->properties[1]->import_value( '#000000', '', 0 ) );
		$this->assertSame( [1, 2, 3], $this->properties[2]->import_value( [1, 2, 3], '', 0 ) );
		$this->assertEmpty( $this->properties[0]->import_value( null, '', 0 ) );
		$this->assertEmpty( $this->properties[1]->import_value( null, '', 0 ) );
	}

	public function test_property_options() {
		$this->assertSame( 'dropdown', $this->properties[0]->get_option( 'type' ) );
		$this->assertSame( 'Dropdown test', $this->properties[0]->get_option( 'title' ) );
		$this->assertSame( 'papi_dropdown_test', $this->properties[0]->get_option( 'slug' ) );

		$this->assertSame( 'dropdown', $this->properties[1]->get_option( 'type' ) );
		$this->assertSame( 'Dropdown test 2', $this->properties[1]->get_option( 'title' ) );
		$this->assertSame( 'papi_dropdown_test_2', $this->properties[1]->get_option( 'slug' ) );

		$this->assertSame( 'dropdown', $this->properties[2]->get_option( 'type' ) );
		$this->assertSame( 'Dropdown test 3', $this->properties[2]->get_option( 'title' ) );
		$this->assertSame( 'papi_dropdown_test_3', $this->properties[2]->get_option( 'slug' ) );
	}

	public function test_property_settings() {
		$settings = $this->properties[0]->get_settings();
		$this->assertEmpty( $settings->placeholder );
		$this->assertSame( '#ffffff', $settings->items['White'] );
		$this->assertSame( '#000000', $settings->items['Black'] );
		$this->assertTrue( $settings->select2 );

		$settings = $this->properties[1]->get_settings();
		$this->assertSame( 'Pick one', $settings->placeholder );
		$this->assertSame( '#ffffff', $settings->items['White'] );
		$this->assertSame( '#000000', $settings->items['Black'] );
		$this->assertTrue( $settings->select2 );

		$settings = $this->properties[2]->get_settings();
		$this->assertSame( 'Pick multiple', $settings->placeholder );
		$this->assertSame( [0, 1, 2, 3, 4, 5], $settings->items );
		$this->assertTrue( $settings->select2 );
	}
}
