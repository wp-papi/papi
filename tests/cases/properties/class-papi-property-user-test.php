<?php

class Papi_Property_User_Test extends Papi_Property_Test_Case {

	public $slug = 'user_test';

	public function assert_values( $expected, $actual ) {
		$this->assertSame( $expected->ID, $actual->ID );
	}

	public function get_value() {
		return new WP_User( 1 );
	}

	public function get_expected() {
		return new WP_User( 1 );
	}

	public function test_property_convert_type() {
		$this->assertSame( 'int', $this->property->convert_type );
	}

	public function test_property_format_value() {
		$this->assertEquals( new WP_User( 1 ), $this->property->format_value( '1', '', 0 ) );
		$this->assertEquals( new WP_User( 1 ), $this->property->format_value( 1, '', 0 ) );
		$this->assertEquals( new WP_User( 1 ), $this->property->format_value( (object) ['ID' => 1], '', 0 ) );
		$this->assertEquals( new WP_User( 1 ), $this->property->format_value( new WP_User( 1 ), '', 0 ) );
		$this->assertNull( $this->property->format_value( null, '', 0 ) );
	}

	public function test_property_import_value() {
		$this->assertSame( 1, $this->property->import_value( $this->get_value(), '', 0 ) );
		$this->assertSame( 1, $this->property->import_value( 1, '', 0 ) );
		$this->assertSame( 1, $this->property->import_value( '1', '', 0 ) );
		$this->assertNull( $this->property->import_value( null, '', 0 ) );
		$this->assertNull( $this->property->import_value( true, '', 0 ) );
		$this->assertNull( $this->property->import_value( false, '', 0 ) );
		$this->assertNull( $this->property->import_value( [], '', 0 ) );
		$this->assertNull( $this->property->import_value( (object) [], '', 0 ) );
	}

	public function test_property_get_value() {
		$this->assertSame( 0, $this->property->get_value() );
		$this->save_properties( $this->property );
		$user = $this->get_expected();
		$this->assertSame( $user->ID, $this->property->get_value() );
	}

	public function test_property_options() {
		$this->assertSame( 'user', $this->property->get_option( 'type' ) );
		$this->assertSame( 'User test', $this->property->get_option( 'title' ) );
		$this->assertSame( 'papi_user_test', $this->property->get_option( 'slug' ) );
	}

	public function test_property_settings() {
		$settings = $this->property->get_settings();
		$this->assertSame( 'Select user', $settings->placeholder );
		$this->assertTrue( $settings->select2 );
	}
}
