<?php

/**
 * @group hooks
 */
class Papi_Lib_Filters_Page_Type_Test extends WP_UnitTestCase {

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

		$page_type = papi_get_entry_type_by_id( 'simple-page-type' );
		$this->assertTrue( papi_filter_settings_show_page_type( 'post', $page_type ) );

		tests_add_filter( 'papi/settings/show_page_type_post', function ( $page_type ) {
			return 'no';
		} );

		$page_type = papi_get_entry_type_by_id( 'simple-page-type' );
		$this->assertFalse( papi_filter_settings_show_page_type( 'post', $page_type ) );
	}

	public function test_papi_filter_settings_standard_page_type_description() {
		$this->assertSame( 'Post with WordPress standard fields', papi_filter_settings_standard_page_type_description( 'post' ) );
		$this->assertSame( 'Page with WordPress standard fields', papi_filter_settings_standard_page_type_description( 'fake' ) );
		$this->assertSame( 'Page with WordPress standard fields', papi_filter_settings_standard_page_type_description( 'Page' ) );

		tests_add_filter( 'papi/settings/standard_page_type_description_post', function () {
			return 'Hello, world!';
		} );

		$this->assertSame( 'Hello, world!', papi_filter_settings_standard_page_type_description( 'post' ) );

		// Old filters, should work until Papi 4.0.0.
		tests_add_filter( 'papi/settings/standard_page_description_post', function () {
			return 'Hello, world!';
		} );

		$this->assertSame( 'Hello, world!', papi_filter_settings_standard_page_type_description( 'post' ) );
	}

	public function test_papi_filter_settings_standard_page_type_name() {
		$this->assertSame( 'Standard Post', papi_filter_settings_standard_page_type_name( 'post' ) );
		$this->assertSame( 'Standard Page', papi_filter_settings_standard_page_type_name( 'fake' ) );
		$this->assertSame( 'Standard Page', papi_filter_settings_standard_page_type_name( 'page' ) );

		tests_add_filter( 'papi/settings/standard_page_type_name_post', function () {
			return 'Hello, world!';
		} );

		$this->assertSame( 'Hello, world!', papi_filter_settings_standard_page_type_name( 'post' ) );

		// Old filters, should work until Papi 4.0.0.
		tests_add_filter( 'papi/settings/standard_page_type_post', function () {
			return 'Hello, world!';
		} );

		$this->assertSame( 'Hello, world!', papi_filter_settings_standard_page_type_name( 'post' ) );
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

	public function test_papi_filter_settings_standard_page_type_thumbnail() {
		$this->assertSame( '', papi_filter_settings_standard_page_type_thumbnail( 'post' ) );

		tests_add_filter( 'papi/settings/standard_page_type_thumbnail_post', function () {
			return 'Hello, world!';
		} );

		$this->assertSame( 'Hello, world!', papi_filter_settings_standard_page_type_thumbnail( 'post' ) );

		// Old filters, should work until Papi 4.0.0.
		tests_add_filter( 'papi/settings/standard_page_thumbnail_post', function () {
			return 'Hello, world!';
		} );

		$this->assertSame( 'Hello, world!', papi_filter_settings_standard_page_type_thumbnail( 'post' ) );
	}
}
