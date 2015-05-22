<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Page_Type` class.
 *
 * @package Papi
 */

class Papi_Page_Type_Base_Test extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 */

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->post_id = $this->factory->post->create();

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'empty-page-type' );
		$this->empty_page_type  = new Papi_Page_Type();

		$this->faq_page_type    = papi_get_page_type_by_id( 'faq-page-type' );
		$this->simple_page_type = papi_get_page_type_by_id( 'simple-page-type' );
	}

	/**
	 * Tear down test.
	 */

	public function tearDown() {
		parent::tearDown();
		unset(
			$this->post_id,
			$this->empty_page_type,
			$this->faq_page_type,
			$this->simple_page_type
		);
	}

	/**
	 * Test broken page type with a non existing meta method.
	 */

	public function test_broken_page_type() {
		$this->assertNull( papi_get_page_type_by_id( 'broken-page-type' ) );
	}

	/**
	 * Test `get_class_name` method.
	 */

	public function test_get_class_name() {
		$this->assertEmpty( $this->empty_page_type->get_class_name() );
		$this->assertEquals( 'Simple_Page_Type', $this->simple_page_type->get_class_name() );
		$this->assertEquals( 'FAQ_Page_Type', $this->faq_page_type->get_class_name() );
	}

	/**
	 * Test `get_file_path` method.
	 */

	public function test_get_file_path() {
		$this->assertEmpty( $this->empty_page_type->get_file_path() );

		$this->assertEquals(
			PAPI_FIXTURE_DIR . '/page-types/simple-page-type.php',
			$this->simple_page_type->get_file_path()
		);

		$this->assertEquals(
				PAPI_FIXTURE_DIR . '/page-types/faq-page-type.php',
			$this->faq_page_type->get_file_path()
		);
	}

	/**
	 * Test `get_id` method.
	 */

	public function test_get_id() {
		$this->assertEmpty( $this->empty_page_type->get_id() );
		$this->assertEquals( 'simple-page-type', $this->simple_page_type->get_id() );
		$this->assertEquals( 'faq-page-type', $this->faq_page_type->get_id() );
		$identifier_page_type = papi_get_page_type_by_id( 'custom-page-type-id' );
		$this->assertEquals( 'custom-page-type-id', $identifier_page_type->get_id() );
	}

	/**
	 * Test `match_id` method.
	 */

	public function test_match_id() {
		$this->assertTrue( $this->empty_page_type->match_id( '' ) );
		$this->assertTrue( $this->simple_page_type->match_id( 'simple-page-type' ) );
		$this->assertTrue( $this->faq_page_type->match_id( 'faq-page-type' ) );
	}

	/**
	 * Test `new_class` method.
	 */

	public function test_new_class() {
		$this->assertEmpty( $this->empty_page_type->new_class() );
		$this->assertEquals( new Simple_Page_Type(), $this->simple_page_type->new_class() );
	}

}
