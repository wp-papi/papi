<?php

/**
 * @group admin
 */
class Papi_Admin_Columns_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$_GET = [];
		$this->post_id = $this->factory->post->create();
		$this->term_id = $this->factory->term->create();

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types', PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );
	}

	public function tearDown() {
		parent::tearDown();
		unset( $_GET, $this->post_id, $this->term_id );
	}

	public function test_manage_page_type_posts_columns() {
		$_GET['post_type'] = 'page';
		$admin = new Papi_Admin_Columns;
		$arr = $admin->manage_page_type_posts_columns( [] );
		$this->assertSame( ['entry_type' => 'Type'], $arr );
		$_GET['post_type'] = 'fake';
		$admin = new Papi_Admin_Columns;
		$arr = $admin->manage_page_type_posts_columns( [] );
		$this->assertEmpty( $arr );
		unset( $_GET['post_type'] );
	}

	public function test_manage_page_type_posts_columns_hide_filter() {
		$_GET['post_type'] = 'page';
		$admin = new Papi_Admin_Columns;
		add_filter( 'papi/settings/column_hide_page', '__return_true' );
		$arr = $admin->manage_page_type_posts_columns( [] );
		$this->assertFalse( isset( $arr['entry_type'] ) );
		unset( $_GET['post_type'] );
	}

	public function test_manage_page_type_sortable_columns() {
		$_GET['post_type'] = 'page';
		$admin = new Papi_Admin_Columns;
		$arr = $admin->manage_page_type_sortable_columns( [] );
		$this->assertSame( ['entry_type' => 'entry_type'], $arr );
	}

	public function test_manage_page_type_posts_columns_title_filter() {
		$_GET['post_type'] = 'page';
		$admin = new Papi_Admin_Columns;
		$arr = $admin->manage_page_type_posts_columns( [] );
		$this->assertSame( ['entry_type' => 'Type'], $arr );

		add_filter( 'papi/settings/column_title_page', function () {
			return 'Typer';
		} );

		$arr = $admin->manage_page_type_posts_columns( [] );
		$this->assertSame( ['entry_type' => 'Typer'], $arr );
		unset( $_GET['post_type'] );
	}

	public function test_manage_page_type_posts_custom_column_standard() {
		$_GET['post_type'] = 'page';
		$admin = new Papi_Admin_Columns;
		$admin->manage_page_type_posts_custom_column( 'entry_type', $this->post_id );
		$this->expectOutputRegex( '/Standard\sPage/' );
		unset( $_GET['post_type'] );
	}

	public function test_manage_page_type_posts_custom_column_empty() {
		$_GET['post_type'] = 'page';
		$admin = new Papi_Admin_Columns;
		$admin->manage_page_type_posts_custom_column( '', $this->post_id );
		$this->expectOutputRegex( '//' );
		unset( $_GET['post_type'] );
	}

	public function test_manage_page_type_posts_custom_column_auto() {
		$_GET['post_type'] = 'page';
		update_post_meta( $this->post_id, papi_get_page_type_key(), 'simple-page-type' );
		$admin = new Papi_Admin_Columns;
		$admin->manage_page_type_posts_custom_column( 'entry_type', $this->post_id );
		$this->expectOutputRegex( '/Simple page/' );
		unset( $_GET['post_type'] );
	}

	public function test_manage_page_type_posts_custom_column_fake() {
		$post_id = $this->factory->post->create();
		$_GET['post_type'] = 'fake';
		$admin = new Papi_Admin_Columns;
		$admin->manage_page_type_posts_custom_column( 'entry_type', $post_id );
		$this->expectOutputRegex( '//' );
		unset( $_GET['post_type'] );
	}

	public function test_manage_taxonomy_posts_custom_column_empty() {
		$_GET['taxonomy'] = 'category';
		$admin = new Papi_Admin_Columns;
		$admin->manage_page_type_posts_custom_column( '', '', $this->term_id );
		$this->expectOutputRegex( '//' );
		unset( $_GET['taxonomy'] );
	}

	public function test_manage_page_type_posts_custom_column_hide_filter() {
		$_GET['post_type'] = 'page';
		$admin = new Papi_Admin_Columns;
		update_post_meta( $this->post_id, papi_get_page_type_key(), 'simple-page-type' );
		add_filter( 'papi/settings/column_hide_page', '__return_true' );

		$admin->manage_page_type_posts_custom_column( 'entry_type', $this->post_id );
		$this->expectOutputRegex( '//' );
		unset( $_GET['post_type'] );
	}

	public function test_pre_get_posts() {
		global $pagenow;
		$pagenow = 'edit.php';
		$admin = new Papi_Admin_Columns;

		$_GET['page_type'] = 'simple-page-type';
		$query = $admin->pre_get_posts( new WP_Query() );
		$this->assertSame( [
			'meta_key'   => papi_get_page_type_key(),
			'meta_value' => 'simple-page-type'
		], $query->query_vars );

		$_GET['page_type'] = 'papi-standard-page';
		$query = $admin->pre_get_posts( new WP_Query() );
		$this->assertSame( [
			[
				'key'     => papi_get_page_type_key(),
				'compare' => 'NOT EXISTS'
			]
		], $query->query_vars['meta_query'] );
		unset( $_GET['page_type'] );

		$_GET['page_type'] = 'papi-standard-page';
		$_GET['orderby'] = 'entry_type';
		$query = new WP_Query();
		$query->set( 'orderby', 'entry_type' );
		$query = $admin->pre_get_posts( $query );
		$this->assertSame( papi_get_page_type_key(), $query->query_vars['meta_key'] );
		$this->assertSame( 'meta_value', $query->query_vars['orderby'] );
		unset( $_GET['page_type'] );
	}

	public function test_restrict_page_types() {
		$_GET['post_type'] = '';
		$admin = new Papi_Admin_Columns;
		$admin->restrict_page_types();
		$this->expectOutputRegex( '//' );
		unset( $_GET['post_type'] );
	}

	public function test_restrict_page_types_2() {
		$_GET['post_type'] = 'page';
		add_filter( 'papi/settings/show_standard_page_type_in_filter_page', '__return_true' );
		$admin = new Papi_Admin_Columns;
		$admin->restrict_page_types();
		$this->expectOutputRegex( '/.*\S.*/' );
		unset( $_GET['post_type'] );
	}

	public function test_setup_actions() {
		$_GET['post_type'] = 'page';
		$admin = new Papi_Admin_Columns;
		$this->assertSame( 10, has_action( 'restrict_manage_posts', [$admin, 'restrict_page_types'] ) );
		unset( $_GET['post_type'] );
	}

	public function test_setup_filters() {
		$_GET['post_type'] = 'page';
		$admin = new Papi_Admin_Columns;
		$this->assertSame( 10, has_filter( 'pre_get_posts', [$admin, 'pre_get_posts'] ) );
		$this->assertSame( 10, has_filter( 'manage_page_posts_columns', [$admin, 'manage_page_type_posts_columns'] ) );
		$this->assertSame( 10, has_filter( 'manage_page_posts_custom_column', [$admin, 'manage_page_type_posts_custom_column'] ) );
		unset( $_GET['post_type'] );
	}
}
