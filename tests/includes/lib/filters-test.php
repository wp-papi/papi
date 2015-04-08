<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering filters functions.
 *
 * @package Papi
 */

class Papi_Lib_Filters_Test extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 *
	 * @since 1.0.0
	 */

	public function setUp() {
		parent::setUp();
		$this->post_id = $this->factory->post->create();
	}

	/**
	 * Tear down test.
	 *
	 * @since 1.3.0
	 */

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id );
	}

	/**
	 * Test `papi_filter_default_sort_order` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_filter_default_sort_order() {
		$this->assertEquals( 1000, papi_filter_settings_sort_order() );

		tests_add_filter( 'papi/settings/sort_order', function () {
			return 1;
		} );

		$this->assertEquals( 1, papi_filter_settings_sort_order() );
	}

	/**
	 * Test `papi_filter_format_value` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_filter_format_value() {
		$this->assertEquals( 'hello', papi_filter_format_value( 'string', 'hello', 'slug', 1 ) );

		tests_add_filter( 'papi/format_value/string', function () {
			return 'change-format';
		} );

		$this->assertEquals( 'change-format', papi_filter_format_value( 'string', 'hello', 'slug', 1 ) );
	}

	/**
	 * Test `papi_filter_format_value` function with a property.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_filter_format_value_property() {
		tests_add_filter( 'papi/format_value/string', function () {
			return 'change-format';
		} );

		$slug = 'heading';
		add_post_meta( $this->post_id, $slug, 'papi' );

		$slug_type = papi_f( papi_get_property_type_key( $slug ) );
		add_post_meta( $this->post_id, $slug_type, 'string' );

		$heading = papi_field( $this->post_id, $slug );
		$this->assertEquals( 'change-format', $heading );

		$heading_property = get_post_meta( $this->post_id, $slug_type, true );
		$this->assertEquals( $heading_property, 'string' );
	}

	/**
	 * Test `papi_filter_load_value` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_filter_load_value() {
		$this->assertEquals( 'hello', papi_filter_load_value( 'string', 'hello', 'slug', 1 ) );

		tests_add_filter( 'papi/load_value/string', function () {
			return 'change-load';
		} );

		$this->assertEquals( 'change-load', papi_filter_load_value( 'string', 'hello', 'slug', 1 ) );
	}

	/**
	 * Test `papi_filter_settings_page_type_from_post_qs` function.
	 *
	 * @since 1.2.0
	 */

	public function test_papi_filter_page_type_from_post_qs() {
		$this->assertEquals( 'from_post', papi_filter_settings_page_type_from_post_qs() );

		tests_add_filter( 'papi/settings/page_type_from_post_qs', function () {
			return 'parent_post';
		} );

		$this->assertEquals( 'parent_post', papi_filter_settings_page_type_from_post_qs() );
	}

	/**
	 * Test `papi_filter_only_page_type` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_filter_only_page_type() {
		$this->assertEquals( '', papi_filter_settings_only_page_type( 'post' ) );

		tests_add_filter( 'papi/settings/only_page_type_post', function () {
			return 'simple-page-type';
		} );

		$this->assertEquals( 'simple-page-type', papi_filter_settings_only_page_type( 'post' ) );

		tests_add_filter( 'papi/settings/only_page_type_post', function () {
			return false;
		} );

		$this->assertEmpty( papi_filter_settings_only_page_type( 'post' ) );
	}

	/**
	 * Test `papi_filter_show_page_type` function.
	 *
	 * @since 1.2.0
	 */

	public function test_papi_filter_show_page_type() {
		$this->assertTrue( papi_filter_show_page_type( 'post', 'test-page-type' ) );

		tests_add_filter( 'papi/settings/standard_page_type_post', function ( $page_type ) {
			if ( $page_type == 'test-page-type' ) {
				return false;
			}

			return true;
		} );

		$this->assertFalse( !papi_filter_show_page_type( 'post', 'test-page-type' ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return array( 1,  papi_test_get_fixtures_path( '/page-types' ) );
		} );

		$page_type = papi_get_page_type_by_id( 'simple-page-type' );
		$this->assertTrue( papi_filter_show_page_type( 'post', $page_type ) );
	}

	/**
	 * Test `papi_filter_standard_page_description` function.
	 *
	 * @since 1.2.0
	 */

	public function test_papi_filter_standard_page_description() {
		$this->assertEquals( 'Just the normal WordPress page', papi_filter_standard_page_description( 'post' ) );

		tests_add_filter( 'papi/settings/standard_page_description_post', function () {
			return 'Hello, world!';
		} );

		$this->assertEquals( 'Hello, world!', papi_filter_standard_page_description( 'post' ) );
	}

	/**
	 * Test `papi_filter_standard_page_name` function.
	 *
	 * @since 1.2.0
	 */

	public function test_papi_filter_standard_page_name() {
		$this->assertEquals( 'Standard Page', papi_filter_standard_page_name( 'post' ) );

		tests_add_filter( 'papi/settings/standard_page_name_post', function () {
			return 'Hello, world!';
		} );

		$this->assertEquals( 'Hello, world!', papi_filter_standard_page_name( 'post' ) );
	}

	/**
	 * Test `papi_filter_show_standard_page_type_for` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_filter_show_standard_page_type_for() {
		$this->assertEquals( true, papi_filter_settings_standard_page_type( 'post' ) );

		tests_add_filter( 'papi/settings/standard_page_type_post', '__return_false' );

		$this->assertEquals( false, papi_filter_settings_standard_page_type( 'post' ) );
	}

	/**
	 * Test `papi_filter_standard_page_thumbnail` function.
	 *
	 * @since 1.2.0
	 */

	public function test_papi_filter_standard_page_thumbnail() {
		$this->assertEquals( '', papi_filter_standard_page_thumbnail( 'post' ) );

		tests_add_filter( 'papi/settings/standard_page_thumbnail_post', function () {
			return 'Hello, world!';
		} );

		$this->assertEquals( 'Hello, world!', papi_filter_standard_page_thumbnail( 'post' ) );
	}

	/**
	 * Test `papi_filter_page_type_directories` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_filter_page_type_directories() {
		tests_add_filter( 'papi/settings/directories', function () {
			return array();
		} );

		$this->assertEmpty( papi_filter_settings_directories() );

		tests_add_filter( 'papi/settings/directories', function () {
			return 'path';
		} );

		$directories = papi_filter_settings_directories();
		$this->assertEquals( 'path', $directories[0] );

		tests_add_filter( 'papi/settings/directories', function () {
			return null;
		} );

		$this->assertEmpty( papi_filter_settings_directories() );
	}

	/**
	 * Test `papi_filter_update_value` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_filter_update_value() {
		$this->assertEquals( 'hello', papi_filter_update_value( 'string', 'hello', 'slug', 1 ) );

		tests_add_filter( 'papi/update_value/string', function () {
			return 'change-update';
		} );

		$this->assertEquals( 'change-update', papi_filter_update_value( 'string', 'hello', 'slug', 1 ) );
	}

}
