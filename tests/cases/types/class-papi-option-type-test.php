<?php

/**
 * @group types
 */
class Papi_Option_Type_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		$_GET = [];

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->header_option_type = papi_get_entry_type_by_id( 'options/header-option-type' );
		$this->empty_option_type = new Papi_Option_Type();
	}

	public function tearDown() {
		parent::tearDown();
		unset(
			$_GET,
			$this->header_option_type
		);
	}

	public function test_get_boxes() {
		$this->assertEmpty( $this->empty_option_type->get_boxes() );

		$this->assertTrue( is_array( $this->header_option_type->get_boxes() ) );

		$boxes = $this->header_option_type->get_boxes();

		$this->assertSame( 'Options', $boxes[0]->title );
	}

	public function test_get_property() {
		$this->assertNull( $this->header_option_type->get_property( 'fake' ) );

		$property = $this->header_option_type->get_property( 'image' );
		$this->assertSame( 'image', $property->get_option( 'type' ) );
		$this->assertSame( 'image', $property->type );
		$this->assertSame( 'papi_image', $property->slug );
		$this->assertSame( 'papi_image', $property->get_option( 'slug' ) );
		$this->assertSame( 'Image', $property->get_option( 'title' ) );
		$this->assertSame( 'Image', $property->title );

		$property = $this->header_option_type->get_property( 'name' );
		$this->assertSame( 'string', $property->get_option( 'type' ) );
		$this->assertSame( 'string', $property->type );
		$this->assertSame( 'papi_name', $property->slug );
		$this->assertSame( 'papi_name', $property->get_option( 'slug' ) );
		$this->assertSame( 'Name', $property->get_option( 'title' ) );
		$this->assertSame( 'Name', $property->title );

		$property = $this->header_option_type->get_property( 'name_levels_2', 'child_name_2' );
		$this->assertSame( 'Child name 2', $property->get_option( 'title' ) );
		$this->assertSame( 'Child name 2', $property->title );
		$this->assertSame( 'papi_child_name_2', $property->slug );
		$this->assertSame( 'papi_child_name_2', $property->get_option( 'slug' ) );
		$this->assertSame( 'string', $property->get_option( 'type' ) );
		$this->assertSame( 'string', $property->type );
	}

	public function test_get_child_properties() {
		$property = $this->header_option_type->get_property( 'name_levels' );
		$children1 = $property->get_child_properties();
		$children2 = $children1[0]->get_child_properties();
		$this->assertTrue( papi_is_property( $children2[0] ) );
		$this->assertSame( 'Child child name', $children2[0]->get_option( 'title' ) );
		$this->assertSame( 'Child child name', $children2[0]->title );
		$this->assertSame( 'string', $children2[0]->get_option( 'type' ) );
		$this->assertSame( 'string', $children2[0]->type );
	}

	public function test_meta_info() {
		$this->assertEmpty( $this->empty_option_type->name );
		$this->assertEmpty( $this->empty_option_type->menu );

		$this->assertSame( 'Header', $this->header_option_type->name );
		$this->assertSame( 'options-general.php', $this->header_option_type->menu );
		$this->assertSame( 'This is your header options', $this->header_option_type->description );
	}

	public function test_render() {
		$this->header_option_type->render();
		$this->expectOutputRegex( '/.*/' );
	}
}
