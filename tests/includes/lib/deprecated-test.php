<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering deprecated functions.
 *
 * @package Papi
 */
class Papi_Lib_Deprecated_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$_GET = [];

		add_action( 'deprecated_function_run', [$this, 'deprecated_function_run'] );
		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->post_id = $this->factory->post->create();
		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );
	}

	public function deprecated_function_run( $function ) {
		add_filter( 'deprecated_function_trigger_error', '__return_false' );
	}

	public function tearDown() {
		parent::tearDown();
		remove_filter( 'deprecated_function_trigger_error', '__return_false' );
		unset( $_GET, $this->post_id );
	}

	/**
	 * `current_page` is deprecated since 2.0.0.
	 */
	public function test_current_page() {
		$this->assertNull( current_page() );
	}

	/**
	 * `papi_field` is deprecated since 2.0.0.
	 */
	public function test_papi_field() {
		update_post_meta( $this->post_id, 'name', 'fredrik' );

		$this->assertNull( papi_field( '' ) );
		$this->assertNull( papi_field( $this->post_id, '' ) );

		$this->assertSame( 'fredrik', papi_field( $this->post_id, 'name' ) );
		$this->assertSame( 'fredrik', papi_field( $this->post_id, 'name', '', 'post' ) );

		$this->assertSame( 'world', papi_field( $this->post_id, 'hello', 'world' ) );

		$_GET['post_id'] = $this->post_id;
		$this->assertNull( papi_field( 'name' ) );
		$this->assertSame( 'fredrik', papi_field( '', 'fredrik' ) );
	}

	/**
	 * `papi_fields` is deprecated since 2.0.0.
	 */
	public function test_papi_fields() {
		$this->assertEmpty( papi_fields() );

		global $post;

		$post = get_post( $this->post_id );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );
		$actual = papi_fields( $this->post_id );

		$this->assertTrue( ! empty( $actual ) );
		$this->assertTrue( is_array( $actual ) );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, '' );
		$this->flush_cache();
		$this->assertEmpty( papi_fields() );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'empty-page-type' );
		$this->flush_cache();
		$this->assertEmpty( papi_fields() );
	}

	/**
	 * `papi_get_page_type_meta_value` is deprecated since 2.0.0.
	 */
	public function test_papi_get_page_type_meta_value() {
		$this->assertEmpty( papi_get_page_type_meta_value() );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );
		$this->assertSame( 'simple-page-type', papi_get_page_type_meta_value( $this->post_id ) );

		$_GET['page_type'] = 'simple-page-type';
		$this->assertSame( 'simple-page-type', papi_get_page_type_meta_value() );
		unset( $_GET['page_type'] );

		$_POST[PAPI_PAGE_TYPE_KEY] = 'simple-page-type';
		$this->assertSame( 'simple-page-type', papi_get_page_type_meta_value() );
		unset( $_POST[PAPI_PAGE_TYPE_KEY] );

		$post_parent = 'post_parent';
		$_GET[$post_parent] = $this->post_id;
		$this->assertSame( 'simple-page-type', papi_get_page_type_meta_value() );
		unset( $_GET[$post_parent] );

		tests_add_filter( 'papi/settings/page_type_from_post_qs', function () {
			return '';
		} );

		$this->assertEmpty( papi_get_page_type_meta_value() );

	}
}
