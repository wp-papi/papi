<?php

/**
 * @group types
 */
class Papi_Data_Type_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->post_id = $this->factory->post->create();

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'empty-page-type' );

		$this->empty_data_type  = new Papi_Data_Type();
		$this->info_data_type = papi_get_data_type( PAPI_FIXTURE_DIR . '/page-types/info-data-type.php' );
	}

	public function tearDown() {
		parent::tearDown();
		unset(
			$this->post_id,
			$this->empty_data_type,
			$this->info_data_type
		);
	}

	public function test_meta_method() {
		$this->assertSame( 'meta', $this->empty_data_type->_meta_method );
		$this->assertSame( 'meta', $this->info_data_type->_meta_method );
	}

	public function test_broken_page_type() {
		$this->assertNull( papi_get_data_type_by_id( 'broken-data-type' ) );
	}

	public function test_get_boxes() {
		$this->assertTrue( is_array( $this->info_data_type->get_boxes() ) );

		$boxes = $this->info_data_type->get_boxes();

		$this->assertSame( 'Info', $boxes[0][0]['title'] );

		$this->assertEmpty( $this->empty_data_type->get_boxes() );
	}

	public function test_get_class_name() {
		$this->assertEmpty( $this->empty_data_type->get_class_name() );
		$this->assertSame( 'Info_Data_Type', $this->info_data_type->get_class_name() );
	}

	public function test_get_file_path() {
		$this->assertEmpty( $this->empty_data_type->get_file_path() );

		$this->assertSame(
			PAPI_FIXTURE_DIR . '/page-types/info-data-type.php',
			$this->info_data_type->get_file_path()
		);
	}

	public function test_get_id() {
		$this->assertEmpty( $this->empty_data_type->get_id() );
		$this->assertSame( 'info-data-type', $this->info_data_type->get_id() );
	}

	public function test_get_property() {
		$this->assertNull( $this->empty_data_type->get_property( 'fake' ) );
		$this->assertNull( $this->info_data_type->get_property( 'fake' ) );

		$property = $this->info_data_type->get_property( 'info' );
		$this->assertSame( 'string', $property->get_option( 'type' ) );
		$this->assertSame( 'string', $property->type );
		$this->assertSame( 'papi_info', $property->slug );
		$this->assertSame( 'papi_info', $property->get_option( 'slug' ) );
		$this->assertSame( 'papi_info', $property->get_slug() );
		$this->assertSame( 'info', $property->get_slug( true ) );
		$this->assertSame( 'Info', $property->get_option( 'title' ) );
		$this->assertSame( 'Info', $property->title );
	}

	public function test_get_type() {
		$this->assertSame( 'data', $this->empty_data_type->get_type() );
		$this->assertSame( 'data', $this->info_data_type->get_type() );
	}

	public function test_match_id() {
		$this->assertTrue( $this->empty_data_type->match_id( '' ) );
		$this->assertTrue( $this->info_data_type->match_id( 'info-data-type' ) );
	}

	public function test_new_class() {
		$this->assertEmpty( $this->empty_data_type->new_class() );
		$this->assertEquals( new Info_Data_Type(), $this->info_data_type->new_class() );
	}

	public function test_sort_order() {
		$this->assertSame( 1000, $this->empty_data_type->sort_order );
		$this->assertSame( 500, $this->info_data_type->sort_order );
	}
}
