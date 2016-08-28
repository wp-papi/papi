<?php

class Papi_Lib_Core_Cache_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->post_id = $this->factory->post->create();

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'simple-page-type' );
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id );
	}

	public function test_papi_cache_delete_1() {
		papi_cache_set( 'test', $this->post_id, 'fredrik' );
		$this->assertSame( 'fredrik', papi_cache_get( 'test', $this->post_id ) );

		papi_cache_set( 'test', $this->post_id, 'elli' );
		$this->assertSame( 'elli', papi_cache_get( 'test', $this->post_id ) );

		papi_cache_delete( 'test', $this->post_id );
		$this->assertEmpty( papi_cache_get( 'test', $this->post_id ) );
	}

	public function test_papi_cache_delete_2() {
		papi_update_property_meta_value( [
			'id'    => $this->post_id,
			'slug'  => 'namn',
			'value' => 'fredrik'
		] );
		$this->assertSame( 'fredrik', papi_get_field( $this->post_id, 'namn' ) );

		papi_update_property_meta_value( [
			'id'    => $this->post_id,
			'slug'  => 'namn',
			'value' => 'elli'
		] );
		$this->assertSame( 'elli', papi_get_field( $this->post_id, 'namn' ) );
	}

	public function test_papi_cache_delete_admin() {
		papi_cache_delete( 'test', $this->post_id );
		$this->assertEmpty( papi_cache_get( 'test', $this->post_id ) );

		global $current_screen;
		$current_screen = WP_Screen::get( 'admin_init' );
		papi_cache_set( 'test', $this->post_id, 'elli' );
		$this->assertSame( 'elli', papi_cache_get( 'test', $this->post_id ) );

		papi_cache_delete( 'test', $this->post_id );
		$this->assertEmpty( papi_cache_get( 'test', $this->post_id ) );
		$current_screen = null;
	}

	public function test_papi_cache_get() {
		papi_cache_set( 'get', $this->post_id, 'elli' );
		$this->assertSame( 'elli', papi_cache_get( 'get', $this->post_id ) );
	}

	public function test_papi_cache_get_admin() {
		global $current_screen;
		$current_screen = WP_Screen::get( 'admin_init' );

		papi_cache_set( 'test', $this->post_id, 'elli' );
		$this->assertSame( 'elli', papi_cache_get( 'test', $this->post_id ) );

		$current_screen = null;

		papi_cache_delete( 'test', $this->post_id );
		$this->assertEmpty( papi_cache_get( 'test', $this->post_id ) );

		global $current_screen;
		$current_screen = WP_Screen::get( 'admin_init' );

		papi_cache_delete( 'test', $this->post_id );
		$this->assertEmpty( papi_cache_get( 'test', $this->post_id ) );

		$current_screen = null;
	}

	public function test_papi_cache_key() {
		$this->assertEmpty( papi_cache_key( 0, 1 ) );
		$this->assertEmpty( papi_cache_key( [], 'hello' ) );
		$this->assertEmpty( papi_cache_key( (object) [], 230 ) );
		$this->assertEmpty( papi_cache_key( true, 'false' ) );
		$this->assertEmpty( papi_cache_key( false, 'true' ) );
		$this->assertEmpty( papi_cache_key( null, 2 ) );

		global $post;
		$post_id = $this->factory->post->create();
		$post = get_post( $post_id );
		$this->assertSame( 'papi_post_page_' . $post_id, papi_cache_key( 'page', $post_id ) );
		$this->assertSame( 'papi_post_page_' . $post_id, papi_cache_key( 'papi_page', $post_id ) );
		$this->assertSame( 'papi_post_page_920', papi_cache_key( 'page', 920 ) );
		unset( $post );
	}

	public function test_papi_cache_set() {
		papi_cache_set( 'set', $this->post_id, 'elli' );
		$this->assertSame( 'elli', papi_cache_get( 'set', $this->post_id ) );
	}

	public function test_papi_cache_set_admin() {
		global $current_screen;
		$current_screen = WP_Screen::get( 'admin_init' );

		papi_cache_set( 'test', $this->post_id, 'elli' );
		$this->assertSame( 'elli', papi_cache_get( 'test', $this->post_id ) );

		$current_screen = null;

		papi_cache_delete( 'test', $this->post_id );
		$this->assertEmpty( papi_cache_get( 'test', $this->post_id ) );

		global $current_screen;
		$current_screen = WP_Screen::get( 'admin_init' );

		papi_cache_delete( 'test', $this->post_id );
		$this->assertEmpty( papi_cache_get( 'test', $this->post_id ) );

		$current_screen = null;
	}
}
