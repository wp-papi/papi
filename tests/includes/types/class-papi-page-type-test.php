<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Page_Type` class.
 *
 * @package Papi
 */
class Papi_Page_Type_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		$_GET = [];

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->post_id = $this->factory->post->create();

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'empty-page-type' );
		$this->empty_page_type  = new Papi_Page_Type();

		$this->faq_page_type        = papi_get_page_type_by_id( 'faq-page-type' );
		$this->flex_page_type       = papi_get_page_type_by_id( 'flex-page-type' );
		$this->simple_page_type     = papi_get_page_type_by_id( 'simple-page-type' );
		$this->tab_page_type        = papi_get_page_type_by_id( 'tab-page-type' );
		$this->properties_page_type = papi_get_page_type_by_id( 'properties-page-type' );
	}

	public function tearDown() {
		parent::tearDown();
		unset(
			$_GET,
			$this->post_id,
			$this->empty_page_type,
			$this->faq_page_type,
			$this->simple_page_type,
			$this->tab_page_type
		);
	}

	public function test_meta_method() {
		$this->assertEquals( 'page_type', $this->empty_page_type->_meta_method );
	}

	public function test_display() {
		$this->assertTrue( $this->properties_page_type->display( 'post' ) );
		$this->assertFalse( $this->flex_page_type->display( 'post' ) );
	}

	public function test_get_boxes() {
		$this->assertTrue( is_array( $this->flex_page_type->get_boxes() ) );
		$this->assertTrue( is_array( $this->simple_page_type->get_boxes() ) );

		$boxes = $this->faq_page_type->get_boxes();

		$this->assertEquals( 'Content', $boxes[0][0]['title'] );

		$this->assertEmpty( $this->empty_page_type->get_boxes() );
	}

	public function test_get_property() {
		$this->assertNull( $this->empty_page_type->get_property( 'fake' ) );
		$this->assertNull( $this->simple_page_type->get_property( 'fake' ) );

		$property = $this->simple_page_type->get_property( 'name' );
		$this->assertEquals( 'string', $property->get_option( 'type' ) );
		$this->assertEquals( 'string', $property->type );
		$this->assertEquals( 'papi_name', $property->slug );
		$this->assertEquals( 'papi_name', $property->get_option( 'slug' ) );
		$this->assertEquals( 'papi_name', $property->get_slug() );
		$this->assertEquals( 'name', $property->get_slug( true ) );
		$this->assertEquals( 'Name', $property->get_option( 'title' ) );
		$this->assertEquals( 'Name', $property->title );

		$property = $this->flex_page_type->get_property( 'sections' );
		$this->assertEquals( 'flexible', $property->get_option( 'type' ) );
		$this->assertEquals( 'flexible', $property->type );
		$this->assertEquals( 'papi_sections', $property->slug );
		$this->assertEquals( 'papi_sections', $property->get_option( 'slug' ) );
		$this->assertEquals( 'papi_sections', $property->get_slug() );
		$this->assertEquals( 'sections', $property->get_slug( true ) );
		$this->assertEquals( 'Sections', $property->get_option( 'title' ) );
		$this->assertEquals( 'Sections', $property->title );

		$property = $this->properties_page_type->get_property( 'repeater_test', 'book_name' );
		$this->assertEquals( 'Book name', $property->get_option( 'title' ) );
		$this->assertEquals( 'Book name', $property->title );
		$this->assertEquals( 'papi_book_name', $property->slug );
		$this->assertEquals( 'papi_book_name', $property->get_option( 'slug' ) );
		$this->assertEquals( 'papi_book_name', $property->get_slug() );
		$this->assertEquals( 'book_name', $property->get_slug( true ) );
		$this->assertEquals( 'string', $property->get_option( 'type' ) );
		$this->assertEquals( 'string', $property->type );

		$property = $this->properties_page_type->get_property( 'flexible_test', 'twitter_name' );
		$this->assertEquals( 'Twitter name', $property->get_option( 'title' ) );
		$this->assertEquals( 'Twitter name', $property->title );
		$this->assertEquals( 'papi_twitter_name', $property->slug );
		$this->assertEquals( 'papi_twitter_name', $property->get_option( 'slug' ) );
		$this->assertEquals( 'papi_twitter_name', $property->get_slug() );
		$this->assertEquals( 'twitter_name', $property->get_slug( true ) );
		$this->assertEquals( 'string', $property->get_option( 'type' ) );
		$this->assertEquals( 'string', $property->type );

		$property = $this->simple_page_type->get_property( 'name_levels', 'child_name' );
		$this->assertEquals( 'Child name', $property->get_option( 'title' ) );
		$this->assertEquals( 'Child name', $property->title );
		$this->assertEquals( 'papi_child_name', $property->slug );
		$this->assertEquals( 'papi_child_name', $property->get_option( 'slug' ) );
		$this->assertEquals( 'papi_child_name', $property->get_slug() );
		$this->assertEquals( 'child_name', $property->get_slug( true ) );
		$this->assertEquals( 'string', $property->get_option( 'type' ) );
		$this->assertEquals( 'string', $property->type );

		$property = $this->simple_page_type->get_property( 'name_levels_2', 'child_name_2' );
		$this->assertEquals( 'Child name 2', $property->get_option( 'title' ) );
		$this->assertEquals( 'Child name 2', $property->title );
		$this->assertEquals( 'papi_child_name_2', $property->slug );
		$this->assertEquals( 'papi_child_name_2', $property->get_option( 'slug' ) );
		$this->assertEquals( 'papi_child_name_2', $property->get_slug() );
		$this->assertEquals( 'child_name_2', $property->get_slug( true ) );
		$this->assertEquals( 'string', $property->get_option( 'type' ) );
		$this->assertEquals( 'string', $property->type );

		$property = $this->tab_page_type->get_property( 'name_levels_2', 'child_name_2' );
		$this->assertEquals( 'Child name 2', $property->get_option( 'title' ) );
		$this->assertEquals( 'Child name 2', $property->title );
		$this->assertEquals( 'papi_child_name_2', $property->slug );
		$this->assertEquals( 'papi_child_name_2', $property->get_option( 'slug' ) );
		$this->assertEquals( 'papi_child_name_2', $property->get_slug() );
		$this->assertEquals( 'child_name_2', $property->get_slug( true ) );
		$this->assertEquals( 'string', $property->get_option( 'type' ) );
		$this->assertEquals( 'string', $property->type );

		$property = $this->simple_page_type->get_property( 'sections[0][title]' );
		$this->assertEquals( 'string', $property->get_option( 'type' ) );
		$this->assertEquals( 'string', $property->type );
		$this->assertEquals( 'papi_title', $property->slug );
		$this->assertEquals( 'papi_title', $property->get_option( 'slug' ) );
		$this->assertEquals( 'papi_title', $property->get_slug() );
		$this->assertEquals( 'title', $property->get_slug( true ) );
		$this->assertEquals( 'Title', $property->get_option( 'title' ) );
		$this->assertEquals( 'Title', $property->title );
	}

	public function test_get_child_properties() {
		$property = $this->simple_page_type->get_property( 'name_levels' );
		$children1 = $property->get_child_properties();
		$children2 = $children1[0]->get_child_properties();
		$this->assertTrue( papi_is_property( $children2[0] ) );
		$this->assertEquals( 'Child child name', $children2[0]->get_option( 'title' ) );
		$this->assertEquals( 'Child child name', $children2[0]->title );
		$this->assertEquals( 'papi_child_child_name', $children2[0]->slug );
		$this->assertEquals( 'papi_child_child_name', $children2[0]->get_option( 'slug' ) );
		$this->assertEquals( 'papi_child_child_name', $children2[0]->get_slug() );
		$this->assertEquals( 'child_child_name', $children2[0]->get_slug( true ) );
		$this->assertEquals( 'string', $children2[0]->get_option( 'type' ) );
		$this->assertEquals( 'string', $children2[0]->type );
	}

	public function test_remove_post_type_support() {
		$_GET['post_type'] = 'page';
		$this->assertNull( $this->simple_page_type->remove_post_type_support() );
		$this->assertNull( $this->simple_page_type->remove_meta_boxes() );
		$_GET['post_type'] = '';
		$this->assertNull( $this->simple_page_type->remove_meta_boxes() );
	}

	public function test_setup() {
		$this->assertNull( $this->simple_page_type->setup() );
		$this->assertNull( $this->empty_page_type->setup() );
		$this->assertNull( $this->faq_page_type->setup() );
		$this->assertNull( $this->tab_page_type->setup() );
	}
}
