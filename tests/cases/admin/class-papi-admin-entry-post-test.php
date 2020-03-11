<?php

/**
 * @group admin
 */
class Papi_Admin_Entry_Post_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		papi()->reset();

		$this->admin = new Papi_Admin_Entry_Post;

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );
	}

	public function tearDown() {
		parent::tearDown();

		unset( $_GET );
	}

	public function test_hidden_meta_boxes() {
		$_GET['post_type'] = 'page';
		$admin = new Papi_Admin_Entry_Post;
		$this->assertNull( $admin->hidden_meta_boxes() );
		do_meta_boxes( 'papi-hidden-editor', 'normal', null );
		$this->expectOutputRegex( '/.*\S.*/' );
	}

	public function test_hidden_meta_boxes_2() {
		$_GET['post_type'] = 'fake';
		$admin = new Papi_Admin_Entry_Post;
		$this->assertNull( $admin->hidden_meta_boxes() );
		do_meta_boxes( 'papi-hidden-editor', 'normal', null );
		$this->expectOutputRegex( '//' );
	}

	public function test_hidden_meta_box_editor() {
		$this->admin->hidden_meta_box_editor();
		$this->expectOutputRegex( '/wp\-editor\-wrap/' );
	}

	public function test_load_post_new() {
		$_GET['post_type'] = '';
		$admin = new Papi_Admin_Entry_Post;
		$admin->load_post_new();
		$this->expectOutputRegex( '//' );
	}

	public function test_load_post_new_2() {
		$_SERVER['REQUEST_URI'] = 'http://site.com/wp-admin/post-new.php?post_type=page';

		add_filter( 'wp_redirect', function( $location ) {
			$this->assertTrue( strpos( $location, 'edit.php?post_type=page&page=papi-add-new-page,page') !== false );
			return false;
		} );

		$_GET['post_type'] = 'page';
		$admin = new Papi_Admin_Entry_Post;
		$admin->load_post_new();
	}

	public function test_load_post_new_3() {
		$_SERVER['REQUEST_URI'] = 'http://site.com/wp-admin/post-new.php?post_type=page';

		add_filter( 'wp_redirect', function( $location ) {
			$this->assertTrue( strpos( $location, 'post-new.php?page_type=simple-page-type&post_type=page') !== false );
			return false;
		} );

		add_filter( 'papi/settings/only_page_type_page', function () {
			return 'simple-page-type';
		} );

		$_GET['post_type'] = 'page';
		$admin = new Papi_Admin_Entry_Post;
		$admin->load_post_new();
	}

	public function test_load_post_new_4() {
		papi_test_register_book_post_type();

		add_filter( 'papi/settings/show_standard_page_type_book', '__return_false' );

		$_SERVER['REQUEST_URI'] = 'http://site.com/wp-admin/post-new.php?post_type=book';
		add_filter( 'wp_redirect', function( $location ) {
			$this->assertTrue( strpos( $location, 'post-new.php?page_type=book-page-type&post_type=book') !== false );
			return false;
		} );
		$_GET['post_type'] = 'book';
		$admin = new Papi_Admin_Entry_Post;
		$admin->load_post_new();
	}

	public function test_setup_actions() {
		global $current_screen;

	    $current_screen = WP_Screen::get( 'admin_init' );

		$admin = new Papi_Admin_Entry_Post;

		$this->assertSame( 10, has_action( 'load-post-new.php', [$admin, 'load_post_new'] ) );
		$this->assertSame( 10, has_action( 'add_meta_boxes', [$admin, 'hidden_meta_boxes'] ) );
		$this->assertSame( 10, has_action( 'redirect_post_location', [$admin, 'redirect_post_location'] ) );

		$current_screen = null;
	}
}
