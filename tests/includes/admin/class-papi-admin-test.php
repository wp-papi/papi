<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Admin` class.
 *
 * @package Papi
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
            $templates = array();
        }

        wp_cache_delete( $cache_key , 'themes' );
        $templates = array_merge( $templates, $new_templates );
        wp_cache_add( $cache_key, $templates, 'themes', 1800 );

        return $new_templates;
	}

	public function test_admin_body_class() {
		$classes = $this->admin->admin_body_class( '' );
		$this->assertEmpty( $classes );
	}

	public function test_admin_body_class_2() {
		$this->register_template_paths( [
			'test.php' => 'Test'
		] );
		$_GET['post_type'] = 'page';
		$classes = $this->admin->admin_body_class( '' );
		$this->assertEquals( 'papi-hide-cpt', $classes );
	}

	public function test_admin_enqueue_scripts() {
		$_SERVER['REQUEST_URI'] = 'plugins.php';
		$this->assertNull( $this->admin->admin_enqueue_scripts() );
		$_SERVER['REQUEST_URI'] = '';
		$this->assertNull( $this->admin->admin_enqueue_scripts() );
	}

	public function test_admin_init() {
		$admin = new Papi_Admin;
		$this->assertNull( $admin->admin_init() );

		$_GET['post'] = $this->factory->post->create();
		$_GET['post_type'] = 'page';
		$_GET['page'] = 'papi/simple-page-type';
		$admin = new Papi_Admin;
		$admin->admin_init();
	}

	public function test_admin_head() {
		$this->assertNull( $this->admin->admin_head() );
	}

	public function test_edit_form_after_title() {
		$this->admin->edit_form_after_title();
		$this->expectOutputRegex( '/papi\_meta\_nonce/' );
	}

	public function test_hidden_meta_boxes() {
		global $wp_meta_boxes;
		$_GET['post_type'] = 'page';
		$admin = new Papi_Admin;
		$this->assertNull( $admin->hidden_meta_boxes() );
		do_meta_boxes('papi-hidden-editor', 'normal', null);
		$this->expectOutputRegex( '/.*\S.*/' );
	}

	public function test_hidden_meta_boxes_2() {
		global $wp_meta_boxes;
		$_GET['post_type'] = 'fake';
		$admin = new Papi_Admin;
		$this->assertNull( $admin->hidden_meta_boxes() );
		do_meta_boxes('papi-hidden-editor', 'normal', null);
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
			$this->assertEquals( 'edit.php?post_type=page&page=papi-add-new-page,page', $location );
			return false;
		} );
		$_GET['post_type'] = 'page';
    	$admin = new Papi_Admin;
		$admin->load_post_new();
	}

	public function test_load_post_new_3() {
		$_SERVER['REQUEST_URI'] = 'http://site.com/wp-admin/post-new.php?post_type=page';
		tests_add_filter( 'wp_redirect', function( $location ) {
			$this->assertEquals( 'post-new.php?page_type=simple-page-type&post_type=page', $location );
			return false;
		} );
		tests_add_filter( 'papi/settings/only_page_type_page', function () {
			return 'simple-page-type';
		} );
		$_GET['post_type'] = 'page';
    	$admin = new Papi_Admin;
		$admin->load_post_new();
	}

	public function test_manage_page_type_posts_columns() {
		$arr = $this->admin->manage_page_type_posts_columns( [] );
		$this->assertEquals( ['page_type' => 'Page Type'], $arr );
	}

	public function test_manage_page_type_posts_custom_column() {
		$this->admin->manage_page_type_posts_custom_column( 'page_type', $this->post_id );
		$this->expectOutputRegex( '/Standard Page/' );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );
		$this->admin->manage_page_type_posts_custom_column( 'page_type', $this->post_id );
		$this->expectOutputRegex( '/Simple page/' );
	}

	public function test_pre_get_posts() {
		global $pagenow;
		$pagenow = 'edit.php';

		$_GET['page_type'] = 'simple-page-type';
		$query = $this->admin->pre_get_posts( new WP_Query() );
		$this->assertEquals( [
			'meta_key'   => PAPI_PAGE_TYPE_KEY,
			'meta_value' => 'simple-page-type'
		], $query->query_vars );

		$_GET['page_type'] = 'papi-standard-page';
		$query = $this->admin->pre_get_posts( new WP_Query() );
		$this->assertEquals( [
			[
				'key'     => PAPI_PAGE_TYPE_KEY,
				'compare' => 'NOT EXISTS'
			]
		], $query->query_vars['meta_query'] );
	}

	public function test_render_view() {
		$_GET['page'] = '';
		$this->admin->render_view();
		$this->expectOutputRegex( '/\<h2\>Papi\s\-\s404\<\/h2\>/' );

		$_GET['page'] = 'papi-add-new-page,page';
		$this->admin->render_view();
		$this->expectOutputRegex( '/Add new page type/' );
	}

	public function test_restrict_page_types() {
		$_GET['post_type'] = '';
		$admin = new Papi_Admin;
		$admin->restrict_page_types();
		$this->expectOutputRegex( '//' );
	}

	public function test_restrict_page_types_2() {
		$_GET['post_type'] = 'page';
		$admin = new Papi_Admin;
		$admin->restrict_page_types();
		$this->expectOutputRegex( '/.*\S.*/' );
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
		$_GET['page'] = 'papi/simple-page-type';
		$admin = new Papi_Admin;
		$this->assertTrue( $admin->setup_papi() );
	}

}
