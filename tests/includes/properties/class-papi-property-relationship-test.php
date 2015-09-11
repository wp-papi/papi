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

	public function test_property_import_value() {
		$output = $this->property->import_value( [], '', 0 );
		$this->assertEmpty( $output );

		$output = $this->property->import_value( (object) [], '', 0 );
		$this->assertEmpty( $output );

		$output = $this->property->import_value( $this->post_id, '', 0 );
		$this->assertEquals( $this->get_value(), $output );

		$output = $this->property->import_value( $this->get_value(), '', 0 );
		$this->assertEquals( $this->get_value(), $output );

		$output = $this->property->import_value( $this->get_expected(), '', 0 );
		$this->assertEquals( $this->get_value(), $output );

		$this->assertNull( $this->property->import_value( 'hello', '', 0 ) );
		$this->assertNull( $this->property->import_value( null, '', 0 ) );
		$this->assertNull( $this->property->import_value( true, '', 0 ) );
		$this->assertNull( $this->property->import_value( false, '', 0 ) );
	}

	public function test_property_load_value() {
		$this->assertEquals( ['yes' => true], $this->property->load_value( '{"yes":true}', '', 0 ) );
		$this->assertEquals( [], $this->property->load_value( '{}', '', 0 ) );
		$this->assertEquals( [1, 2, 3], $this->property->load_value( '[1, 2, 3]', '', 0 ) );
		$this->assertEquals( [], $this->property->load_value( '[]', '', 0 ) );
	}

	public function test_property_options() {
		$this->assertEquals( 'relationship', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'Relationship test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_relationship_test', $this->property->get_option( 'slug' ) );
	}
}
