<?php

/**
 * Unit tests covering property relationship.
 *
 * @package Papi
 */
class Papi_Property_Relationship_Test extends Papi_Property_Test_Case {

	public $slug = 'relationship_test';

	public function get_value() {
		return [$this->post_id];
	}

	public function get_expected() {
		return [get_post( $this->post_id )];
	}

	public function test_property_convert_type() {
		$this->assertEquals( 'array', $this->property->convert_type );
	}

	public function test_property_default_value() {
		$this->assertEquals( [], $this->property->default_value );
	}

	public function test_property_options() {
		$this->assertEquals( 'relationship', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'Relationship test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_relationship_test', $this->property->get_option( 'slug' ) );
	}

}
