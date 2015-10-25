<?php

class Papi_Property_Term_Test extends Papi_Property_Test_Case {

	public $slug = 'term_test';

	public function setUp() {
		parent::setUp();

		register_taxonomy( 'test_taxonomy', 'post' );

		$this->term_id = $this->factory->term->create( ['taxonomy' => 'test_taxonomy'] );
	}

	public function tearDown() {
		parent::tearDown();

		unset( $this->term_id );
	}

	public function assert_values( $expected, $actual ) {
		$this->assertSame( $expected->term_id, $actual->term_id );
	}

	public function get_value() {
		return $this->term_id;
	}

	public function get_expected() {
		return get_term( $this->term_id, 'test_taxonomy' );
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
