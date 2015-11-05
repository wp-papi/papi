<?php

class Papi_Lib_Core_Page_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->post_id = $this->factory->post->create();
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id );
	}

	public function test_papi_get_page() {
		$page = papi_get_page( $this->post_id );
		$this->assertTrue( is_object( $page ) );
		$page = papi_get_page( $this->post_id, 'fake' );
		$this->assertNull( $page );
	}

	public function test_papi_get_post_types() {
		$actual = papi_get_post_types();

		foreach ( $actual as $key => $value ) {
			if ( $value !== 'page' ) {
				unset( $actual[$key] );
			}
		}

		$this->assertSame( ['page'], array_values( $actual ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$post_types = papi_get_post_types();

		$this->assertTrue( in_array( 'page', $post_types ) );
	}

	public function test_papi_get_slugs() {
		$this->assertEmpty( papi_get_slugs() );

		global $post;

		$post = get_post( $this->post_id );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );
		$actual = papi_get_slugs( $this->post_id );

		$this->assertTrue( ! empty( $actual ) );
		$this->assertTrue( is_array( $actual ) );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, '' );
		$this->flush_cache();
		$this->assertEmpty( papi_get_slugs() );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'empty-page-type' );
		$this->flush_cache();
		$this->assertEmpty( papi_get_slugs() );
	}
}
