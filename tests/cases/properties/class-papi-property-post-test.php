<?php

/**
 * @group properties
 */
class Papi_Property_Post_Test extends Papi_Property_Test_Case {

	public $slugs = ['post_test', 'post_test_2'];

	public function get_value() {
		$args = func_get_args();
		$args[0] = isset( $args[0] ) ? $args[0] : $this->slugs[0];
		switch ( $args[0] ) {
			default:
				return $this->post_id;
		}
	}

	public function get_expected() {
		$args = func_get_args();
		$args[0] = isset( $args[0] ) ? $args[0] : $this->slugs[0];
		switch ( $args[0] ) {
			default:
				return get_post( $this->post_id );
		}
	}

	public function assert_values( $expected, $actual, $slug ) {
		switch ( $slug ) {
			case 'post_test_2';
				break; // fail ok since we don't save a custom id.
			default:
				$this->assertSame( $expected->ID, $actual->ID );
				break;
		}
	}

	public function test_property_convert_type() {
		$this->assertSame( 'object', $this->properties[0]->convert_type );
	}

	public function test_property_format_value() {
		$this->assertEquals( get_post( $this->post_id ), $this->properties[0]->format_value( $this->post_id, '', 0 ) );
		$this->assertEquals( get_post( $this->post_id ), $this->properties[0]->format_value( strval( $this->post_id ), '', 0 ) );
		$this->assertNull( $this->properties[0]->format_value( 'hello', '', 0 ) );
		$this->assertNull( $this->properties[0]->format_value( null, '', 0 ) );
		$this->assertNull( $this->properties[0]->format_value( true, '', 0 ) );
		$this->assertNull( $this->properties[0]->format_value( false, '', 0 ) );
		$this->assertNull( $this->properties[0]->format_value( [], '', 0 ) );
		$this->assertNull( $this->properties[0]->format_value( (object) [], '', 0 ) );
	}

	public function test_property_format_value_meta_key() {
		$this->assertSame( 0, intval( $this->properties[1]->format_value( 1, '', 0 ) ) );
		update_post_meta( $this->post_id, 'custom_id', 1 );
		$this->assertSame( 1, intval( $this->properties[1]->format_value( 1, '', 0 ) ) );
	}

	public function test_property_options() {
		$this->assertSame( 'post', $this->properties[0]->get_option( 'type' ) );
		$this->assertSame( 'Post test', $this->properties[0]->get_option( 'title' ) );
		$this->assertSame( 'papi_post_test', $this->properties[0]->get_option( 'slug' ) );
	}

	public function test_property_settings() {
		$settings = $this->properties[0]->get_settings();
		$this->assertEmpty( $settings->placeholder );
		$this->assertTrue( $settings->select2 );
	}
}
