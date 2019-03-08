<?php

/**
 * @group types
 */
class Papi_Page_Type_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		$_GET = [];

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->post_id = $this->factory->post->create();

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'empty-page-type' );

		$this->empty_page_type       = new Papi_Page_Type();
		$this->big_page_type         = papi_get_entry_type_by_id( 'big-page-type' );
		$this->display_not_page_type = papi_get_entry_type_by_id( 'display-not-page-type' );
		$this->faq_page_type         = papi_get_entry_type_by_id( 'faq-page-type' );
		$this->faq_extra_page_type   = papi_get_entry_type_by_id( 'faq-extra-page-type' );
		$this->faq_extra2_page_type  = papi_get_entry_type_by_id( 'faq-extra2-page-type' );
		$this->flex_page_type        = papi_get_entry_type_by_id( 'flex2-page-type' );
		$this->simple_page_type      = papi_get_entry_type_by_id( 'simple-page-type' );
		$this->tab_page_type         = papi_get_entry_type_by_id( 'tab-page-type' );
		$this->properties_page_type  = papi_get_entry_type_by_id( 'properties-page-type' );
	}

	public function tearDown() {
		parent::tearDown();
		unset(
			$_GET,
			$this->post_id,
			$this->big_page_type,
			$this->empty_page_type,
			$this->display_not_page_type,
			$this->faq_page_type,
			$this->faq_extra_page_type,
			$this->faq_extra2_page_type,
			$this->simple_page_type,
			$this->tab_page_type
		);
	}

	public function test_display() {
		$this->assertTrue( $this->properties_page_type->display( 'post' ) );
		$this->assertFalse( $this->flex_page_type->display( 'post' ) );
	}

	public function test_get_body_classes() {
		$this->assertTrue( is_array( $this->empty_page_type->get_body_classes() ) );
		$this->assertEmpty( $this->empty_page_type->get_body_classes() );
		$this->assertSame( ['simple-page-type', 'papi-hide-edit-slug-box', 'papi-hide-pageparentdiv'], $this->simple_page_type->get_body_classes() );
	}

	public function test_get_boxes() {
		$this->assertTrue( is_array( $this->flex_page_type->get_boxes() ) );
		$this->assertTrue( is_array( $this->simple_page_type->get_boxes() ) );

		$boxes = $this->faq_page_type->get_boxes();

		$this->assertSame( 'Content', $boxes[0]->title );
		$this->assertEmpty( $this->empty_page_type->get_boxes() );
	}

	public function test_get_child_types() {
		$this->assertEmpty( $this->simple_page_type->get_child_types() );
		$child_types = $this->faq_page_type->get_child_types();
		$this->assertTrue( is_object( $child_types[0] ) );
	}

	public function test_get_labels() {
		$this->assertEmpty( $this->simple_page_type->get_labels() );
		$this->assertEmpty( $this->empty_page_type->get_labels() );
		$this->assertEquals( [
			'add_new_item' => 'Add New FAQ page',
			'edit_item'    => 'Edit FAQ page',
			'view_item'    => 'View FAQ page',
			'nan_item'     => 'Not a number item'
		], $this->faq_page_type->get_labels() );
	}

	public function test_get_thumbnail() {
		$this->assertSame( '', $this->simple_page_type->get_thumbnail() );
		$this->assertSame( '', $this->empty_page_type->get_thumbnail() );
		$this->assertSame( 'faq.png', $this->faq_page_type->get_thumbnail() );
	}

	public function test_get_property() {
		$this->assertNull( $this->empty_page_type->get_property( 'fake' ) );
		$this->assertNull( $this->simple_page_type->get_property( 'fake' ) );

		$property = $this->simple_page_type->get_property( 'name' );
		$this->assertSame( 'string', $property->get_option( 'type' ) );
		$this->assertSame( 'string', $property->type );
		$this->assertSame( 'papi_name', $property->slug );
		$this->assertSame( 'papi_name', $property->get_option( 'slug' ) );
		$this->assertSame( 'papi_name', $property->get_slug() );
		$this->assertSame( 'name', $property->get_slug( true ) );
		$this->assertSame( 'Name', $property->get_option( 'title' ) );
		$this->assertSame( 'Name', $property->title );

		$property = $this->flex_page_type->get_property( 'sections' );
		$this->assertSame( 'flexible', $property->get_option( 'type' ) );
		$this->assertSame( 'flexible', $property->type );
		$this->assertSame( 'papi_sections', $property->slug );
		$this->assertSame( 'papi_sections', $property->get_option( 'slug' ) );
		$this->assertSame( 'papi_sections', $property->get_slug() );
		$this->assertSame( 'sections', $property->get_slug( true ) );
		$this->assertSame( 'Sections', $property->get_option( 'title' ) );
		$this->assertSame( 'Sections', $property->title );

		$property = $this->properties_page_type->get_property( 'repeater_test', 'book_name' );
		$this->assertSame( 'Book name', $property->get_option( 'title' ) );
		$this->assertSame( 'Book name', $property->title );
		$this->assertSame( 'papi_book_name', $property->slug );
		$this->assertSame( 'papi_book_name', $property->get_option( 'slug' ) );
		$this->assertSame( 'papi_book_name', $property->get_slug() );
		$this->assertSame( 'book_name', $property->get_slug( true ) );
		$this->assertSame( 'string', $property->get_option( 'type' ) );
		$this->assertSame( 'string', $property->type );

		$property = $this->properties_page_type->get_property( 'flexible_test', 'twitter_name' );
		$this->assertSame( 'Twitter name', $property->get_option( 'title' ) );
		$this->assertSame( 'Twitter name', $property->title );
		$this->assertSame( 'papi_twitter_name', $property->slug );
		$this->assertSame( 'papi_twitter_name', $property->get_option( 'slug' ) );
		$this->assertSame( 'papi_twitter_name', $property->get_slug() );
		$this->assertSame( 'twitter_name', $property->get_slug( true ) );
		$this->assertSame( 'string', $property->get_option( 'type' ) );
		$this->assertSame( 'string', $property->type );

		$property = $this->simple_page_type->get_property( 'name_levels', 'child_name' );
		$this->assertSame( 'Child name', $property->get_option( 'title' ) );
		$this->assertSame( 'Child name', $property->title );
		$this->assertSame( 'papi_child_name', $property->slug );
		$this->assertSame( 'papi_child_name', $property->get_option( 'slug' ) );
		$this->assertSame( 'papi_child_name', $property->get_slug() );
		$this->assertSame( 'child_name', $property->get_slug( true ) );
		$this->assertSame( 'string', $property->get_option( 'type' ) );
		$this->assertSame( 'string', $property->type );

		$property = $this->simple_page_type->get_property( 'name_levels_2', 'child_name_2' );
		$this->assertSame( 'Child name 2', $property->get_option( 'title' ) );
		$this->assertSame( 'Child name 2', $property->title );
		$this->assertSame( 'papi_child_name_2', $property->slug );
		$this->assertSame( 'papi_child_name_2', $property->get_option( 'slug' ) );
		$this->assertSame( 'papi_child_name_2', $property->get_slug() );
		$this->assertSame( 'child_name_2', $property->get_slug( true ) );
		$this->assertSame( 'string', $property->get_option( 'type' ) );
		$this->assertSame( 'string', $property->type );

		$property = $this->tab_page_type->get_property( 'name_levels_2', 'child_name_2' );
		$this->assertSame( 'Child name 2', $property->get_option( 'title' ) );
		$this->assertSame( 'Child name 2', $property->title );
		$this->assertSame( 'papi_child_name_2', $property->slug );
		$this->assertSame( 'papi_child_name_2', $property->get_option( 'slug' ) );
		$this->assertSame( 'papi_child_name_2', $property->get_slug() );
		$this->assertSame( 'child_name_2', $property->get_slug( true ) );
		$this->assertSame( 'string', $property->get_option( 'type' ) );
		$this->assertSame( 'string', $property->type );

		$property = $this->simple_page_type->get_property( 'sections[0][title]' );
		$this->assertSame( 'string', $property->get_option( 'type' ) );
		$this->assertSame( 'string', $property->type );
		$this->assertSame( 'papi_title', $property->slug );
		$this->assertSame( 'papi_title', $property->get_option( 'slug' ) );
		$this->assertSame( 'papi_title', $property->get_slug() );
		$this->assertSame( 'title', $property->get_slug( true ) );
		$this->assertSame( 'Title', $property->get_option( 'title' ) );
		$this->assertSame( 'Title', $property->title );
	}

	public function test_get_child_properties() {
		$property = $this->simple_page_type->get_property( 'name_levels' );
		$children1 = $property->get_child_properties();
		$children2 = $children1[0]->get_child_properties();
		$this->assertTrue( papi_is_property( $children2[0] ) );
		$this->assertSame( 'Child child name', $children2[0]->get_option( 'title' ) );
		$this->assertSame( 'Child child name', $children2[0]->title );
		$this->assertSame( 'papi_child_child_name', $children2[0]->slug );
		$this->assertSame( 'papi_child_child_name', $children2[0]->get_option( 'slug' ) );
		$this->assertSame( 'papi_child_child_name', $children2[0]->get_slug() );
		$this->assertSame( 'child_child_name', $children2[0]->get_slug( true ) );
		$this->assertSame( 'string', $children2[0]->get_option( 'type' ) );
		$this->assertSame( 'string', $children2[0]->type );
	}

	public function test_has_post_type() {
		$this->assertTrue( $this->simple_page_type->has_post_type( 'page' ) );
		$this->assertTrue( $this->empty_page_type->has_post_type( 'page' ) );
		$this->assertTrue( $this->faq_page_type->has_post_type( 'faq' ) );
	}

	public function test_meta_info() {
		$this->assertSame( [], $this->empty_page_type->capabilities );
		$this->assertSame( [], $this->empty_page_type->child_types );
		$this->assertSame( '', $this->empty_page_type->description );
		$this->assertFalse( $this->empty_page_type->fill_labels );
		$this->assertSame( '', $this->empty_page_type->name );
		$this->assertSame( 'page', $this->empty_page_type->post_type[0] );
		$this->assertSame( 1000, $this->empty_page_type->sort_order );
		$this->assertFalse( $this->empty_page_type->standard_type );
		$this->assertSame( '', $this->empty_page_type->template );
		$this->assertSame( '', $this->empty_page_type->thumbnail );

		$this->assertSame( ['kvack'], $this->display_not_page_type->capabilities );
		$this->assertSame( [], $this->display_not_page_type->child_types );
		$this->assertSame( 'This is a display not page', $this->display_not_page_type->description );
		$this->assertFalse( $this->display_not_page_type->fill_labels );
		$this->assertSame( 'Display not page', $this->display_not_page_type->name );
		$this->assertSame( 'page', $this->display_not_page_type->post_type[0] );
		$this->assertSame( 1000, $this->display_not_page_type->sort_order );
		$this->assertFalse( $this->display_not_page_type->standard_type );
		$this->assertSame( 'pages/display-not-page.php', $this->display_not_page_type->template );
		$this->assertSame( '', $this->display_not_page_type->thumbnail );

		$this->assertSame( [], $this->faq_page_type->capabilities );
		$this->assertSame( ['simple-page-type', null, 'fake'], $this->faq_page_type->child_types );
		$this->assertSame( 'This is a faq page', $this->faq_page_type->description );
		$this->assertTrue( $this->faq_page_type->fill_labels );
		$this->assertSame( 'FAQ page', $this->faq_page_type->name );
		$this->assertSame( 'faq', $this->faq_page_type->post_type[0] );
		$this->assertSame( 1000, $this->faq_page_type->sort_order );
		$this->assertFalse( $this->faq_page_type->standard_type );
		$this->assertSame( 'pages/faq-page.php', $this->faq_page_type->template );
		$this->assertSame( 'faq.png', $this->faq_page_type->thumbnail );

		$this->assertSame( [], $this->simple_page_type->capabilities );
		$this->assertSame( [], $this->simple_page_type->child_types );
		$this->assertSame( 'This is a simple page', $this->simple_page_type->description );
		$this->assertFalse( $this->simple_page_type->fill_labels );
		$this->assertSame( 'Simple page', $this->simple_page_type->name );
		$this->assertSame( 'page', $this->simple_page_type->post_type[0] );
		$this->assertSame( 1000, $this->simple_page_type->sort_order );
		$this->assertTrue( $this->simple_page_type->standard_type );
		$this->assertSame( 'pages/simple-page.php', $this->simple_page_type->template );
		$this->assertSame( '', $this->simple_page_type->thumbnail );
	}

	public function test_parent_boxes() {
		// FAQ 1
		$property = $this->faq_page_type->get_property( 'question' );
		$this->assertSame( 'string', $property->get_option( 'type' ) );
		$this->assertSame( 'string', $property->type );
		$this->assertSame( 'papi_question', $property->slug );
		$this->assertSame( 'papi_question', $property->get_option( 'slug' ) );
		$this->assertSame( 'papi_question', $property->get_slug() );
		$this->assertSame( 'question', $property->get_slug( true ) );
		$this->assertSame( 'Question', $property->get_option( 'title' ) );
		$this->assertSame( 'Question', $property->title );

		// FAQ 2
		$property = $this->faq_extra_page_type->get_property( 'question' );
		$this->assertSame( 'string', $property->get_option( 'type' ) );
		$this->assertSame( 'string', $property->type );
		$this->assertSame( 'papi_question', $property->slug );
		$this->assertSame( 'papi_question', $property->get_option( 'slug' ) );
		$this->assertSame( 'papi_question', $property->get_slug() );
		$this->assertSame( 'question', $property->get_slug( true ) );
		$this->assertSame( 'Question', $property->get_option( 'title' ) );
		$this->assertSame( 'Question', $property->title );

		// FAQ 3
		$property = $this->faq_extra2_page_type->get_property( 'question' );
		$this->assertSame( 'string', $property->get_option( 'type' ) );
		$this->assertSame( 'string', $property->type );
		$this->assertSame( 'papi_question', $property->slug );
		$this->assertSame( 'papi_question', $property->get_option( 'slug' ) );
		$this->assertSame( 'papi_question', $property->get_slug() );
		$this->assertSame( 'question', $property->get_slug( true ) );
		$this->assertSame( 'Question', $property->get_option( 'title' ) );
		$this->assertSame( 'Question', $property->title );
	}

	public function test_publish_box() {
		$this->simple_page_type->setup();
		$this->assertFalse( has_action( 'post_submitbox_misc_actions', [$this->simple_page_type, 'publish_box'] ) );

		$this->faq_page_type->setup();
		$this->assertSame( 10, has_action( 'post_submitbox_misc_actions', [$this->faq_page_type, 'publish_box'] ) );
	}

	public function test_remove_post_type_supports() {
		global $_wp_post_type_features;
		$this->assertNull( $this->simple_page_type->remove_post_type_support() );
		$_GET['post_type'] = 'page';
		$_wp_post_type_features['page']['editor'] = true;
		$this->simple_page_type->remove_post_type_support();
		$this->assertFalse( isset( $_wp_post_type_features['page']['editor'] ) );
	}

	public function test_remove_post_type_supports_faq_level_1() {
		global $_wp_post_type_features;
		$this->assertNull( $this->faq_page_type->remove_post_type_support() );
		$_GET['post_type'] = 'faq';
		$_wp_post_type_features['faq']['div'] = true;
		$this->faq_page_type->remove_post_type_support();
		$this->assertFalse( isset( $_wp_post_type_features['faq']['div'] ) );
	}

	public function test_remove_post_type_supports_faq_level_2() {
		global $_wp_post_type_features;
		$this->assertNull( $this->faq_extra_page_type->remove_post_type_support() );
		$_GET['post_type'] = 'faq';
		$_wp_post_type_features['faq']['div'] = true;
		$_wp_post_type_features['faq']['blog'] = true;
		$this->faq_extra_page_type->remove_post_type_support();
		$this->assertFalse( isset( $_wp_post_type_features['faq']['div'] ) );
		$this->assertFalse( isset( $_wp_post_type_features['faq']['blog'] ) );
	}

	public function test_remove_post_type_supports_faq_level_3() {
		global $_wp_post_type_features;
		$this->assertNull( $this->faq_extra2_page_type->remove_post_type_support() );
		$_GET['post_type'] = 'faq';
		$_wp_post_type_features['faq']['div'] = true;
		$_wp_post_type_features['faq']['blog'] = true;
		$_wp_post_type_features['faq']['editor'] = true;
		$this->faq_extra2_page_type->remove_post_type_support();
		$this->assertFalse( isset( $_wp_post_type_features['faq']['div'] ) );
		$this->assertFalse( isset( $_wp_post_type_features['faq']['blog'] ) );
		$this->assertFalse( isset( $_wp_post_type_features['faq']['editor'] ) );
	}

	public function test_remove_meta_boxes() {
		global $wp_meta_boxes, $current_screen;

		$_GET['post_type'] = 'faq';
		$current_screen = WP_Screen::get( 'admin_init' );
		$wp_meta_boxes['faq']['normal']['default']['test_meta_box'] = true;

		$this->assertTrue( $wp_meta_boxes['faq']['normal']['default']['test_meta_box'] );

		$this->faq_page_type->remove_post_type_support();
		do_action( 'add_meta_boxes' );

		$this->assertFalse( $wp_meta_boxes['faq']['normal']['default']['test_meta_box'] );
	}

	public function test_remove_all_meta_boxes() {
		global $wp_meta_boxes, $current_screen;

		$_GET['post_type'] = 'page';
		$current_screen = WP_Screen::get( 'admin_init' );
		$wp_meta_boxes['page']['normal']['default']['test_meta_box'] = true;
		$wp_meta_boxes['page']['normal']['default']['_papi_content'] = true;
		$this->assertTrue( $wp_meta_boxes['page']['normal']['default']['test_meta_box'] );

		$this->big_page_type->remove_post_type_support();
		do_action( 'add_meta_boxes' );

		$this->assertFalse( $wp_meta_boxes['page']['normal']['default']['test_meta_box'] );
		$this->assertArrayHasKey( '_papi_content', $wp_meta_boxes['page']['normal']['default'] );
	}

	public function test_setup_page_templates() {
		$_GET['post_type'] = 'page';

		$page_templates = get_page_templates();
		$this->assertEmpty( $page_templates );

		$path = __DIR__ . '/../../data/page-types/name-page-type.php';
		require_once $path;

		new Name_Page_Type( $path );
		$page_templates = wp_get_theme()->get_page_templates();

		$this->assertNotEmpty( $page_templates );
		$this->assertTrue( isset( $page_templates['layout-a.php'] ) );
	}

	public function test_tabs_meta_boxes() {
		$boxes = $this->tab_page_type->get_boxes();

		// Box 1
		$this->assertInstanceOf( 'Papi_Core_Tab', $boxes[0]->properties[0] );
		$this->assertInstanceOf( 'Papi_Core_Tab', $boxes[0]->properties[1] );

		// Box 2.
		$this->assertFalse( $boxes[1]->properties[0] instanceof Papi_Core_Tab );
		$this->assertInstanceOf( 'Papi_Core_Property', $boxes[1]->properties[0] );

		// Box 3.
		$this->assertEmpty( $boxes[2]->properties );

		// Box 4.
		$this->assertFalse( $boxes[3]->properties[0] instanceof Papi_Core_Tab );
		$this->assertInstanceOf( 'Papi_Core_Property', $boxes[3]->properties[0] );

		// Box 5.
		$this->assertInstanceOf( 'Papi_Core_Tab', $boxes[4]->properties[0] );
	}

	public function test_any_post_type() {
		require_once PAPI_FIXTURE_DIR . '/page-types2/any-page-type.php';
		$page_type = new Any_Page_Type( PAPI_FIXTURE_DIR . '/page-types2/any-page-type.php' );
		$this->assertSame( count( $page_type->post_type ), count( get_post_types( '', 'names' ) ) );
	}
}
