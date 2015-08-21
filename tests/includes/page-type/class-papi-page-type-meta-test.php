<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Page_Type_Meta` class.
 *
 * @package Papi
 */
class Papi_Page_Type_Meta_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->faq_page_type    = papi_get_page_type_by_id( 'faq-page-type' );
		$this->simple_page_type = papi_get_page_type_by_id( 'simple-page-type' );
		$this->empty_page_type = new Papi_Page_Type();
	}

	public function tearDown() {
		parent::tearDown();
		unset(
			$this->empty_page_type,
			$this->faq_page_type,
			$this->simple_page_type
		);
	}

	public function test_meta_method() {
		$this->assertEquals( 'page_type', $this->simple_page_type->_meta_method );
		$this->assertEquals( 'page_type', $this->empty_page_type->_meta_method );
		$this->assertEquals( 'page_type', $this->faq_page_type->_meta_method );
	}

	public function test_current_user_is_allowed() {
		$this->assertTrue( $this->simple_page_type->current_user_is_allowed() );
		$this->assertTrue( $this->empty_page_type->current_user_is_allowed() );
		$this->assertFalse( $this->faq_page_type->current_user_is_allowed() );
	}

	public function test_get_child_page_types() {
		$this->assertEmpty( $this->simple_page_type->get_child_page_types() );
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
		$this->assertEquals( '', $this->simple_page_type->get_thumbnail() );
		$this->assertEquals( '', $this->empty_page_type->get_thumbnail() );
		$this->assertEquals( 'faq.png', $this->faq_page_type->get_thumbnail() );
	}

	public function test_has_post_type() {
		$this->assertTrue( $this->simple_page_type->has_post_type( 'page' ) );
		$this->assertTrue( $this->empty_page_type->has_post_type( 'page' ) );
		$this->assertTrue( $this->faq_page_type->has_post_type( 'faq' ) );
	}

	public function test_meta_info() {
		$this->assertEquals( [], $this->empty_page_type->capabilities );
		$this->assertEquals( [], $this->empty_page_type->child_page_types );
		$this->assertEquals( '', $this->empty_page_type->description );
		$this->assertFalse( $this->empty_page_type->fill_labels );
		$this->assertEquals( '', $this->empty_page_type->name );
		$this->assertEquals( 'page', $this->empty_page_type->post_type[0] );
		$this->assertEquals( 1000, $this->empty_page_type->sort_order );
		$this->assertEquals( '', $this->empty_page_type->template );
		$this->assertEquals( '', $this->simple_page_type->thumbnail );

		$this->assertEquals( [ 'kvack' ], $this->faq_page_type->capabilities );
		$this->assertEquals( [], $this->faq_page_type->child_page_types );
		$this->assertEquals( 'This is a faq page', $this->faq_page_type->description );
		$this->assertTrue( $this->faq_page_type->fill_labels );
		$this->assertEquals( 'FAQ page', $this->faq_page_type->name );
		$this->assertEquals( 'faq', $this->faq_page_type->post_type[0] );
		$this->assertEquals( 1000, $this->faq_page_type->sort_order );
		$this->assertEquals( 'pages/faq-page.php', $this->faq_page_type->template );
		$this->assertEquals( 'faq.png', $this->faq_page_type->thumbnail );

		$this->assertEquals( [], $this->simple_page_type->capabilities );
		$this->assertEquals( [], $this->simple_page_type->child_page_types );
		$this->assertEquals( 'This is a simple page', $this->simple_page_type->description );
		$this->assertFalse( $this->simple_page_type->fill_labels );
		$this->assertEquals( 'Simple page', $this->simple_page_type->name );
		$this->assertEquals( 'page', $this->simple_page_type->post_type[0] );
		$this->assertEquals( 1000, $this->simple_page_type->sort_order );
		$this->assertEquals( 'pages/simple-page.php', $this->simple_page_type->template );
		$this->assertEquals( '', $this->simple_page_type->thumbnail );
	}

}
