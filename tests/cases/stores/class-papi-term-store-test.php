<?php

class Papi_Term_Store_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		if ( ! papi_supports_term_meta() ) {
			$this->markTestSkipped( 'Term metadata is not supported' );
		}

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );

		$this->term_id = $this->factory->term->create();
		$_GET['term_id'] = $this->term_id;
		$this->store = papi_get_meta_store( $this->term_id, 'term' );
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->term_id, $_GET['term_id'], $this->store );
	}

	public function test_term_store_construct() {
		$this->assertTrue( ( new Papi_Term_Store )->valid() );
		$this->assertTrue( ( new Papi_Term_Store( $this->term_id ) )->valid() );
	}

	public function test_get_permalink() {
		$permalink = $this->store->get_permalink();
		$this->assertFalse( empty( $permalink ) );
	}

	public function test_get_term() {
		$this->assertTrue( is_object( $this->store->get_term() ) );
		$this->assertSame( $this->term_id, $this->store->get_term()->term_id );
	}

	public function test_get_value() {
		$handler = new Papi_Admin_Meta_Handler();

		update_term_meta( $this->term_id, papi_get_page_type_key(), 'properties-taxonomy-type' );

		update_term_meta( $this->term_id, 'string_test', 'Janni' );
		$this->assertSame( 'Janni', $this->store->get_value( 'string_test' ) );

		update_term_meta( $this->term_id, 'string_test', 'Fredrik' );

		$this->assertSame( 'Fredrik', $this->store->get_value( 'string_test' ) );

		$property = papi_property( [
			'type'  => 'number',
			'title' => 'Number',
			'slug'  => 'number_test'
		] );

		$this->assertSame( 'number', $property->type );
		$this->assertSame( 'Number', $property->title );
		$this->assertSame( 'papi_number_test', $property->slug );

		$taxonomies_type = papi_get_entry_type_by_id( 'properties-taxonomy-type' );
		$property  = $taxonomies_type->get_property( 'string_test' );

		$this->assertSame( 'string', $property->get_option( 'type' ) );
		$this->assertSame( 'String test', $property->get_option( 'title' ) );
		$this->assertSame( 'papi_string_test', $property->get_option( 'slug' ) );

		$_POST = papi_test_create_property_post_data( [
			'slug'  => $property->get_option( 'slug' ),
			'type'  => $property,
			'value' => 'Fredrik'
		], $_POST );

		$handler->save_properties( $this->term_id );

		$actual = papi_get_term_field( $this->term_id, $property->get_option( 'slug' ) );
		$this->assertSame( 'Fredrik', $actual );

		update_term_meta( $this->term_id, papi_get_page_type_key(), 'properties-taxonomy-type' );
		$this->assertSame( 'Fredrik', papi_get_term_field( $this->term_id, 'string_test' ) );
	}

	public function test__get() {
		update_term_meta( $this->term_id, 'name', '' );

		$this->assertNull( $this->store->name );
	}

	public function test_get_property() {
		$store = papi_get_meta_store( $this->term_id, 'term' );
		$this->assertNull( $store->get_property( 'string_test' ) );
	}

	public function test_valid() {
		$this->assertTrue( $this->store->valid() );
	}
}
