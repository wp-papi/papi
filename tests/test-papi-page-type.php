<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering page type functionality.
 *
 * @package Papi
 */

class WP_Papi_Page_Type extends WP_UnitTestCase {

	/**
	 * Setup the test and register the page types directory.
	 *
	 * @since 1.0.0
	 */

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return array( 1, dirname( __FILE__ ) . '/data/page-types' );
		} );

		$this->post_id = $this->factory->post->create();
	}

	/**
	 * Tear down test.
	 *
	 * @since 1.3.0
	 */

	public function tearDown() {
		unset( $this->post_id );
	}

	/**
	 * Test so we acctually has any page type files.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_all_page_types() {
		$page_types = papi_get_all_page_types( true );
		$this->assertTrue( ! empty( $page_types ) );
	}

	/**
	 * Test so it works to load a single page type
	 * and save Papi page type value on a post.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_page_type() {
		$page_type = papi_get_page_type( dirname( __FILE__ ) . '/data/page-types/simple-page-type.php' );
		$this->assertEquals( $page_type->name, 'Simple page' );

		$this->assertEquals( 1000, $page_type->sort_order );

		add_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, $page_type->get_id() );
		$this->assertEquals( $page_type->get_id(), papi_get_page_type_meta_value( $this->post_id ) );

		$this->assertEquals( 'pages/simple-page.php', papi_get_page_type_template( $this->post_id ) );
	}

	/**
	 * Test papi_get_page_type_by_id.
	 *
	 * @since 1.3.0
	 */

	public function test_papi_get_page_type_by_id() {
		$page_type = papi_get_page_type_by_id( 'custom-page-type-id' );
		$this->assertEquals( $page_type->name, 'Identifier page' );

		$this->assertEquals( 1000, $page_type->sort_order );

		add_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, $page_type->get_id() );
		$this->assertEquals( $page_type->get_id(), papi_get_page_type_meta_value( $this->post_id ) );

		$this->assertEquals( 'pages/identifier-page.php', papi_get_page_type_template( $this->post_id ) );
	}

	/**
	 * Test papi_get_all_files_in_directory.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_all_files_in_directory() {
		$files  = papi_get_all_page_type_files();
		$actual = false;

		foreach ($files as $file) {
			if ($actual === false) {
				$actual = strpos( $file, 'simple-page-type' ) !== false;
			}
		}

		$this->assertTrue( $actual );
	}

	/**
	 * Test papi_get_page_type_base_path.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_page_type_base_path() {
		$files = papi_get_all_page_type_files();
		$files = array_map( 'papi_get_page_type_base_path', $files );
		$this->assertTrue( in_array( 'simple-page-type' , $files ) );
	}

	/**
	 * Test slug generation.
	 *
	 * @since 1.0.0
	 */

	public function test_slug() {
		$slug = papi_get_property_type_key_f( 'heading' );
		$this->assertEquals( $slug, '_heading_property' );
	}

	/**
	 * Test creating a fake property data via `add_post_meta`.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_fields() {
		$slug = 'heading';
		add_post_meta( $this->post_id, $slug, 'papi' );

		$slug_type = papi_f( papi_get_property_type_key( $slug ) );
		add_post_meta( $this->post_id, $slug_type, 'string' );

		$heading = papi_field( $this->post_id, $slug );
		$this->assertEquals( $heading, 'papi' );

		$heading_property = get_post_meta( $this->post_id, $slug_type, true );
		$this->assertEquals( $heading_property, 'string' );
	}

}
