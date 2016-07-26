<?php

/**
 * @group hooks
 */
class Papi_Lib_Filters_Taxonomy_Type_Test extends WP_UnitTestCase {

	public function test_papi_filter_settings_only_taxonomy_type() {
		$this->assertSame( '', papi_filter_settings_only_taxonomy_type( 'category' ) );

		tests_add_filter( 'papi/settings/only_taxonomy_type_category', function () {
			return 'simple-taxonomy-type';
		} );

		$this->assertSame( 'simple-taxonomy-type', papi_filter_settings_only_taxonomy_type( 'category' ) );

		tests_add_filter( 'papi/settings/only_taxonomy_type_category', function () {
			return false;
		} );

		$this->assertEmpty( papi_filter_settings_only_taxonomy_type( 'category' ) );
	}

	public function test_papi_filter_settings_standard_taxonomy_type_name() {
		$this->assertSame( 'Standard Category', papi_filter_settings_standard_taxonomy_type_name( 'category' ) );
		$this->assertSame( 'Standard Taxonomy', papi_filter_settings_standard_taxonomy_type_name( 'fake' ) );

		tests_add_filter( 'papi/settings/standard_taxonomy_name_post', function () {
			return 'Hello, world!';
		} );

		$this->assertSame( 'Hello, world!', papi_filter_settings_standard_taxonomy_type_name( 'post' ) );
	}

	public function test_papi_filter_settings_show_standard_taxonomy_type() {
		$this->assertSame( false, papi_filter_settings_show_standard_taxonomy_type( 'post' ) );

		tests_add_filter( 'papi/settings/show_standard_taxonomy_type_post', '__return_true' );

		$this->assertSame( true, papi_filter_settings_show_standard_taxonomy_type( 'post' ) );
	}
}
