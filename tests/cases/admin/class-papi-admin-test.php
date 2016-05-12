<?php

/**
 * @group admin
 */
class Papi_Admin_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->admin = new Papi_Admin;
		$this->post_id = $this->factory->post->create();

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->admin, $this->post_id );
	}

	public function register_template_paths( $new_templates ) {
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) {
			$templates = [];
		}

		wp_cache_delete( $cache_key , 'themes' );
		$templates = array_merge( $templates, $new_templates );
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );

		return $new_templates;
	}

	public function test_admin_body_class() {
		$classes = $this->admin->admin_body_class( '' );
		$this->assertSame( ' papi-meta-type-post', $classes );
	}

	public function test_admin_body_class_2() {
		papi()->reset();
		$_GET['post_type'] = 'page';
		$admin = new Papi_Admin;
		$this->register_template_paths( [
			'test.php' => 'Test'
		] );
		$classes = $admin->admin_body_class( '' );
		$this->assertSame( ' papi-meta-type-post papi-hide-cpt', $classes );
	}

	public function test_admin_body_class_with_entry_type_body_classes() {
		$_GET['post'] = $this->factory->post->create();
		$_GET['post_type'] = 'page';
		$_GET['page'] = 'papi/page/simple-page-type';
		$admin = new Papi_Admin;
		$classes = $admin->admin_body_class( '' );
		add_filter( 'theme_page_templates', '__return_empty_array' );
		$this->assertSame( ' papi-meta-type-post simple-page-type', $classes );
	}

	public function test_admin_init() {
		$admin = new Papi_Admin;
		$this->assertNull( $admin->admin_init() );

		$_GET['post'] = $this->factory->post->create();
		$_GET['post_type'] = 'page';
		$_GET['page'] = 'papi/page/simple-page-type';
		$admin = new Papi_Admin;
		$admin->admin_init();
	}

	public function test_edit_form_after_title() {
		$this->admin->edit_form_after_title();
		$this->expectOutputRegex( '/papi\_meta\_nonce/' );
	}

	public function test_edit_form_after_title_2() {
		$_GET['entry_type'] = 'test';
		$this->admin->edit_form_after_title();
		$this->expectOutputRegex( '/name\=\"\_papi\_page\_type\"/' );
	}

	public function test_hidden_meta_boxes() {
		$_GET['post_type'] = 'page';
		$admin = new Papi_Admin;
		$this->assertNull( $admin->hidden_meta_boxes() );
		do_meta_boxes( 'papi-hidden-editor', 'normal', null );
		$this->expectOutputRegex( '/.*\S.*/' );
	}

	public function test_hidden_meta_boxes_2() {
		$_GET['post_type'] = 'fake';
		$admin = new Papi_Admin;
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
		$admin = new Papi_Admin;
		$admin->load_post_new();
		$this->expectOutputRegex( '//' );
	}

	public function test_load_post_new_2() {
		$_SERVER['REQUEST_URI'] = 'http://site.com/wp-admin/post-new.php?post_type=page';

		tests_add_filter( 'wp_redirect', function( $location ) {
			$this->assertSame( 'edit.php?post_type=page&page=papi-add-new-page,page', $location );
			return false;
		} );

		$_GET['post_type'] = 'page';
		$admin = new Papi_Admin;
		$admin->load_post_new();
	}

	public function test_load_post_new_3() {
		$_SERVER['REQUEST_URI'] = 'http://site.com/wp-admin/post-new.php?post_type=page';

		tests_add_filter( 'wp_redirect', function( $location ) {
			$this->assertSame( 'post-new.php?page_type=simple-page-type&post_type=page', $location );
			return false;
		} );

		tests_add_filter( 'papi/settings/only_page_type_page', function () {
			return 'simple-page-type';
		} );

		$_GET['post_type'] = 'page';
		$admin = new Papi_Admin;
		$admin->load_post_new();
	}

	public function test_load_post_new_4() {
		papi_test_register_book_post_type();

		tests_add_filter( 'papi/settings/show_standard_page_type_book', '__return_false' );

		$_SERVER['REQUEST_URI'] = 'http://site.com/wp-admin/post-new.php?post_type=book';
		tests_add_filter( 'wp_redirect', function( $location ) {
			$this->assertSame( 'post-new.php?page_type=book-page-type&post_type=book', $location );
			return false;
		} );
		$_GET['post_type'] = 'book';
		$admin = new Papi_Admin;
		$admin->load_post_new();
	}

	public function test_plugin_row_meta() {
		$output = $this->admin->plugin_row_meta( [], 'fake/fake.php' );
		$this->assertEmpty( $output );

		$output = $this->admin->plugin_row_meta( [], 'papi/papi-loader.php' );
		$this->assertArrayHasKey( 'docs', $output );
	}

	public function test_setup_actions() {
		global $current_screen;

	    $current_screen = WP_Screen::get( 'admin_init' );

		$admin = new Papi_Admin;

		$this->assertSame( 10, has_action( 'admin_init', [$admin, 'admin_init'] ) );
		$this->assertSame( 10, has_action( 'edit_form_after_title', [$admin, 'edit_form_after_title'] ) );
		$this->assertSame( 10, has_action( 'load-post-new.php', [$admin, 'load_post_new'] ) );
		$this->assertSame( 10, has_action( 'add_meta_boxes', [$admin, 'hidden_meta_boxes'] ) );

		$_GET['taxonomy'] = 'post_tag';
		$admin = new Papi_Admin;

		$this->assertSame( 10, has_action( 'post_tag_add_form', [$admin, 'edit_form_after_title'] ) );
		$this->assertSame( 10, has_action( 'post_tag_edit_form', [$admin, 'edit_form_after_title'] ) );

		$current_screen = null;
	}

	public function test_setup_filters() {
		global $current_screen;

	    $current_screen = WP_Screen::get( 'admin_init' );
		$admin = new Papi_Admin;

		$this->assertSame( 10, has_filter( 'admin_body_class', [$admin, 'admin_body_class'] ) );

		$current_screen = null;
	}

	public function test_setup_globals() {
		$_GET['post_type'] = 'page';
		$admin = new Papi_Admin;

		$post_type = function ( Papi_Admin $class ) {
			return $class->post_type;
		};
		$post_type = Closure::bind( $post_type, null, $admin );
		$this->assertSame( 'page', $post_type( $admin ) );
	}

	public function test_setup_papi() {
		$admin = new Papi_Admin;
		$this->assertFalse( $admin->setup_papi() );
		$_GET['post_type'] = 'revision';
		$admin = new Papi_Admin;
		$this->assertFalse( $admin->setup_papi() );
		$_GET['post_type'] = 'nav_menu_item';
		$admin = new Papi_Admin;
		$this->assertFalse( $admin->setup_papi() );

		$_GET['post'] = $this->factory->post->create();
		$_GET['post_type'] = 'page';
		$_GET['page'] = 'papi/page/simple-page-type';
		$admin = new Papi_Admin;
		$this->assertTrue( $admin->setup_papi() );

		unset( $_GET['page'] );

		$_GET['post_type'] = 'attachment';
		$admin = new Papi_Admin;
		$this->assertTrue( $admin->setup_papi() );
	}

	public function test_wp_link_query() {
		$admin = new Papi_Admin;
		$post  = [
			'ID'   => $this->post_id,
			'info' => 'Page'
		];
		$post2 = [
			'ID'   => $this->post_id,
			'info' => 'Standard Page'
		];
		$results = $admin->wp_link_query( [$post] );
		$this->assertSame( [$post2], $results );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'simple-page-type' );

		$post3 = [
			'ID'   => $this->post_id,
			'info' => 'Simple page'
		];
		$results = $admin->wp_link_query( [$post] );
		$this->assertSame( [$post3], $results );
	}

	public function test_wp_refresh_nonces() {
		$admin = new Papi_Admin;

		$arr = [];
		$this->assertEmpty( $admin->wp_refresh_nonces( $arr ) );

		$arr = [
			'wp-refresh-post-nonces' => [
				'replace' => []
			]
		];

		$arr2 = $admin->wp_refresh_nonces( $arr );

		$this->assertArrayHasKey( 'papi_meta_nonce', $arr2['wp-refresh-post-nonces']['replace'] );
	}
}
