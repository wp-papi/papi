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
	 * Test _papi_filter_default_sort_order.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_filter_default_sort_order() {
		$this->assertEquals( 1000, _papi_filter_default_sort_order() );

		tests_add_filter('papi_default_sort_order', function () {
			return 1;
		});

		$this->assertEquals( 1, _papi_filter_default_sort_order() );
	}

	/**
	 * Test _papi_filter_only_page_type.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_filter_only_page_type() {
		$this->assertEquals( '', _papi_filter_only_page_type( 'post' ) );

		tests_add_filter('papi_only_page_type_for_post', function () {
			return 'simple-page-type';
		});

		$this->assertEquals( 'simple-page-type', _papi_filter_only_page_type( 'post' ) );
	}

	/**
	 * Test _papi_filter_show_standard_page_type_for.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_filter_show_standard_page_type_for() {
		$this->assertEquals( true, _papi_filter_show_standard_page_for( 'post' ) );

		tests_add_filter('papi_show_standard_page_for_post', '__return_false');

		$this->assertEquals( false, _papi_filter_show_standard_page_for( 'post' ) );
	}

	/**
	 * Test _papi_filter_page_type_directories.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_filter_page_type_directories() {
		tests_add_filter('papi_page_type_directories', function () {
			return array();
		});

		$this->assertEmpty( _papi_filter_page_type_directories() );
	}

}
