<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering page type functionality.
 *
 * @package Papi
 */

class WP_Test_Papi_Page_Type extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 *
	 * @since 1.3.0
	 */

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return array( 1,  papi_test_get_files_path( '/page-types' ) );
		} );

		$this->post_id = $this->factory->post->create();

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'empty-page-type' );
		$this->empty_page_type  = new Papi_Page_Type();

		$this->faq_page_type    = papi_get_page_type_by_id( 'faq-page-type' );
		$this->simple_page_type = papi_get_page_type_by_id( 'simple-page-type' );
	}

	/**
	 * Test get_boxes method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_boxes() {
		$this->assertEmpty( $this->simple_page_type->get_boxes() );
		$this->assertTrue( is_array( $this->simple_page_type->get_boxes() ) );

		$boxes = $this->faq_page_type->get_boxes();

		$this->assertEquals( 'Content', $boxes[0][0]['title'], 'Content' );
	}

	/**
	 * Test get_class_name method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_class_name() {
		$this->assertEmpty( $this->empty_page_type->get_class_name() );
		$this->assertEquals( 'Simple_Page_Type', $this->simple_page_type->get_class_name() );
		$this->assertEquals( 'FAQ_Page_Type', $this->faq_page_type->get_class_name() );
	}

	/**
	 * Test get_file_path method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_file_path() {
		$this->assertEmpty( $this->empty_page_type->get_file_path() );

		$this->assertEquals(
			papi_test_get_files_path( '/page-types/simple-page-type.php' ),
			$this->simple_page_type->get_file_path()
		);

		$this->assertEquals(
			papi_test_get_files_path( '/page-types/faq-page-type.php' ),
			$this->faq_page_type->get_file_path()
		);
	}

	/**
	 * Test get_id method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_id() {
		$this->assertEmpty( $this->empty_page_type->get_id() );
		$this->assertEquals( 'simple-page-type', $this->simple_page_type->get_id() );
		$this->assertEquals( 'faq-page-type', $this->faq_page_type->get_id() );
		$identifier_page_type = papi_get_page_type_by_id( 'custom-page-type-id' );
		$this->assertEquals( 'custom-page-type-id', $identifier_page_type->get_id() );
	}

	/**
	 * Test match_id method.
	 *
	 * @since 1.3.0
	 */

	public function test_match_id() {
		$this->assertTrue( $this->empty_page_type->match_id( '' ) );
		$this->assertTrue( $this->simple_page_type->match_id( 'simple-page-type' ) );
		$this->assertTrue( $this->faq_page_type->match_id( 'faq-page-type' ) );
	}

	/**
	 * Test new_class method.
	 *
	 * @since 1.3.0
	 */

	public function test_new_class() {
		$this->assertEmpty( $this->empty_page_type->new_class() );
		$this->assertEquals( new Simple_Page_Type(), $this->simple_page_type->new_class() );
	}

}
