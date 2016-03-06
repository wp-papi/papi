<?php

/**
 * @group types
 */
class Papi_Page_Type_Meta_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->display_not_page_type = papi_get_entry_type_by_id( 'display-not-page-type' );
		$this->faq_page_type = papi_get_entry_type_by_id( 'faq-page-type' );
		$this->simple_page_type = papi_get_entry_type_by_id( 'simple-page-type' );
		$this->empty_page_type = new Papi_Page_Type();
	}

	public function tearDown() {
		parent::tearDown();
		unset(
			$this->display_not_page_type,
			$this->empty_page_type,
			$this->faq_page_type,
			$this->simple_page_type
		);
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
}
