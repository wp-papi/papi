<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Page` class.
 *
 * @package Papi
 */

class Papi_Page_Test extends WP_UnitTestCase {

	/**
	 * Setup test.
	 *
	 * @since 1.3.0
	 */

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return [ 1, papi_test_get_fixtures_path( '/page-types' ) ];
		} );

		$this->post_id = $this->factory->post->create();

		$this->page = papi_get_page( $this->post_id );
	}

	/**
	 * Tear down property.
	 *
	 * @since 1.3.0
	 */

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id, $this->page );
	}

	/**
	 * Test `get_page_type` method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_page_type() {
		$this->assertEmpty( $this->page->get_page_type() );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );

		$page = papi_get_page( $this->post_id );

		$this->assertEquals( $page->get_page_type()->name, 'Simple page' );
	}

	/**
	 * Test `get_permalink` method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_permalink() {
		$permalink = $this->page->get_permalink();
		$this->assertFalse( empty( $permalink ) );
	}

	/**
	 * Test `get_post` method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_post() {
		$this->assertTrue( is_object( $this->page->get_post() ) );
		$this->assertEquals( $this->post_id, $this->page->get_post()->ID );
	}

	/**
	 * Test `get_status` method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_status() {
		$this->assertEquals( 'publish', $this->page->get_status() );
	}

	/**
	 * Test `get_value` method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_value() {
		$handler = new Papi_Admin_Meta_Boxes();

		update_post_meta( $this->post_id, 'name', 'Janni' );
		update_post_meta( $this->post_id, papi_f( papi_get_property_type_key( 'name' ) ), 'name' );
		$this->assertEquals( 'Janni', $this->page->get_value( 'name' ) );

		update_post_meta( $this->post_id, 'name', 'Fredrik' );
		update_post_meta( $this->post_id, papi_f( papi_get_property_type_key( 'name' ) ), 'string' );

		$this->assertEquals( 'Fredrik', $this->page->get_value( 'name' ) );

		define( 'WP_ADMIN', true );

		$this->assertEquals( 'Fredrik', $this->page->get_value( 'name' ) );

		$property = papi_property( [
			'type'  => 'number',
			'title' => 'Nummer',
			'slug'  => 'nummer'
		] );

		$this->assertEquals( 'number', $property->type );
		$this->assertEquals( 'Nummer', $property->title );
		$this->assertEquals( 'papi_nummer', $property->slug );

		$_POST = papi_test_create_property_post_data( [
			'slug'  => $property->slug,
			'type'  => $property,
			'value' => 42
		], $_POST );

		$handler->save_property( $this->post_id );

		$actual = papi_field( $this->post_id, $property->slug );
		$this->assertEquals( 42, $actual );

		tests_add_filter( 'papi/settings/directories', function () {
			return [ 1,  papi_test_get_fixtures_path( '/page-types' ) ];
		} );

		$page_type = papi_get_page_type_by_id( 'simple-page-type' );
		$property  = $page_type->get_property( 'name' );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );

		$this->assertEquals( 'string', $property->type );
		$this->assertEquals( 'Name', $property->title );
		$this->assertEquals( 'papi_name', $property->slug );

		$_POST = papi_test_create_property_post_data( [
			'slug'  => $property->slug,
			'type'  => $property,
			'value' => 'Fredrik'
		], $_POST );

		$handler->save_property( $this->post_id );

		$actual = papi_field( $this->post_id, $property->slug );
		$this->assertEquals( 'Fredrik', $actual );
	}

	/**
	 * Test `__get` method.
	 *
	 * @since 1.3.0
	 */

	public function test__get() {
		update_post_meta( $this->post_id, 'name', '' );

		$this->assertNull( $this->page->name );
	}
}
