<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Unit tests covering filters functionality.
 *
 * @package Papi
 */

class WP_Papi_Functions_Filters extends WP_UnitTestCase {

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
	 * Test _papi_filter_default_sort_order.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_filter_default_sort_order() {
		$this->assertEquals( 1000, _papi_filter_settings_sort_order() );

		tests_add_filter( 'papi/settings/sort_order', function () {
			return 1;
		} );

		$this->assertEquals( 1, _papi_filter_settings_sort_order() );
	}

	/**
	 * Test _papi_filter_format_value.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_filter_format_value() {
		$this->assertEquals( 'hello', _papi_filter_format_value( 'string', 'hello', 'slug', 1 ) );

		tests_add_filter( 'papi/format_value/string', function () {
			return 'change-format';
		} );

		$this->assertEquals( 'change-format', _papi_filter_format_value( 'string', 'hello', 'slug', 1 ) );
	}

	/**
	 * Test _papi_filter_format_value with a property.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_filter_format_value_property() {
		tests_add_filter( 'papi/format_value/string', function () {
			return 'change-format';
		} );

		$slug = 'heading';
		add_post_meta( $this->post_id, $slug, 'papi' );

		$slug_type = _papi_f( _papi_get_property_type_key( $slug ) );
		add_post_meta( $this->post_id, $slug_type, 'string' );

		$heading = papi_field( $this->post_id, $slug );
		$this->assertEquals( 'change-format', $heading );

		$heading_property = get_post_meta( $this->post_id, $slug_type, true );
		$this->assertEquals( $heading_property, 'string' );
	}

	/**
	 * Test _papi_filter_load_value.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_filter_load_value() {
		$this->assertEquals( 'hello', _papi_filter_load_value( 'string', 'hello', 'slug', 1 ) );

		tests_add_filter( 'papi/load_value/string', function () {
			return 'change-load';
		} );

		$this->assertEquals( 'change-load', _papi_filter_load_value( 'string', 'hello', 'slug', 1 ) );
	}

	/**
	 * Test _papi_filter_only_page_type.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_filter_only_page_type() {
		$this->assertEquals( '', _papi_filter_settings_only_page_type( 'post' ) );

		tests_add_filter( 'papi/settings/only_page_type_post', function () {
			return 'simple-page-type';
		} );

		$this->assertEquals( 'simple-page-type', _papi_filter_settings_only_page_type( 'post' ) );
	}

	/**
	 * Test _papi_filter_show_standard_page_type_for.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_filter_show_standard_page_type_for() {
		$this->assertEquals( true, _papi_filter_settings_standard_page_type( 'post' ) );

		tests_add_filter( 'papi/settings/standard_page_type_post', '__return_false' );

		$this->assertEquals( false, _papi_filter_settings_standard_page_type( 'post' ) );
	}

	/**
	 * Test _papi_filter_page_type_directories.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_filter_page_type_directories() {
		tests_add_filter( 'papi/settings/directories', function () {
			return array();
		} );

		$this->assertEmpty( _papi_filter_settings_directories() );
	}

	/**
	 * Test _papi_filter_update_value.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_filter_update_value() {
		$this->assertEquals( 'hello', _papi_filter_update_value( 'string', 'hello', 'slug', 1 ) );

		tests_add_filter( 'papi/update_value/string', function () {
			return 'change-update';
		} );

		$this->assertEquals( 'change-update', _papi_filter_update_value( 'string', 'hello', 'slug', 1 ) );
	}

}
