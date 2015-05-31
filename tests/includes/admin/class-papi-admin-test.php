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
		$this->admin = Papi_Admin::instance();
		$this->post_id = $this->factory->post->create();

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->admin, $this->post_id );
	}

	public function test_admin_body_class() {
		$classes = $this->admin->admin_body_class( [] );
		$this->assertEmpty( $classes );
	}

	public function test_admin_init() {
		$this->assertNull( $this->admin->admin_init() );
	}

	public function test_edit_form_after_title() {
		$this->admin->edit_form_after_title();
		$this->expectOutputRegex( '/papi\_meta\_nonce/' );
	}

	public function test_hidden_meta_box_editor() {
		$this->admin->hidden_meta_box_editor();
		$this->expectOutputRegex( '/wp\-editor\-wrap/' );
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

	public function test_setup_papi() {
		$this->assertFalse( $this->admin->setup_papi() );
	}

}
