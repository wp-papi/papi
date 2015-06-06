<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Option_Type` class.
 *
 * @package Papi
 */

class Papi_Option_Type_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		$_GET = [];

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->header_option_type = papi_get_page_type_by_id( 'options/header-option-type' );
		$this->empty_option_type = new Papi_Option_Type();
	}

	public function tearDown() {
		parent::tearDown();
		unset(
			$_GET,
			$this->header_option_type
		);
	}

	public function test_display() {
		$this->assertFalse( $this->header_option_type->display( $this->header_option_type->post_type[0] ) );
		$this->assertTrue( $this->empty_option_type->display( $this->empty_option_type->post_type[0] ) );
	}

	public function test_get_boxes() {
		$this->assertNull( $this->empty_option_type->get_boxes() );

		$this->assertTrue( is_array( $this->header_option_type->get_boxes() ) );

		$boxes = $this->header_option_type->get_boxes();

		$this->assertEquals( 'Options', $boxes[0][0]['title'] );
	}

	public function test_get_property() {
		$this->assertNull( $this->header_option_type->get_property( 'fake' ) );

		$property = $this->header_option_type->get_property( 'image' );
		$this->assertEquals( 'image', $property->get_option( 'type' ) );
		$this->assertEquals( 'image', $property->type );
		$this->assertEquals( 'papi_image', $property->slug );
		$this->assertEquals( 'papi_image', $property->get_option( 'slug' ) );
		$this->assertEquals( 'Image', $property->get_option( 'title' ) );
		$this->assertEquals( 'Image', $property->title );

		$property = $this->header_option_type->get_property( 'name' );
		$this->assertEquals( 'string', $property->get_option( 'type' ) );
		$this->assertEquals( 'string', $property->type );
		$this->assertEquals( 'papi_name', $property->slug );
		$this->assertEquals( 'papi_name', $property->get_option( 'slug' ) );
		$this->assertEquals( 'Name', $property->get_option( 'title' ) );
		$this->assertEquals( 'Name', $property->title );

		$property = $this->header_option_type->get_property( 'name_levels_2', 'child_name_2' );
		$this->assertEquals( 'Child name 2', $property->get_option( 'title' ) );
		$this->assertEquals( 'Child name 2', $property->title );
		$this->assertEquals( 'papi_child_name_2', $property->slug );
		$this->assertEquals( 'papi_child_name_2', $property->get_option( 'slug' ) );
		$this->assertEquals( 'string', $property->get_option( 'type' ) );
		$this->assertEquals( 'string', $property->type );
	}

	public function test_get_child_properties() {
		$property = $this->header_option_type->get_property( 'name_levels' );
		$children1 = $property->get_child_properties();
		$children2 = $children1[0]->get_child_properties();
		$this->assertTrue( papi_is_property( $children2[0] ) );
		$this->assertEquals( 'Child child name', $children2[0]->get_option( 'title' ) );
		$this->assertEquals( 'Child child name', $children2[0]->title );
		$this->assertEquals( 'string', $children2[0]->get_option( 'type' ) );
		$this->assertEquals( 'string', $children2[0]->type );
	}

	public function test_has_post_type() {
		$this->assertTrue( $this->header_option_type->has_post_type( $this->header_option_type->post_type[0] ) );
		$this->assertTrue( $this->empty_option_type->has_post_type( $this->empty_option_type->post_type[0] ) );
	}

	public function test_meta_method() {
		$this->assertEquals( 'option_type', $this->header_option_type->_meta_method );
		$this->assertEquals( 'option_type', $this->empty_option_type->_meta_method );
	}

	public function test_meta_info() {
		$this->assertEmpty( $this->empty_option_type->name );
		$this->assertEmpty( $this->empty_option_type->menu );

		$this->assertEquals( 'Header', $this->header_option_type->name );
		$this->assertEquals( 'options-general.php', $this->header_option_type->menu );
	}

	public function test_post_type() {
		$this->assertTrue( is_array( $this->header_option_type->post_type ) );
		$this->assertEquals( '_papi_option_type', $this->header_option_type->post_type[0] );
		$this->assertEquals( '_papi_option_type', $this->header_option_type->get_post_type() );
		$this->assertTrue( is_array( $this->empty_option_type->post_type ) );
		$this->assertEquals( '_papi_option_type', $this->empty_option_type->post_type[0] );
		$this->assertEquals( '_papi_option_type', $this->empty_option_type->get_post_type() );
	}

	public function test_render() {
		$this->header_option_type->render();
		$this->expectOutputRegex( '/.*/' );
	}

	public function test_setup() {
		$this->assertNull( $this->header_option_type->setup() );
		$this->assertNull( $this->empty_option_type->setup() );
	}

}
