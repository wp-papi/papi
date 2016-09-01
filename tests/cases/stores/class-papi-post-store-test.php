<?php

class Papi_Post_Store_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->post_id = $this->factory->post->create();
		$_GET['post'] = $this->post_id;

		$this->store = papi_get_meta_store( $this->post_id );
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id, $_GET['post'], $this->store );
	}

	public function test_post_store_construct() {
		$this->assertTrue( ( new Papi_Post_Store )->valid() );
		$this->assertTrue( ( new Papi_Post_Store( $this->post_id ) )->valid() );
	}

	public function test_get_type_class() {
		$this->assertEmpty( $this->store->get_type_class() );

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'simple-page-type' );

		$store = papi_get_meta_store( $this->post_id );

		$this->assertSame( $store->get_type_class()->name, 'Simple page' );
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

	public function test_load_value() {
		update_post_meta( $this->post_id, papi_get_page_type_key(), 'simple-page-type' );

		update_post_meta( $this->post_id, 'name', 'Janni' );
		$this->assertSame( 'Janni', $this->store->load_value( 'name' ) );

		update_post_meta( $this->post_id, 'name', 'Fredrik' );

		$this->assertSame( 'Fredrik', $this->store->load_value( 'name' ) );
	}

	public function test_format_value() {
		$handler = new Papi_Admin_Meta_Handler();
		update_post_meta( $this->post_id, papi_get_page_type_key(), 'simple-page-type' );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$page_type = papi_get_entry_type_by_id( 'simple-page-type' );
		$property  = $page_type->get_property( 'name' );

		$_POST = papi_test_create_property_post_data( [
			'slug'  => $property->get_option( 'slug' ),
			'type'  => $property,
			'value' => 'Fredrik'
		], $_POST );

		$handler->save_properties( $this->post_id );

		$value = $this->store->load_value( $property->get_option( 'slug' ) );
		$value = $this->store->format_value( $property->get_option( 'slug' ), $value );

		$this->assertSame( 'Fredrik', $value );
	}

	public function test_post_store_editor_value() {
		$handler = new Papi_Admin_Meta_Handler();
		update_post_meta( $this->post_id, papi_get_page_type_key(), 'editor-page-type' );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$page_type = papi_get_entry_type_by_id( 'editor-page-type' );
		$property  = $page_type->get_property( 'editor' );

		$_POST = papi_test_create_property_post_data( [
			'slug'  => $property->get_option( 'slug' ),
			'type'  => $property,
			'value' => '[embed width="123" height="456"]http://wordpress.org[/embed]'
		], $_POST );

		$handler->save_properties( $this->post_id );

		$value = $this->store->load_value( $property->get_option( 'slug' ) );
		$this->assertSame( '[embed width="123" height="456"]http://wordpress.org[/embed]', $value );

		$value = $this->store->format_value( $property->get_option( 'slug' ), $value );
		$this->assertSame( '<p><a href="http://wordpress.org">http://wordpress.org</a></p>', trim( $value ) );
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
