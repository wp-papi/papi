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
}
