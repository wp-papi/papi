<?php

class Papi_Lib_Fields_Taxonomy_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		if ( ! papi_supports_term_meta() ) {
			$this->markTestSkipped( 'Term metadata is not supported' );
		}

		$_GET = [];

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/taxonomy-types'];
		} );

		$this->term_id = $this->factory->term->create();

		update_term_meta( $this->term_id, papi_get_page_type_key(), 'properties-taxonomy-type' );
	}

	public function tearDown() {
		parent::tearDown();
		unset( $_GET, $this->term_id );
	}

	public function test_papi_delete_term_field() {
		$this->assertFalse( papi_delete_term_field( 1 ) );
		$this->assertFalse( papi_delete_term_field( null ) );
		$this->assertFalse( papi_delete_term_field( true ) );
		$this->assertFalse( papi_delete_term_field( false ) );
		$this->assertFalse( papi_delete_term_field( [] ) );
		$this->assertFalse( papi_delete_term_field( (object) [] ) );
		$this->assertFalse( papi_delete_term_field( '' ) );
		$this->assertFalse( papi_delete_term_field( 0, 'fake_slug' ) );
		$this->assertFalse( papi_delete_term_field( 'fake_slug' ) );
		$this->assertFalse( papi_delete_term_field( 93099, 'fake_slug' ) );

		update_term_meta( $this->term_id, 'string_test', 'brunnsgatan' );
		$this->assertTrue( papi_delete_term_field( $this->term_id, 'string_test' ) );

		$_GET['term_id'] = $this->term_id;
		update_term_meta( $this->term_id, 'string_test', 'brunnsgatan' );
		$this->assertTrue( papi_delete_term_field( 'string_test' ) );
		unset( $_GET['term_id'] );

		$this->assertFalse( papi_delete_term_field( $this->term_id, 'fake' ) );
		$this->assertFalse( papi_delete_term_field( 0, 'fake' ) );
	}

	public function test_papi_get_term_field() {
		update_term_meta( $this->term_id, 'string_test', 'fredrik' );

		$this->assertNull( papi_get_term_field( 1 ) );
		$this->assertNull( papi_get_term_field( null ) );
		$this->assertNull( papi_get_term_field( true ) );
		$this->assertNull( papi_get_term_field( false ) );
		$this->assertNull( papi_get_term_field( [] ) );
		$this->assertNull( papi_get_term_field( (object) [] ) );
		$this->assertNull( papi_get_term_field( '' ) );
		$this->assertNull( papi_get_term_field( $this->term_id, '' ) );

		$this->assertSame( 'fredrik', papi_get_term_field( $this->term_id, 'string_test' ) );
		$this->assertSame( 'fredrik', papi_get_term_field( $this->term_id, 'string_test', '', 'post' ) );

		$this->assertSame( 'world', papi_get_term_field( $this->term_id, 'hello', 'world' ) );

		$_GET['term_id'] = 0;
		$this->assertNull( papi_get_term_field( 'string_test' ) );
		$this->assertSame( 'fredrik', papi_get_term_field( '', 'fredrik' ) );
	}

	public function test_papi_get_term_slugs() {
		$this->assertEmpty( papi_get_term_slugs() );

		update_term_meta( $this->term_id, papi_get_page_type_key(), 'simple-taxonomy-type' );
		$actual = papi_get_term_slugs( $this->term_id );

		$this->assertTrue( ! empty( $actual ) );
		$this->assertTrue( is_array( $actual ) );

		$slugs = papi_get_term_slugs( $this->term_id, true );
		$this->assertTrue( array_filter( $slugs, 'is_string' ) === $slugs );

		update_term_meta( $this->term_id, papi_get_page_type_key(), '' );
		$this->flush_cache();
		$this->assertEmpty( papi_get_term_slugs() );

		update_term_meta( $this->term_id, papi_get_page_type_key(), 'empty-taxonomy-type' );
		$this->flush_cache();
		$this->assertEmpty( papi_get_term_slugs() );
	}

	public function test_papi_taxonomy_shortcode() {
		update_term_meta( $this->term_id, 'string_test', 'fredrik' );

		$this->assertEmpty( papi_taxonomy_shortcode( [] ) );
		$this->assertSame( 'fredrik', papi_taxonomy_shortcode( [
			'id'   => $this->term_id,
			'slug' => 'string_test'
		] ) );

		$this->assertSame( '1, 2, 3', papi_taxonomy_shortcode( [
			'slug'    => 'numbers',
			'default' => [1, 2, 3]
		] ) );

		$this->assertSame( '1, 2, 3', papi_taxonomy_shortcode( [
			'id'      => $this->term_id,
			'slug'    => 'numbers',
			'default' => [1, 2, 3]
		] ) );

		$_GET['term_id'] = $this->term_id;

		$this->assertSame( 'fredrik', papi_taxonomy_shortcode( [
			'slug' => 'string_test'
		] ) );
	}

	public function test_papi_update_term_field() {
		$this->assertFalse( papi_update_term_field( 1 ) );
		$this->assertFalse( papi_update_term_field( null ) );
		$this->assertFalse( papi_update_term_field( true ) );
		$this->assertFalse( papi_update_term_field( false ) );
		$this->assertFalse( papi_update_term_field( [] ) );
		$this->assertFalse( papi_update_term_field( (object) [] ) );
		$this->assertFalse( papi_update_term_field( '' ) );
		$this->assertFalse( papi_update_term_field( 0, 'fake_slug' ) );
		$this->assertFalse( papi_update_term_field( 0, 'fake_slug', 'value' ) );
		$this->assertFalse( papi_update_term_field( 'fake_slug', 'value' ) );
		$this->assertFalse( papi_update_term_field( 93099, 'fake_slug', 'value' ) );
		$this->assertFalse( papi_update_term_field( $this->term_id, 'fake_slug', 'value' ) );
		$this->assertTrue( papi_update_term_field( $this->term_id, 'string_test', 'Kalle' ) );
		$this->assertSame( 'Kalle', papi_get_term_field( $this->term_id, 'string_test' ) );
	}

	public function test_the_papi_term_meta() {
		update_term_meta( $this->term_id, 'string_test', 'fredrik' );

		the_papi_term_meta( $this->term_id, 'string_test' );
		$this->expectOutputRegex( '/fredrik/' );

		the_papi_term_meta( $this->term_id, 'numbers', [ 1, 2, 3 ] );
		$this->expectOutputRegex( '/1\, 2\, 3/' );
	}
}
