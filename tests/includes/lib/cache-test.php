<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering cache functions.
 *
 * @package Papi
 */
class Papi_Lib_Cache_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->post_id = $this->factory->post->create();

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );
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
			'post_id'       => $this->post_id,
			'slug'          => 'namn',
			'value'         => 'fredrik'
		] );
		$this->assertEquals( 'fredrik', papi_get_field( $this->post_id, 'namn' ) );

		papi_update_property_meta_value( [
			'post_id'       => $this->post_id,
			'slug'          => 'namn',
			'value'         => 'elli'
		] );
		$this->assertEquals( 'elli', papi_get_field( $this->post_id, 'namn' ) );
	}

    public function test_papi_cache_get() {
        papi_cache_set( 'get', $this->post_id, 'elli' );
        $this->assertSame( 'elli', papi_cache_get( 'get', $this->post_id ) );
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
		$this->assertEquals( 'papi_page_' . $post_id, papi_cache_key( 'page', $post_id ) );
		$this->assertEquals( 'papi_page_920', papi_cache_key( 'page', 920 ) );
		unset( $post );
	}

    public function test_papi_cache_set() {
        papi_cache_set( 'set', $this->post_id, 'elli' );
        $this->assertSame( 'elli', papi_cache_get( 'set', $this->post_id ) );
    }

}
