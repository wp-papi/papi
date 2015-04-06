<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering page functions.
 *
 * @package Papi
 */

class Papi_Lib_Page_Test extends WP_UnitTestCase {

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
	 * Test `current_page` function.
	 *
	 * @since 1.0.0
	 */

	public function test_current_page() {
		$this->assertNull( current_page() );
	}

	/**
	 * Test `papi_get_all_page_types` function.
	 *
	 * @since 1.3.0
	 */

	public function test_papi_get_all_page_types() {
		$this->assertEmpty( papi_get_all_page_types() );

		tests_add_filter( 'papi/settings/directories', function () {
			return array( 1,  papi_test_get_fixtures_path( '/page-types' ) );
		} );

		$actual = papi_get_all_page_types();
		$this->assertTrue( empty( $actual ) );

		$actual = papi_get_all_page_types( true );
		$this->assertFalse( empty( $actual ) );
	}

	/**
	 * Test `papi_get_file_data` function.
	 *
	 * @since 1.3.0
	 */

	public function test_papi_get_file_data() {
		$this->assertNull( papi_get_file_data( null ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return array( 1,  papi_test_get_fixtures_path( '/page-types' ) );
		} );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );

		$this->assertTrue( is_object( papi_get_file_data( $this->post_id ) ) );
	}

	/**
	 * Test `papi_get_file_data` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_number_of_pages() {
		$this->assertEquals( 0, papi_get_number_of_pages( 'simple-page-type' ) );
		$this->assertEquals( 0, papi_get_number_of_pages( null ) );
		$this->assertEquals( 0, papi_get_number_of_pages( true ) );
		$this->assertEquals( 0, papi_get_number_of_pages( false ) );
		$this->assertEquals( 0, papi_get_number_of_pages( array() ) );
		$this->assertEquals( 0, papi_get_number_of_pages( new stdClass() ) );
		$this->assertEquals( 0, papi_get_number_of_pages( 1 ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return array( 1,  papi_test_get_fixtures_path( '/page-types' ) );
		} );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );

		$this->assertEquals( 1, papi_get_number_of_pages( 'simple-page-type' ) );

		$simple_page_type = papi_get_page_type_by_id( 'simple-page-type' );

		$this->assertEquals( 1, papi_get_number_of_pages( $simple_page_type ) );
	}

	/**
	 * Test `papi_get_page` function.
	 *
	 * @since 1.3.0
	 */

	public function test_papi_get_page() {
		$actual = papi_get_page( $this->post_id );
		$this->assertTrue( is_object( $actual ) );
	}

	/**
	 * Test `papi_get_page_type_template` function.
	 *
	 * @since 1.3.0
 	 */

	public function test_papi_get_page_type_template() {
		$this->assertNull( papi_get_page_type_template( 0 ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return array( 1,  papi_test_get_fixtures_path( '/page-types' ) );
		} );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );

		$actual = papi_get_page_type_template( $this->post_id );
		$this->assertEquals( 'pages/simple-page.php', $actual );
	}

	/**
	 * Test `papi_get_page_type` function.
	 *
	 * @since 1.3.0
	 */

	public function test_papi_get_page_type() {
		$this->assertNull( papi_get_page_type( 'hello.php' ) );
		$path = papi_test_get_fixtures_path( '/boxes/simple.php' );
		$this->assertNull( papi_get_page_type( $path ) );
	}

	/**
	 * Test `papi_get_page_type_by_id` function.
	 *
	 * @since 1.3.0
	 */

	public function test_papi_get_page_type_by_id() {
		$this->assertNull( papi_get_page_type_by_id( 'page' ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return array( 1,  papi_test_get_fixtures_path( '/page-types' ) );
		} );

		$simple_page_type = papi_get_page_type_by_id( 'simple-page-type' );
		$this->assertTrue( is_object( $simple_page_type ) );
	}

	/**
	 * Test `papi_get_page_type_meta_value` function.
	 *
	 * @since 1.3.0
	 */

	public function test_papi_get_page_type_meta_value() {
		$this->assertEmpty( papi_get_page_type_meta_value() );

		tests_add_filter( 'papi/settings/directories', function () {
			return array( 1,  papi_test_get_fixtures_path( '/page-types' ) );
		} );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );
		$this->assertEquals( 'simple-page-type', papi_get_page_type_meta_value( $this->post_id ) );

		$_GET['page_type'] = 'simple-page-type';
		$this->assertEquals( 'simple-page-type', papi_get_page_type_meta_value() );
		unset( $_GET['page_type'] );

		$_POST[PAPI_PAGE_TYPE_KEY] = 'simple-page-type';
		$this->assertEquals( 'simple-page-type', papi_get_page_type_meta_value() );
		unset( $_POST[PAPI_PAGE_TYPE_KEY] );

		$from_post = papi_filter_settings_page_type_from_post_qs();
		$_GET[$from_post] = $this->post_id;
		$this->assertEquals( 'simple-page-type', papi_get_page_type_meta_value() );
		unset( $_GET[$from_post] );

		tests_add_filter( 'papi/settings/page_type_from_post_qs', function () {
			return '';
		} );

		$this->assertEmpty( papi_get_page_type_meta_value() );

	}

	/**
	 * Test `papi_get_post_type` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_post_types() {
		$actual = papi_get_post_types();

		foreach ( $actual as $key => $value ) {
			if ( $value !== 'page' ) {
				unset( $actual[$key] );
			}
		}

		$this->assertEquals( array( 'page' ), array_values( $actual ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return array( 1,  papi_test_get_fixtures_path( '/page-types2' ) );
		} );

		$actual = papi_get_post_types();

		$this->assertEquals( array( 'page' ), $actual );
	}
}
