<?php

class Papi_Lib_Types_Taxonomy_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		if ( ! papi_supports_term_meta() ) {
			$this->markTestSkipped( 'Term metadata is not supported' );
		}

		$this->term_id = $this->factory->term->create();
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->term_id );
	}

	public function test_papi_get_taxonomy_type_id_meta_value() {
		$this->assertEmpty( papi_get_taxonomy_type_id() );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );

		update_term_meta( $this->term_id, papi_get_page_type_key(), 'properties-taxonomy-type' );
		$this->assertSame( 'properties-taxonomy-type', papi_get_taxonomy_type_id( $this->term_id ) );
		delete_term_meta( $this->term_id, papi_get_page_type_key() );
	}

	public function test_papi_get_taxonomy_type_id_container() {
		$_GET['taxonomy'] = 'post_tag';
		papi()->bind( 'entry_type_id.taxonomy.post_tag', 'properties-taxonomy-type' );
		$this->assertSame( 'properties-taxonomy-type', papi_get_taxonomy_type_id( 0 ) );
		papi()->remove( 'entry_type_id.taxonomy.post_tag' );
		unset( $_GET['taxonomy'] );
	}

	public function test_papi_get_taxonomy_type_name() {
		$this->assertEmpty( papi_get_taxonomy_type_name() );
		$this->assertEmpty( papi_get_taxonomy_type_name( null ) );
		$this->assertEmpty( papi_get_taxonomy_type_name( 0 ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );

		update_term_meta( $this->term_id, papi_get_page_type_key(), 'simple-taxonomy-type' );

		$_GET['term_id'] = $this->term_id;

		$this->assertSame( 'Simple taxonomy', papi_get_taxonomy_type_name() );
		$this->assertSame( 'Simple taxonomy', papi_get_taxonomy_type_name( $this->term_id ) );

		update_term_meta( $this->term_id, papi_get_page_type_key(), 'simple-taxonomy-type2' );

		$this->assertEmpty( papi_get_taxonomy_type_name( $this->term_id ) );
	}

	public function test_papi_get_taxonomies() {
		papi()->remove( 'papi_get_all_core_type_files' );

		$this->assertEmpty( papi_get_taxonomies() );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );

		papi()->remove( 'papi_get_all_core_type_files' );

		$taxonomies = papi_get_taxonomies();

		$this->assertTrue( in_array( 'post_tag', $taxonomies ) );
	}

	public function test_papi_load_taxonomy_type_id() {
		$this->assertEmpty( papi_load_taxonomy_type_id() );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );

		$_GET['term_id'] = $this->term_id;
		update_term_meta( $this->term_id, papi_get_page_type_key(), 'properties-taxonomy-type' );
		$this->assertSame( 'properties-taxonomy-type', papi_load_taxonomy_type_id() );
		delete_term_meta( $this->term_id, papi_get_page_type_key() );
		unset( $_GET['term_id'] );

		$_GET['taxonomy'] = 'post_tag';
		papi()->bind( 'entry_type_id.taxonomy.post_tag', 'properties-taxonomy-type' );
		$this->assertSame( 'properties-taxonomy-type', papi_load_taxonomy_type_id() );
		papi()->remove( 'entry_type_id.taxonomy.post_tag' );
		unset( $_GET['taxonomy'] );
	}

	public function test_papi_set_taxonomy_type_id() {
		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );

		$this->assertFalse( papi_set_taxonomy_type_id( 0, 'hello' ) );
		$term_id = $this->factory->term->create();
		$this->assertFalse( papi_set_taxonomy_type_id( $term_id, 'hello' ) );
		$this->assertNotFalse( papi_set_taxonomy_type_id( $term_id, 'simple-taxonomy-type' ) );
		$this->assertSame( 'simple-taxonomy-type', papi_get_taxonomy_type_id( $term_id ) );
	}

	public function test_the_papi_taxonomy_type_name() {
		the_papi_taxonomy_type_name();
		$this->expectOutputRegex( '//' );

		the_papi_taxonomy_type_name( null );
		$this->expectOutputRegex( '//' );

		the_papi_taxonomy_type_name( 0 );
		$this->expectOutputRegex( '//' );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );

		update_post_meta( $this->term_id, papi_get_page_type_key(), 'simple-taxonomy-type' );

		$_GET['term_id'] = $this->term_id;
		the_papi_taxonomy_type_name();
		$this->expectOutputRegex( '/Simple\staxonomy/' );

		the_papi_taxonomy_type_name( $this->term_id );
		$this->expectOutputRegex( '/Simple\staxonomy/' );

		update_post_meta( $this->term_id, papi_get_page_type_key(), '' );
		the_papi_taxonomy_type_name();
		$this->expectOutputRegex( '//' );

		update_post_meta( $this->term_id, papi_get_page_type_key(), 'random322-page-type' );
		the_papi_taxonomy_type_name();
		$this->expectOutputRegex( '//' );
	}
}
