<?php

/**
 * @group properties
 */
class Papi_Property_Term_Test extends Papi_Property_Test_Case {

	public $slugs = ['term_test', 'term_test_2'];

	public function setUp() {
		parent::setUp();

		register_taxonomy( 'test_taxonomy', 'post' );

		$this->term_id = $this->factory->term->create( ['taxonomy' => 'test_taxonomy'] );
	}

	public function tearDown() {
		parent::tearDown();

		unset( $this->term_id );
	}

	public function get_value() {
		$args = func_get_args();
		$args[0] = isset( $args[0] ) ? $args[0] : $this->slugs[0];
		switch ( $args[0] ) {
			default:
				return $this->term_id;
		}
	}

	public function get_expected() {
		$args = func_get_args();
		$args[0] = isset( $args[0] ) ? $args[0] : $this->slugs[0];
		switch ( $args[0] ) {
			default:
				return get_term( $this->term_id, 'test_taxonomy' );
		}
	}

	public function assert_values( $expected, $actual, $slug ) {
		switch ( $slug ) {
			case 'term_test_2';
				break; // fail ok since we don't save a custom id.
			default:
				$this->assertSame( $expected->term_id, $actual->term_id );
				break;
		}
	}

	public function test_property_convert_type() {
		$this->assertSame( 'object', $this->property->convert_type );
	}

	public function test_property_format_value() {
		$this->assertEquals( get_term( $this->term_id, 'test_taxonomy' ), $this->property->format_value( $this->term_id, '', 0 ) );
		$this->assertEquals( get_term( $this->term_id, 'test_taxonomy' ), $this->property->format_value( strval( $this->term_id ), '', 0 ) );
		$this->assertNull( $this->property->format_value( 'hello', '', 0 ) );
		$this->assertNull( $this->property->format_value( null, '', 0 ) );
		$this->assertNull( $this->property->format_value( true, '', 0 ) );
		$this->assertNull( $this->property->format_value( false, '', 0 ) );
		$this->assertNull( $this->property->format_value( [], '', 0 ) );
		$this->assertNull( $this->property->format_value( (object) [], '', 0 ) );
	}

	public function test_property_format_value_meta_key() {
		$this->assertSame( 0, intval( $this->properties[1]->format_value( 1, '', 0 ) ) );
		update_term_meta( $this->term_id, 'custom_id', 1 );
		$this->assertSame( 1, intval( $this->properties[1]->format_value( 1, '', 0 ) ) );
	}

	public function test_property_import_value() {
		$this->assertSame( $this->term_id, $this->property->import_value( $this->term_id, '', 0 ) );
		$this->assertSame( $this->term_id, $this->property->import_value( strval( $this->term_id ), '', 0 ) );
		$this->assertSame( $this->term_id, $this->property->import_value( get_term( $this->term_id, 'test_taxonomy' ), '', 0 ) );
		$this->assertNull( $this->property->import_value( 'hello', '', 0 ) );
		$this->assertNull( $this->property->import_value( null, '', 0 ) );
		$this->assertNull( $this->property->import_value( true, '', 0 ) );
		$this->assertNull( $this->property->import_value( false, '', 0 ) );
		$this->assertNull( $this->property->import_value( [], '', 0 ) );
		$this->assertNull( $this->property->import_value( (object) [], '', 0 ) );
	}

	public function test_property_options() {
		$this->assertSame( 'term', $this->property->get_option( 'type' ) );
		$this->assertSame( 'Term test', $this->property->get_option( 'title' ) );
		$this->assertSame( 'papi_term_test', $this->property->get_option( 'slug' ) );
	}

	public function test_property_settings() {
		$settings = $this->property->get_settings();
		$this->assertTrue( $settings->select2 );
	}
}
