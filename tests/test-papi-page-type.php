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
		$slug = _papi_f(_papi_property_type_key( 'heading' ));
		$this->assertEquals( $slug, '_heading_property' );
	}

	/**
	 * Test creating a fake property data via `add_post_meta`.
	 */

	public function test_papi_field() {
		$post_id = $this->factory->post->create();

		$slug = 'heading';
		add_post_meta( $post_id, $slug, 'papi' );

		$slug_type = _papi_f(_papi_property_type_key( $slug ));
		add_post_meta( $post_id, $slug_type, 'PropertyString' );

		$heading = papi_field( $post_id, $slug );
		$this->assertEquals( $heading, 'papi' );

		$heading_property = papi_field( $post_id, $slug_type );
		$this->assertEquals( $heading_property, 'PropertyString' );
	}

}
