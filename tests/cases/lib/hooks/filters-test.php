<?php

/**
 * @group hooks
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

	public function test_papi_filter_update_value() {
		$this->assertSame( 'hello', papi_filter_update_value( 'string', 'hello', 'slug', 1 ) );

		tests_add_filter( 'papi/update_value/string', function () {
			return 'change-update';
		} );

		$this->assertSame( 'change-update', papi_filter_update_value( 'string', 'hello', 'slug', 1 ) );
	}
}
