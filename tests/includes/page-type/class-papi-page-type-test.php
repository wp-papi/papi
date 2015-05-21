<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Page_Type` class.
 *
 * @package Papi
 */

class Papi_Page_Type_Test extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 *
	 * @since 1.3.0
	 */

	public function setUp() {
		parent::setUp();

		$_GET = [];

		tests_add_filter( 'papi/settings/directories', function () {
			return [ 1,  papi_test_get_fixtures_path( '/page-types' ) ];
		} );

		$this->post_id = $this->factory->post->create();

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'empty-page-type' );
		$this->empty_page_type  = new Papi_Page_Type();

		$this->faq_page_type    = papi_get_page_type_by_id( 'faq-page-type' );
		$this->simple_page_type = papi_get_page_type_by_id( 'simple-page-type' );
		$this->tab_page_type    = papi_get_page_type_by_id( 'tab-page-type' );
		$this->flex_page_type   = papi_get_page_type_by_id( 'flex-page-type' );
	}

	/**
	 * Tear down test.
	 *
	 * @since 1.3.0
	 */

	public function tearDown() {
		parent::tearDown();
		$_GET = [];
		unset(
			$this->post_id,
			$this->empty_page_type,
			$this->faq_page_type,
			$this->simple_page_type,
			$this->tab_page_type
		);
	}

	/**
	 * Test `get_boxes` method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_boxes() {
		$this->assertTrue( is_array( $this->simple_page_type->get_boxes() ) );
		$this->assertTrue( is_array( $this->flex_page_type->get_boxes() ) );

		$boxes = $this->faq_page_type->get_boxes();

		$this->assertEquals( 'Content', $boxes[0][0]['title'] );

		$this->assertNull( $this->empty_page_type->get_boxes() );
	}

	/**
	 * Test `get_property` method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_property() {
		$this->assertNull( $this->empty_page_type->get_property( 'fake' ) );
		$this->assertNull( $this->simple_page_type->get_property( 'fake' ) );

		$property = $this->simple_page_type->get_property( 'name' );

		$this->assertEquals( 'string', $property->type );
		$this->assertEquals( 'Name', $property->title );

		$property = $this->flex_page_type->get_property( 'sections' );

		$this->assertEquals( 'flexible', $property->type );
		$this->assertEquals( 'Sections', $property->title );
	}

	/**
	 * Test `remove` method.
	 *
	 * @since 1.3.0
	 */

	public function test_remove_post_type_support() {
		$_GET['post_type'] = 'page';
		$this->assertNull( $this->simple_page_type->remove_post_type_support() );
		$this->assertNull( $this->simple_page_type->remove_meta_boxes() );
		$_GET['post_type'] = '';
		$this->assertNull( $this->simple_page_type->remove_meta_boxes() );
	}

	/**
	 * Test `setup` method.
	 *
	 * @since 1.3.0
	 */

	public function test_setup() {
		$this->assertNull( $this->simple_page_type->setup() );
		$this->assertNull( $this->empty_page_type->setup() );
		$this->assertNull( $this->faq_page_type->setup() );
		$this->assertNull( $this->tab_page_type->setup() );
	}
}
