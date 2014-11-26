<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

		global $papi_directories;
		$papi_directories = array();

		register_page_types_directory( dirname( __FILE__ ) . '/data/page-types' );

		$this->post_id = $this->factory->post->create();
	}

	/**
	 * Test so we acctually has any page type files.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_all_page_types() {
		$page_types = _papi_get_all_page_types( true );
		$this->assertTrue( ! empty( $page_types ) );
	}

	/**
	 * Test so it works to load a single page type
	 * and save Papi page type value on a post.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_page_type() {
		// Test to load the page type.
		$page_type = _papi_get_page_type( dirname( __FILE__ ) . '/data/page-types/simple-page-type.php' );
		$this->assertEquals( $page_type->name, 'Simple page' );

		// Test the default sort order value.
		$this->assertEquals( 1000, $page_type->sort_order );

		// Test to save the page type value and load it.
		add_post_meta( $this->post_id, '_papi_page_type', $page_type->get_filename() );
		$this->assertEquals( $page_type->get_filename(), _papi_get_page_type_meta_value( $this->post_id ) );

		// Test to get the template file from the page type.
		$this->assertEquals( 'pages/simple-page.php', _papi_get_page_type_template( $this->post_id ) );
	}

	/**
	 * Test _papi_get_all_files_in_directory.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_all_files_in_directory() {
		$files = _papi_get_all_page_type_files();
		$this->assertTrue( strpos( $files[0], 'simple-page-type' ) !== false );
	}

	/**
	 * Test _papi_get_page_type_base_path.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_page_type_base_path() {
		$files = _papi_get_all_page_type_files();
		$this->assertEquals( 'simple-page-type', _papi_get_page_type_base_path( $files[0] ) );
	}

	/**
	 * Test slug generation.
	 *
	 * @since 1.0.0
	 */

	public function test_slug() {
		$slug = _papi_get_property_type_key_f( 'heading' );
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

		$slug_type = _papi_f( _papi_get_property_type_key( $slug ) );
		add_post_meta( $this->post_id, $slug_type, 'string' );

		$heading = papi_field( $this->post_id, $slug );
		$this->assertEquals( $heading, 'papi' );

		$heading_property = get_post_meta( $this->post_id, $slug_type, true );
		$this->assertEquals( $heading_property, 'string' );
	}

}
