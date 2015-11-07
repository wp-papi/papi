<?php

class Papi_Lib_Filters_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->post_id = $this->factory->post->create();
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id );
	}

	public function test_papi_filter_format_value() {
		$this->assertSame( 'hello', papi_filter_format_value( 'string', 'hello', 'slug', 1 ) );

		tests_add_filter( 'papi/format_value/string', function () {
			return 'change-format';
		} );

		$this->assertSame( 'change-format', papi_filter_format_value( 'string', 'hello', 'slug', 1 ) );
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
		$this->assertSame( 'hello', papi_filter_load_value( 'string', 'hello', 'slug', 1 ) );

		tests_add_filter( 'papi/load_value/string', function () {
			return 'change-load';
		} );

		$this->assertSame( 'change-load', papi_filter_load_value( 'string', 'hello', 'slug', 1 ) );
	}

	public function test_papi_filter_settings_page_type_column_title() {
		$this->assertSame( 'Type', papi_filter_settings_page_type_column_title( 'page' ) );

		add_filter( 'papi/settings/column_title_page', function () {
			return 'Typ';
		} );

		$this->assertSame( 'Typ', papi_filter_settings_page_type_column_title( 'page' ) );
	}

	public function test_papi_filter_settings_default_sort_order() {
		$this->assertSame( 1000, papi_filter_settings_sort_order() );

		tests_add_filter( 'papi/settings/sort_order', function () {
			return 1;
		} );

		$this->assertSame( 1, papi_filter_settings_sort_order() );
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
		$this->assertSame( 'path', $directories[0] );

		tests_add_filter( 'papi/settings/directories', function () {
			return null;
		} );

		$this->assertEmpty( papi_filter_settings_directories() );
	}

	public function test_papi_filter_settings_only_page_type() {
		$this->assertSame( '', papi_filter_settings_only_page_type( 'post' ) );

		tests_add_filter( 'papi/settings/only_page_type_post', function () {
			return 'simple-page-type';
		} );

		$this->assertSame( 'simple-page-type', papi_filter_settings_only_page_type( 'post' ) );

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
		$this->assertSame( 'Post with WordPress standard fields', papi_filter_settings_standard_page_description( 'post' ) );
		$this->assertSame( 'Page with WordPress standard fields', papi_filter_settings_standard_page_description( 'fake' ) );
		$this->assertSame( 'Page with WordPress standard fields', papi_filter_settings_standard_page_description( 'Page' ) );

		tests_add_filter( 'papi/settings/standard_page_description_post', function () {
			return 'Hello, world!';
		} );

		$this->assertSame( 'Hello, world!', papi_filter_settings_standard_page_description( 'post' ) );
	}

	public function test_papi_filter_settings_standard_page_name() {
		$this->assertSame( 'Standard Post', papi_filter_settings_standard_page_name( 'post' ) );
		$this->assertSame( 'Standard Page', papi_filter_settings_standard_page_name( 'fake' ) );
		$this->assertSame( 'Standard Page', papi_filter_settings_standard_page_name( 'page' ) );

		tests_add_filter( 'papi/settings/standard_page_name_post', function () {
			return 'Hello, world!';
		} );

		$this->assertSame( 'Hello, world!', papi_filter_settings_standard_page_name( 'post' ) );
	}

	public function test_papi_filter_settings_show_standard_page_type() {
		$this->assertSame( false, papi_filter_settings_show_standard_page_type( 'post' ) );

		tests_add_filter( 'papi/settings/show_standard_page_type_post', '__return_true' );

		$this->assertSame( true, papi_filter_settings_show_standard_page_type( 'post' ) );
	}

	public function test_papi_filter_settings_show_standard_page_type_in_filter() {
		$this->assertSame( false, papi_filter_settings_show_standard_page_type_in_filter( 'post' ) );

		tests_add_filter( 'papi/settings/show_standard_page_type_in_filter_post', '__return_true' );

		$this->assertSame( true, papi_filter_settings_show_standard_page_type_in_filter( 'post' ) );
	}

	public function test_papi_filter_settings_standard_page_thumbnail() {
		$this->assertSame( '', papi_filter_settings_standard_page_thumbnail( 'post' ) );

		tests_add_filter( 'papi/settings/standard_page_thumbnail_post', function () {
			return 'Hello, world!';
		} );

		$this->assertSame( 'Hello, world!', papi_filter_settings_standard_page_thumbnail( 'post' ) );
	}

	public function test_papi_filter_update_value() {
		$this->assertSame( 'hello', papi_filter_update_value( 'string', 'hello', 'slug', 1 ) );

		tests_add_filter( 'papi/update_value/string', function () {
			return 'change-update';
		} );

		$this->assertSame( 'change-update', papi_filter_update_value( 'string', 'hello', 'slug', 1 ) );
	}
}
