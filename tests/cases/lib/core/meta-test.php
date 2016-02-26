<?php

class Papi_Lib_Core_Meta_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->post_id = $this->factory->post->create();
		$this->term_id = $this->factory->term->create();
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id, $this->term_id );
	}

	public function test_papi_get_meta_id() {
		$this->assertSame( 'post_id', papi_get_meta_id() );
		$this->assertSame( 'post_id', papi_get_meta_id( 'post' ) );
		$this->assertSame( 'post_id', papi_get_meta_id( 'page' ) );
		$this->assertSame( 'term_id', papi_get_meta_id( 'term' ) );
		$this->assertSame( 'term_id', papi_get_meta_id( 'taxonomy' ) );
		$this->assertNull( papi_get_meta_id( 'hello' ) );
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
	}
}
