<?php

class Papi_Lib_Core_Meta_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$_GET = [];
		$this->post_id = $this->factory->post->create();
		$this->term_id = $this->factory->term->create();
	}

	public function tearDown() {
		parent::tearDown();
		unset( $_GET, $this->post_id, $this->term_id );
	}

	public function test_papi_get_meta_id() {
		$this->assertSame( 0, papi_get_meta_id( 'option' ) );

		$_GET['post'] = 1;
		$this->assertSame( 1, papi_get_meta_id() );
		$this->assertSame( 1, papi_get_meta_id( 'post' ) );
		unset( $_GET['post'] );

		$_GET['term_id'] = 2;
		$this->assertSame( 2, papi_get_meta_id( 'term' ) );
		unset( $_GET['term_id'] );
	}

	public function test_papi_get_meta_id_column() {
		$this->assertSame( 'post_id', papi_get_meta_id_column() );
		$this->assertSame( 'post_id', papi_get_meta_id_column( 'post' ) );
		$this->assertSame( 'post_id', papi_get_meta_id_column( 'page' ) );
		$this->assertSame( 'term_id', papi_get_meta_id_column( 'term' ) );
		$this->assertSame( 'term_id', papi_get_meta_id_column( 'taxonomy' ) );
		$this->assertNull( papi_get_meta_id_column( 'hello' ) );
	}

	public function test_papi_get_meta_store() {
		$this->assertInstanceOf( 'Papi_Post_Store', papi_get_meta_store( $this->post_id ) );
		$this->assertInstanceOf( 'Papi_Option_Store', papi_get_meta_store( 0, 'option' ) );
		$store = papi_get_meta_store( $this->post_id, 'fake' );
		$this->assertNull( $store );

		if ( function_exists( 'get_term_meta' ) ) {
			$this->assertInstanceOf( 'Papi_Term_Store', papi_get_meta_store( $this->term_id, 'term' ) );
		}
	}

	public function test_papi_get_meta_type() {
		$this->assertSame( 'post', papi_get_meta_type() );
		$this->assertSame( 'post', papi_get_meta_type( 'post' ) );
		$this->assertSame( 'post', papi_get_meta_type( 'page' ) );
		$this->assertSame( 'term', papi_get_meta_type( 'term' ) );
		$this->assertSame( 'term', papi_get_meta_type( 'taxonomy' ) );
		$this->assertSame( 'option', papi_get_meta_type( 'option' ) );
		$this->assertNull( papi_get_meta_type( 'hello' ) );

		$_GET['meta_type'] = 'test';
		$this->assertSame( 'test', papi_get_meta_type() );
		unset( $_GET['meta_type'] );

		$this->assertSame( 'post', papi_get_meta_type() );
	}

	public function test_papi_get_meta_type_quried_object() {
		global $wp_query;

		// Test term object.
		$wp_query->is_category = true;
		$category_id = $this->factory->category->create();
		$wp_query->query_vars['cat'] = $category_id;
		$this->assertSame( 'term', papi_get_meta_type() );
		$this->assertTrue( isset( get_queried_object()->term_id ) );

		// Turn off category query.
		$wp_query->queried_object = null;
		$wp_query->is_category = false;

		// Test post object.
		$post_id = $this->factory->post->create();
		$wp_query->is_singular = true;
		$wp_query->post = get_post( $post_id );
		$this->assertSame( 'post', papi_get_meta_type() );
		$this->assertTrue( isset( get_queried_object()->post_type ) );

		// Turn off post query.
		$wp_query->queried_object = null;
		$wp_query->is_singular = false;
		$wp_query->post = null;
	}

	public function test_papi_get_meta_type_wp_query() {
		global $wp_query;

		$wp_query->is_tag = true;
		$this->assertSame( 'term', papi_get_meta_type() );
		$wp_query->is_tag = false;

		$wp_query->is_category = true;
		$this->assertSame( 'term', papi_get_meta_type() );
		$wp_query->is_category = false;
	}

	public function test_papi_get_meta_type_admin() {
		global $current_screen;

		$current_screen = WP_Screen::get( 'admin_init' );

		$_SERVER['REQUEST_URI'] = 'http://site.com/?taxonomy=test';
		$this->assertSame( 'term', papi_get_meta_type() );
		$_SERVER['REQUEST_URI'] = '';

		$_SERVER['REQUEST_URI'] = 'http://site.com/?page=papi/option/test';
		$this->assertSame( 'option', papi_get_meta_type() );
		$_SERVER['REQUEST_URI'] = '';

		$current_screen = null;
	}

	public function test_papi_get_meta_type_admin_ajax() {
		global $current_screen;

		$current_screen = WP_Screen::get( 'admin_init' );

		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		$_POST['post_type'] = 'post';
		$_POST['taxonomy'] = 'test';
		$this->assertSame( 'term', papi_get_meta_type() );
		unset( $_POST['taxonomy'] );

		$this->assertSame( 'post', papi_get_meta_type() );
		unset( $_POST['post_type'] );

		$current_screen = null;
	}
}
