<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering filters functions.
 *
 * @package Papi
 */
class Papi_Lib_Filters_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->post_id = $this->factory->post->create();
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id );
	}

	public function test_papi_filter_core_load_one_type_on() {
		$this->assertEquals( ['attachment'], papi_filter_core_load_one_type_on() );

		tests_add_filter( 'papi/core/load_one_type_on', function () {
			return ['page', 'post'];
		} );

		$this->assertEquals( ['page', 'post'], papi_filter_core_load_one_type_on() );

		tests_add_filter( 'papi/core/load_one_type_on', function () {
			return false;
		} );

		$this->assertEquals( ['attachment'], papi_filter_core_load_one_type_on() );
	}

	public function test_papi_filter_format_value() {
		$this->assertEquals( 'hello', papi_filter_format_value( 'string', 'hello', 'slug', 1 ) );

		tests_add_filter( 'papi/format_value/string', function () {
			return 'change-format';
		} );

		$this->assertEquals( 'change-format', papi_filter_format_value( 'string', 'hello', 'slug', 1 ) );
	}

	public function test_papi_filter_conditional_rule_allowed() {
		$this->assertFalse( papi_filter_conditional_rule_allowed( true ) );
		$this->assertFalse( papi_filter_conditional_rule_allowed( false ) );
		$this->assertFalse( papi_filter_conditional_rule_allowed( null ) );
		$this->assertFalse( papi_filter_conditional_rule_allowed( 1 ) );
		$this->assertFalse( papi_filter_conditional_rule_allowed( '' ) );
		$this->assertFalse( papi_filter_conditional_rule_allowed( (object) [] ) );
		$this->assertTrue( papi_filter_conditional_rule_allowed( new Papi_Core_Conditional_Rule( [
			'operator' => '=',
			'slug'     => 'fake',
			'source'   => 'hello',
			'value'    => 'hello'
		] ) ) );
		$this->assertTrue( papi_filter_conditional_rule_allowed( [
			'operator' => '=',
			'slug'     => 'fake',
			'source'   => 'hello',
			'value'    => 'hello'
		] ) );
	}

	public function test_papi_filter_load_value() {
		$this->assertEquals( 'hello', papi_filter_load_value( 'string', 'hello', 'slug', 1 ) );

		tests_add_filter( 'papi/load_value/string', function () {
			return 'change-load';
		} );

		$this->assertEquals( 'change-load', papi_filter_load_value( 'string', 'hello', 'slug', 1 ) );
	}

	public function test_papi_filter_settings_page_type_column_title() {
		$this->assertEquals( 'Type', papi_filter_settings_page_type_column_title( 'page' ) );

		add_filter( 'papi/settings/column_title_page', function () {
			return 'Typ';
		} );

		$this->assertEquals( 'Typ', papi_filter_settings_page_type_column_title( 'page' ) );
	}

	public function test_papi_filter_settings_default_sort_order() {
		$this->assertEquals( 1000, papi_filter_settings_sort_order() );

		tests_add_filter( 'papi/settings/sort_order', function () {
			return 1;
		} );

		$this->assertEquals( 1, papi_filter_settings_sort_order() );
	}

	public function test_papi_filter_page_type_directories() {
		tests_add_filter( 'papi/settings/directories', function () {
			return [];
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

	public function test_papi_filter_settings_only_page_type() {
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

	public function test_papi_filter_settings_show_page_type() {
		$this->assertTrue( papi_filter_settings_show_page_type( 'post', 'test-page-type' ) );

		tests_add_filter( 'papi/settings/show_page_type_post', function ( $page_type ) {
			if ( $page_type == 'test-page-type' ) {
				return false;
			}

			return true;
		} );

		$this->assertFalse( papi_filter_settings_show_page_type( 'post', 'test-page-type' ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$page_type = papi_get_page_type_by_id( 'simple-page-type' );
		$this->assertTrue( papi_filter_settings_show_page_type( 'post', $page_type ) );

		tests_add_filter( 'papi/settings/show_page_type_post', function ( $page_type ) {
			return 'no';
		} );

		$page_type = papi_get_page_type_by_id( 'simple-page-type' );
		$this->assertFalse( papi_filter_settings_show_page_type( 'post', $page_type ) );
	}

	public function test_papi_filter_settings_standard_page_description() {
		$this->assertEquals( 'Post with WordPress standard fields', papi_filter_settings_standard_page_description( 'post' ) );
		$this->assertEquals( 'Page with WordPress standard fields', papi_filter_settings_standard_page_description( 'fake' ) );
		$this->assertEquals( 'Page with WordPress standard fields', papi_filter_settings_standard_page_description( 'Page' ) );

		tests_add_filter( 'papi/settings/standard_page_description_post', function () {
			return 'Hello, world!';
		} );

		$this->assertEquals( 'Hello, world!', papi_filter_settings_standard_page_description( 'post' ) );
	}

	public function test_papi_filter_settings_standard_page_name() {
		$this->assertEquals( 'Standard Post', papi_filter_settings_standard_page_name( 'post' ) );
		$this->assertEquals( 'Standard Page', papi_filter_settings_standard_page_name( 'fake' ) );
		$this->assertEquals( 'Standard Page', papi_filter_settings_standard_page_name( 'page' ) );

		tests_add_filter( 'papi/settings/standard_page_name_post', function () {
			return 'Hello, world!';
		} );

		$this->assertEquals( 'Hello, world!', papi_filter_settings_standard_page_name( 'post' ) );
	}

	public function test_papi_filter_settings_show_standard_page_type() {
		$this->assertEquals( false, papi_filter_settings_show_standard_page_type( 'post' ) );

		tests_add_filter( 'papi/settings/show_standard_page_type_post', '__return_true' );

		$this->assertEquals( true, papi_filter_settings_show_standard_page_type( 'post' ) );
	}

	public function test_papi_filter_settings_show_standard_page_type_in_filter() {
		$this->assertEquals( false, papi_filter_settings_show_standard_page_type_in_filter( 'post' ) );

		tests_add_filter( 'papi/settings/show_standard_page_type_in_filter_post', '__return_true' );

		$this->assertEquals( true, papi_filter_settings_show_standard_page_type_in_filter( 'post' ) );
	}

	public function test_papi_filter_settings_standard_page_thumbnail() {
		$this->assertEquals( '', papi_filter_settings_standard_page_thumbnail( 'post' ) );

		tests_add_filter( 'papi/settings/standard_page_thumbnail_post', function () {
			return 'Hello, world!';
		} );

		$this->assertEquals( 'Hello, world!', papi_filter_settings_standard_page_thumbnail( 'post' ) );
	}

	public function test_papi_filter_update_value() {
		$this->assertEquals( 'hello', papi_filter_update_value( 'string', 'hello', 'slug', 1 ) );

		tests_add_filter( 'papi/update_value/string', function () {
			return 'change-update';
		} );

		$this->assertEquals( 'change-update', papi_filter_update_value( 'string', 'hello', 'slug', 1 ) );
	}
}
