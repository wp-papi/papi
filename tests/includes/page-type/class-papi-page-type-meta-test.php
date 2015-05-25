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
	}

	public function tearDown() {
		parent::tearDown();
		unset(
			$this->faq_page_type,
			$this->simple_page_type
		);
	}

	public function test_current_user_is_allowed() {
		$this->assertTrue( $this->simple_page_type->current_user_is_allowed() );
		$this->assertFalse( $this->faq_page_type->current_user_is_allowed() );
	}

	public function test_get_labels() {
		$this->assertEquals( [
			'add_new_item' => 'Add New FAQ page',
			'edit_item'    => 'Edit FAQ page',
			'view_item'    => 'View FAQ page'
		], $this->faq_page_type->get_labels() );
	}

	public function test_get_thumbnail() {
		$this->assertEmpty( $this->simple_page_type->get_thumbnail() );
		$this->assertEquals( 'faq.png', $this->faq_page_type->get_thumbnail() );
	}

	public function test_has_post_type() {
		$this->assertTrue( $this->simple_page_type->has_post_type( 'page' ) );
		$this->assertTrue( $this->faq_page_type->has_post_type( 'faq' ) );
	}

}
