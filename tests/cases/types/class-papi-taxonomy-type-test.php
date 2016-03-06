<?php

/**
 * @group types
 */
class Papi_Taxonomy_Type_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );

		$this->term_id = $this->factory->term->create();

		$this->empty_taxonomy_type      = new Papi_Taxonomy_Type();
		$this->properties_taxonomy_type = papi_get_entry_type_by_id( 'properties-taxonomy-type' );
		$this->simple_taxonomy_type     = papi_get_entry_type_by_id( 'simple-taxonomy-type' );
	}

	public function tearDown() {
		parent::tearDown();
		unset(
			$this->post_id,
			$this->empty_taxonomy_type,
			$this->properties_taxonomy_type,
			$this->simple_taxonomy_type
		);
	}

	public function test_display() {
		$this->assertTrue( $this->properties_taxonomy_type->display( 'post_tag' ) );
		$this->assertFalse( $this->simple_taxonomy_type->display( 'test_taxonomy' ) );
	}

	public function test_edit_form() {
		$_GET['taxonomy'] = 'post_tag';
		$_GET['post'] = 'post';
		$this->simple_taxonomy_type->setup();
		$this->simple_taxonomy_type->edit_form();
		$this->expectOutputRegex( '/name\=\"papi\_name\"/' );
	}

	public function test_get_boxes() {
		$this->assertTrue( is_array( $this->properties_taxonomy_type->get_boxes() ) );
		$this->assertTrue( is_array( $this->simple_taxonomy_type->get_boxes() ) );

		$boxes = $this->simple_taxonomy_type->get_boxes();

		$this->assertSame( 'Content', $boxes[0]->title );
		$this->assertEmpty( $this->empty_taxonomy_type->get_boxes() );
	}

	public function test_meta_data() {
		$this->assertEmpty( $this->empty_taxonomy_type->taxonomy );
		$this->assertSame( ['post_tag'], $this->properties_taxonomy_type->taxonomy );
	}

	public function test_get_property() {
		$this->assertNull( $this->empty_taxonomy_type->get_property( 'fake' ) );
		$this->assertNull( $this->simple_taxonomy_type->get_property( 'fake' ) );

		$property = $this->properties_taxonomy_type->get_property( 'repeater_test', 'book_name' );
		$this->assertSame( 'Book name', $property->get_option( 'title' ) );
		$this->assertSame( 'Book name', $property->title );
		$this->assertSame( 'papi_book_name', $property->slug );
		$this->assertSame( 'papi_book_name', $property->get_option( 'slug' ) );
		$this->assertSame( 'papi_book_name', $property->get_slug() );
		$this->assertSame( 'book_name', $property->get_slug( true ) );
		$this->assertSame( 'string', $property->get_option( 'type' ) );
		$this->assertSame( 'string', $property->type );

		$property = $this->properties_taxonomy_type->get_property( 'flexible_test', 'twitter_name' );
		$this->assertSame( 'Twitter name', $property->get_option( 'title' ) );
		$this->assertSame( 'Twitter name', $property->title );
		$this->assertSame( 'papi_twitter_name', $property->slug );
		$this->assertSame( 'papi_twitter_name', $property->get_option( 'slug' ) );
		$this->assertSame( 'papi_twitter_name', $property->get_slug() );
		$this->assertSame( 'twitter_name', $property->get_slug( true ) );
		$this->assertSame( 'string', $property->get_option( 'type' ) );
		$this->assertSame( 'string', $property->type );
	}
}
