<?php

class Papi_Lib_Types_Taxonomy_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
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

	public function test_papi_get_page_type_id_container() {
		$_GET['taxonomy'] = 'post_tag';
		papi()->bind( 'entry_type_id.taxonomy.post_tag', 'properties-taxonomy-type' );
		$this->assertSame( 'properties-taxonomy-type', papi_get_taxonomy_type_id( 0 ) );
		papi()->remove( 'entry_type_id.taxonomy.post_tag' );
		unset( $_GET['taxonomy'] );
	}

	public function test_papi_get_entry_type_by_id() {
		$this->assertNull( papi_get_entry_type_by_id( 0 ) );
		$this->assertNull( papi_get_entry_type_by_id( [] ) );
		$this->assertNull( papi_get_entry_type_by_id( (object) [] ) );
		$this->assertNull( papi_get_entry_type_by_id( true ) );
		$this->assertNull( papi_get_entry_type_by_id( false ) );
		$this->assertNull( papi_get_entry_type_by_id( null ) );
		$this->assertNull( papi_get_entry_type_by_id( 'page' ) );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );

		$this->assertInstanceOf( 'Papi_Taxonomy_Type', papi_get_entry_type_by_id( 'properties-taxonomy-type' ) );
	}

	public function test_papi_is_taxonomy_type() {
		$this->assertTrue( papi_is_taxonomy_type( new Papi_Taxonomy_Type ) );
		$this->assertFalse( papi_is_taxonomy_type( new Papi_Page_Type ) );
		$this->assertFalse( papi_is_taxonomy_type( true ) );
		$this->assertFalse( papi_is_taxonomy_type( false ) );
		$this->assertFalse( papi_is_taxonomy_type( null ) );
		$this->assertFalse( papi_is_taxonomy_type( 1 ) );
		$this->assertFalse( papi_is_taxonomy_type( 0 ) );
		$this->assertFalse( papi_is_taxonomy_type( '' ) );
		$this->assertFalse( papi_is_taxonomy_type( [] ) );
		$this->assertFalse( papi_is_taxonomy_type( (object) [] ) );
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

	public function test_papi_is_taxonomy_page() {
		$_SERVER['REQUEST_URI'] = '';
		$this->assertFalse( papi_is_taxonomy_page() );

		$_SERVER['REQUEST_URI'] = 'http://wordpress/wp-admin/?taxonomy=post_tag';
		$this->assertFalse( papi_is_taxonomy_page() );

		global $current_screen;
		$current_screen = WP_Screen::get( 'admin_init' );

		$_SERVER['REQUEST_URI'] = 'http://wordpress/wp-admin/?taxonomy=post_tag';
		$this->assertTrue( papi_is_taxonomy_page() );

		$_SERVER['REQUEST_URI'] = '';
		$current_screen = null;
	}
}
