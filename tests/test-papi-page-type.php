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
	 */

	public function setUp() {
		parent::setUp();

		register_page_types_directory( getcwd() . '/tests/data/page-types' );
	}

	/**
	 * Test so we acctually has any page type files.
	 */

	public function test_papi_get_all_page_types() {
		$page_types = _papi_get_all_page_types( true );
		$this->assertTrue( ! empty( $page_types ) );
	}

	/**
	 * Test slug generation.
	 */

	public function test_slug() {
		$slug = _test_papi_generate_slug( 'heading' );
		$this->assertEquals( $slug, '_papi_heading' );

		$slug = _papi_property_type_key( $slug );
		$this->assertEquals( $slug, '_papi_heading_property' );
	}

	/**
	 * Test creating a fake property data via `add_post_meta`.
	 */

	public function test_papi_field() {
		$post_id = $this->factory->post->create();

		$slug = _test_papi_generate_slug( 'heading' );
		add_post_meta( $post_id, $slug, 'papi' );

		$slug = _papi_property_type_key( $slug );
		add_post_meta( $post_id, $slug, 'PropertyString' );

		$heading = papi_field( $post_id, 'heading' );
		$this->assertEquals( $heading, 'papi' );

		$heading_property = papi_field( $post_id, 'heading_property' );
		$this->assertEquals( $heading_property, 'PropertyString' );
	}

}
