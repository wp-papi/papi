<?php

/**
 * Unit tests covering property post.
 *
 * @package Papi
 */
class Papi_Property_Post_Test extends Papi_Property_Test_Case {

	public $slug = 'post_test';

	public function assert_values( $expected, $actual ) {
		$this->assertSame( $expected->ID, $actual->ID );
	}

	public function get_value() {
		return $this->post_id;
	}

	public function get_expected() {
		return get_post( $this->post_id );
	}

	public function test_property_convert_type() {
		$this->assertEquals( 'object', $this->property->convert_type );
	}

	public function test_property_format_value() {
		$this->assertEquals( get_post( $this->post_id ), $this->property->format_value( $this->post_id, '', 0 ) );
		$this->assertEquals( get_post( $this->post_id ), $this->property->format_value( strval( $this->post_id ), '', 0 ) );
		$this->assertNull( $this->property->format_value( 'hello', '', 0 ) );
		$this->assertNull( $this->property->format_value( null, '', 0 ) );
		$this->assertNull( $this->property->format_value( true, '', 0 ) );
		$this->assertNull( $this->property->format_value( false, '', 0 ) );
		$this->assertNull( $this->property->format_value( [], '', 0 ) );
		$this->assertNull( $this->property->format_value( (object) [], '', 0 ) );
	}

	public function test_property_import_value() {
		$this->assertEquals( $this->post_id, $this->property->import_value( $this->post_id, '', 0 ) );
		$this->assertEquals( $this->post_id, $this->property->import_value( strval( $this->post_id ), '', 0 ) );
		$this->assertEquals( $this->post_id, $this->property->import_value( get_post( $this->post_id ), '', 0 ) );
		$this->assertNull( $this->property->import_value( 'hello', '', 0 ) );
		$this->assertNull( $this->property->import_value( null, '', 0 ) );
		$this->assertNull( $this->property->import_value( true, '', 0 ) );
		$this->assertNull( $this->property->import_value( false, '', 0 ) );
		$this->assertNull( $this->property->import_value( [], '', 0 ) );
		$this->assertNull( $this->property->import_value( (object) [], '', 0 ) );
	}

	public function test_property_options() {
		$this->assertEquals( 'post', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'Post test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_post_test', $this->property->get_option( 'slug' ) );
	}

	public function test_property_settings() {
		$settings = $this->property->get_settings();
		$this->assertEmpty( $settings->placeholder );
		$this->assertTrue( $settings->select2 );
	}
}
