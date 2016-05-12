<?php

/**
 * @group types
 */
class Papi_Entry_Type_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/entry-types'];
		} );

		$this->post_id = $this->factory->post->create();

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'empty-page-type' );

		$this->empty_entry_type  = new Papi_Entry_Type();
		$this->info_entry_type = papi_get_entry_type( PAPI_FIXTURE_DIR . '/entry-types/info-entry-type.php' );
		$this->term_entry_type = papi_get_entry_type( PAPI_FIXTURE_DIR . '/entry-types/term-entry-type.php' );
	}

	public function tearDown() {
		parent::tearDown();
		unset(
			$this->post_id,
			$this->empty_entry_type,
			$this->info_entry_type,
			$this->term_entry_type
		);
	}

	public function test_body_classes() {
		$this->assertTrue( is_array( $this->empty_entry_type->body_classes() ) );
		$this->assertEmpty( $this->empty_entry_type->body_classes() );
	}

	public function test_broken_page_type() {
		$this->assertNull( papi_get_entry_type_by_id( 'broken-entry-type' ) );
	}

	public function test_get_boxes() {
		$this->assertTrue( is_array( $this->info_entry_type->get_boxes() ) );

		$boxes = $this->info_entry_type->get_boxes();

		$this->assertSame( 'Info', $boxes[0]->title );

		$this->assertEmpty( $this->empty_entry_type->get_boxes() );
	}

	public function test_get_class_name() {
		$this->assertEmpty( $this->empty_entry_type->get_class_name() );
		$this->assertSame( 'Info_Entry_Type', $this->info_entry_type->get_class_name() );
	}

	public function test_get_file_path() {
		$this->assertEmpty( $this->empty_entry_type->get_file_path() );

		$this->assertSame(
			PAPI_FIXTURE_DIR . '/entry-types/info-entry-type.php',
			$this->info_entry_type->get_file_path()
		);
	}

	public function test_get_id() {
		$this->assertEmpty( $this->empty_entry_type->get_id() );
		$this->assertSame( 'info-entry-type', $this->info_entry_type->get_id() );
	}

	public function test_get_labels() {
		$entry_type = new Papi_Entry_Type;
		$this->assertEmpty( $entry_type->get_labels() );
	}

	public function test_get_property() {
		$this->assertNull( $this->empty_entry_type->get_property( 'fake' ) );
		$this->assertNull( $this->info_entry_type->get_property( 'fake' ) );

		$property = $this->info_entry_type->get_property( 'info' );
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
		$this->assertSame( 'entry', $this->empty_entry_type->get_type() );
		$this->assertSame( 'entry', $this->info_entry_type->get_type() );
	}

	public function test_help_tabs() {
		$help = $this->info_entry_type->help();

		$this->assertArrayHasKey( 'Hello 1', $help );
		$this->assertArrayHasKey( 'Hello 2', $help );

		global $current_screen;

		$this->assertNull( $this->info_entry_type->add_help_tabs() );
		$this->assertNull( $current_screen );

	    $current_screen = WP_Screen::get( 'admin_init' );

	    $this->info_entry_type->add_help_tabs();
	    $tabs = $current_screen->get_help_tabs();

		$this->assertArrayHasKey( 'papi_hello_1', $tabs );
		$this->assertArrayHasKey( 'papi_hello_2', $tabs );

		$this->assertSame( '<p>Lorem ipsum</p>', trim( $tabs['papi_hello_1']['content'] ) );
		$this->assertSame( '<p>Lorem ipsum 2</p>', trim( $tabs['papi_hello_2']['callback']() ) );

	    $current_screen = null;
	}

	public function test_help_tabs_meta_property() {
		global $current_screen;
	    $current_screen = WP_Screen::get( 'admin_init' );

	    $this->term_entry_type->add_help_tabs();
	    $tabs = $current_screen->get_help_tabs();

	    $this->assertEmpty( $tabs );

	    $current_screen = null;
	}

	public function test_match_id() {
		$this->assertTrue( $this->empty_entry_type->match_id( '' ) );
		$this->assertTrue( $this->info_entry_type->match_id( 'info-entry-type' ) );
	}

	public function test_new_class() {
		$this->assertEmpty( $this->empty_entry_type->new_class() );
		$this->assertEquals( new Info_Entry_Type(), $this->info_entry_type->new_class() );
	}

	public function test_setup() {
		$this->assertTrue( apply_filters( 'screen_options_show_screen', true ) );
		$this->info_entry_type->setup();
		$this->assertFalse( apply_filters( 'screen_options_show_screen', true ) );
	}

	public function test_sort_order() {
		$this->assertSame( 1000, $this->empty_entry_type->sort_order );
		$this->assertSame( 500, $this->info_entry_type->sort_order );
	}
}
