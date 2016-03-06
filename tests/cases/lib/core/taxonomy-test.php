<?php

class Papi_Lib_Core_Taxonomy_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		if ( ! papi_supports_term_meta() ) {
			$this->markTestSkipped( 'Term metadata is not supported' );
		}
	}

	public function test_papi_get_term_id() {
		$this->assertSame( 1, papi_get_term_id( 1 ) );
		$this->assertSame( 1, papi_get_term_id( '1' ) );

		$term_id = $this->factory->term->create();

		$term = get_term( $term_id );
		$this->assertSame( $term_id, papi_get_term_id( $term ) );

		$_GET['term_id'] = $term_id;
		$this->assertSame( $term_id, papi_get_term_id() );
		$this->assertSame( $term_id, papi_get_term_id( null ) );
		unset( $_GET['term_id'] );

		$_GET['tag_ID'] = $term_id;
		$this->assertSame( $term_id, papi_get_term_id() );
		$this->assertSame( $term_id, papi_get_term_id( null ) );
		unset( $_GET['tag_ID'] );
	}

	public function test_papi_get_term_id_wp_query() {
		global $wp_query;

		$term_id = $this->factory->term->create(['slug' => 'category_test']);
		$term = get_term_by('slug', 'category_test', 'post_tag');
		$this->go_to( get_term_link( $term ) );
		$this->assertSame( $term_id, papi_get_term_id() );
	}

	public function test_papi_get_taxonomy() {
		$this->assertSame( '', papi_get_taxonomy() );

		$_GET['taxonomy'] = 'post_tag';
		$this->assertSame( 'post_tag', papi_get_taxonomy() );
		$this->assertSame( 'post_tag', papi_get_taxonomy( null ) );
		unset( $_GET['taxonomy'] );

		$_GET['term_id'] = $this->factory->term->create();
		$this->assertSame( get_term( $_GET['term_id'] )->taxonomy, papi_get_taxonomy() );
		unset( $_GET['term_id'] );
	}

	public function test_papi_supports_term_meta() {
		if ( function_exists( 'get_term_meta' ) ) {
			$this->assertTrue( papi_supports_term_meta() );
		} else {
			$this->assertFalse( papi_supports_term_meta() );
		}
	}
}
