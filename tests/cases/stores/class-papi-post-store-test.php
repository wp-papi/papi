<?php

class Papi_Post_Store_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->post_id = $this->factory->post->create();

		$this->store = papi_get_meta_store( $this->post_id );
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id, $this->store );
	}

	public function test_get_page_type() {
		$this->assertEmpty( $this->store->get_page_type() );

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'simple-page-type' );

		$store = papi_get_meta_store( $this->post_id );

		$this->assertSame( $store->get_page_type()->name, 'Simple page' );
	}

	public function test_get_permalink() {
		$permalink = $this->store->get_permalink();
		$this->assertFalse( empty( $permalink ) );
	}

	public function test_get_post() {
		$this->assertTrue( is_object( $this->store->get_post() ) );
		$this->assertSame( $this->post_id, $this->store->get_post()->ID );
	}

	public function test_get_status() {
		$this->assertSame( 'publish', $this->store->get_status() );
	}

	public function test_get_value() {
		$handler = new Papi_Admin_Meta_Handler();

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'simple-page-type' );

		update_post_meta( $this->post_id, 'name', 'Janni' );
		$this->assertSame( 'Janni', $this->store->get_value( 'name' ) );

		update_post_meta( $this->post_id, 'name', 'Fredrik' );

		$this->assertSame( 'Fredrik', $this->store->get_value( 'name' ) );

		$property = papi_property( [
			'type'  => 'number',
			'title' => 'Nummer',
			'slug'  => 'nummer'
		] );

		$this->assertSame( 'number', $property->type );
		$this->assertSame( 'Nummer', $property->title );
		$this->assertSame( 'papi_nummer', $property->slug );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$page_type = papi_get_page_type_by_id( 'simple-page-type' );
		$property  = $page_type->get_property( 'name' );

		$this->assertSame( 'string', $property->get_option( 'type' ) );
		$this->assertSame( 'Name', $property->get_option( 'title' ) );
		$this->assertSame( 'papi_name', $property->get_option( 'slug' ) );

		$_POST = papi_test_create_property_post_data( [
			'slug'  => $property->get_option( 'slug' ),
			'type'  => $property,
			'value' => 'Fredrik'
		], $_POST );

		$handler->save_properties( $this->post_id );

		$actual = papi_get_field( $this->post_id, $property->get_option( 'slug' ) );
		$this->assertSame( 'Fredrik', $actual );

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'simple-page-type' );
		$this->assertSame( 'Fredrik', papi_get_field( $this->post_id, 'name_default' ) );
	}

	public function test__get() {
		update_post_meta( $this->post_id, 'name', '' );

		$this->assertNull( $this->store->name );
	}

	public function test_get_property() {
		update_post_meta( $this->post_id, papi_get_page_type_key(), 'random322-page-type' );
		$store = papi_get_meta_store( $this->post_id );
		$this->assertNull( $store->get_property( 'fake' ) );
	}

	public function test_valid() {
		$this->assertTrue( $this->store->valid() );
	}
}
