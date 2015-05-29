<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Post_Page` class.
 *
 * @package Papi
 */

class Papi_Post_Page_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->post_id = $this->factory->post->create();

		$this->page = papi_get_page( $this->post_id );
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id, $this->page );
	}

	public function test_get_page_type() {
		$this->assertEmpty( $this->page->get_page_type() );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );

		$page = papi_get_page( $this->post_id );

		$this->assertEquals( $page->get_page_type()->name, 'Simple page' );
	}

	public function test_get_permalink() {
		$permalink = $this->page->get_permalink();
		$this->assertFalse( empty( $permalink ) );
	}

	public function test_get_post() {
		$this->assertTrue( is_object( $this->page->get_post() ) );
		$this->assertEquals( $this->post_id, $this->page->get_post()->ID );
	}

	public function test_get_status() {
		$this->assertEquals( 'publish', $this->page->get_status() );
	}

	public function test_get_value() {
		$handler = new Papi_Admin_Post_Handler();

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );

		update_post_meta( $this->post_id, 'name', 'Janni' );
		$this->assertEquals( 'Janni', $this->page->get_value( 'name' ) );

		update_post_meta( $this->post_id, 'name', 'Fredrik' );

		$this->assertEquals( 'Fredrik', $this->page->get_value( 'name' ) );

		$property = papi_property( [
			'type'  => 'number',
			'title' => 'Nummer',
			'slug'  => 'nummer'
		] );

		$this->assertEquals( 'number', $property->type );
		$this->assertEquals( 'Nummer', $property->title );
		$this->assertEquals( 'papi_nummer', $property->slug );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$page_type = papi_get_page_type_by_id( 'simple-page-type' );
		$property  = $page_type->get_property( 'name' );

		$this->assertEquals( 'string', $property->get_option( 'type' ) );
		$this->assertEquals( 'Name', $property->get_option( 'title' ) );
		$this->assertEquals( 'papi_name', $property->get_option( 'slug' ) );

		$_POST = papi_test_create_property_post_data( [
			'slug'  => $property->get_option( 'slug' ),
			'type'  => $property,
			'value' => 'Fredrik'
		], $_POST );

		$handler->save_property( $this->post_id );

		$actual = papi_field( $this->post_id, $property->get_option( 'slug' ) );
		$this->assertEquals( 'Fredrik', $actual );
	}

	public function test__get() {
		update_post_meta( $this->post_id, 'name', '' );

		$this->assertNull( $this->page->name );
	}

	public function test_get_property() {
		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'random322-page-type' );
		$data_page = papi_get_page( $this->post_id );
		$this->assertNull( $data_page->get_property( 'fake' ) );
	}

	public function test_valid() {
		$this->assertTrue( $this->page->valid() );
	}
}
