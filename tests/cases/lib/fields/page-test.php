<?php

class Papi_Lib_Fields_Page_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		$_GET = [];

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->post_id = $this->factory->post->create();

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'simple-page-type' );
	}

	public function tearDown() {
		parent::tearDown();
		unset( $_GET, $this->post_id );
	}

	public function test_papi_delete_field() {
		$this->assertFalse( papi_delete_field( 1 ) );
		$this->assertFalse( papi_delete_field( null ) );
		$this->assertFalse( papi_delete_field( true ) );
		$this->assertFalse( papi_delete_field( false ) );
		$this->assertFalse( papi_delete_field( [] ) );
		$this->assertFalse( papi_delete_field( (object) [] ) );
		$this->assertFalse( papi_delete_field( '' ) );
		$this->assertFalse( papi_delete_field( 0, 'fake_slug' ) );
		$this->assertFalse( papi_delete_field( 'fake_slug' ) );
		$this->assertFalse( papi_delete_field( 93099, 'fake_slug' ) );

		update_post_meta( $this->post_id, 'name', 'brunnsgatan' );
		$this->assertTrue( papi_delete_field( $this->post_id, 'name' ) );

		$_GET['page_id'] = $this->post_id;
		update_post_meta( $this->post_id, 'name', 'brunnsgatan' );
		$this->assertTrue( papi_delete_field( 'name' ) );
		unset( $_GET['page_id'] );

		$this->assertFalse( papi_delete_field( $this->post_id, 'fake' ) );
		$this->assertFalse( papi_delete_field( 0, 'fake' ) );
	}

	public function test_papi_get_field() {
		update_post_meta( $this->post_id, 'name', 'fredrik' );

		$this->assertNull( papi_get_field( 1 ) );
		$this->assertNull( papi_get_field( null ) );
		$this->assertNull( papi_get_field( true ) );
		$this->assertNull( papi_get_field( false ) );
		$this->assertNull( papi_get_field( [] ) );
		$this->assertNull( papi_get_field( (object) [] ) );
		$this->assertNull( papi_get_field( '' ) );
		$this->assertNull( papi_get_field( $this->post_id, '' ) );
		$this->assertNull( papi_get_field( 99999, 'fake' ) );

		$this->assertSame( 'fredrik', papi_get_field( $this->post_id, 'name' ) );
		$this->assertSame( 'fredrik', papi_get_field( $this->post_id, 'name', '', 'post' ) );

		$this->assertSame( 'world', papi_get_field( $this->post_id, 'hello', 'world' ) );

		$_GET['post_id'] = $this->post_id;
		$this->assertNull( papi_get_field( 'name' ) );
		$this->assertSame( 'fredrik', papi_get_field( '', 'fredrik' ) );

		update_post_meta( $this->post_id, 'uppercase', 'fredrik' );
		$this->assertSame( 'fredrik', papi_get_field( $this->post_id, 'UPPERCASE' ) );
	}

	public function test_papi_get_field_cache() {
		papi_data_update( $this->post_id, 'name', 'fredrik' );

		$this->assertSame( 'fredrik', papi_get_field( $this->post_id, 'name' ) );
		$this->assertSame( 'fredrik', papi_cache_get( 'name', $this->post_id ) );

		// Turn off property cache.
		add_filter( 'papi/get_property', function ( $property ) {
			$property->set_option( 'cache', false );
			return $property;
		} );

		$this->assertSame( 'fredrik', papi_get_field( $this->post_id, 'name' ) );
		$this->assertEmpty( papi_cache_get( 'name', $this->post_id ) );
	}

	public function test_papi_get_slugs() {
		$this->assertEmpty( papi_get_slugs() );

		global $post;

		$post = get_post( $this->post_id );

		add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'simple-page-type' );
		$actual = papi_get_slugs( $this->post_id );

		$this->assertTrue( ! empty( $actual ) );
		$this->assertTrue( is_array( $actual ) );

		$slugs = papi_get_slugs( $this->post_id, true );
		$this->assertTrue( array_filter( $slugs, 'is_string' ) === $slugs );

		$_GET['post_id'] = $this->post_id;
		$slugs = papi_get_slugs( true );
		$this->assertTrue( array_filter( $slugs, 'is_string' ) === $slugs );
		unset( $_GET['post_id'] );

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'empty-page-type' );
		$this->flush_cache();
		$this->assertEmpty( papi_get_slugs() );
	}

	public function test_papi_field_value() {
		$this->assertSame( 'fredrik', papi_field_value(
			[ 'what', 'name' ],
			[ 'what' => [ 'name' => 'fredrik' ] ]
		) );
		$this->assertSame( 'fredrik', papi_field_value(
			[ 'what', 'name' ],
			(object) [ 'what' => [ 'name' => 'fredrik' ] ]
		) );
	}

	public function test_papi_field_shortcode() {
		update_post_meta( $this->post_id, 'name', 'fredrik' );

		$this->assertEmpty( papi_field_shortcode( [] ) );
		$this->assertSame( 'fredrik', papi_field_shortcode( [
			'id'   => $this->post_id,
			'slug' => 'name'
		] ) );

		$this->assertSame( '1, 2, 3', papi_field_shortcode( [
			'slug'    => 'numbers',
			'default' => [1, 2, 3]
		] ) );

		$this->assertSame( '1, 2, 3', papi_field_shortcode( [
			'id'      => $this->post_id,
			'slug'    => 'numbers',
			'default' => [1, 2, 3]
		] ) );

		global $post;

		$post = get_post( $this->post_id );

		$this->assertSame( 'fredrik', papi_field_shortcode( [
			'slug' => 'name'
		] ) );
	}

	public function test_papi_update_field() {
		$this->assertFalse( papi_update_field( 1 ) );
		$this->assertFalse( papi_update_field( null ) );
		$this->assertFalse( papi_update_field( true ) );
		$this->assertFalse( papi_update_field( false ) );
		$this->assertFalse( papi_update_field( [] ) );
		$this->assertFalse( papi_update_field( (object) [] ) );
		$this->assertFalse( papi_update_field( '' ) );
		$this->assertFalse( papi_update_field( 0, 'fake_slug' ) );
		$this->assertFalse( papi_update_field( 0, 'fake_slug', 'value' ) );
		$this->assertFalse( papi_update_field( 'fake_slug', 'value' ) );
		$this->assertFalse( papi_update_field( 93099, 'fake_slug', 'value' ) );
		$this->assertFalse( papi_update_field( $this->post_id, 'fake_slug', 'value' ) );
		$this->assertTrue( papi_update_field( $this->post_id, 'name', 'Kalle' ) );
		$this->assertSame( 'Kalle', papi_get_field( $this->post_id, 'name' ) );
	}

	public function test_the_papi_field() {
		update_post_meta( $this->post_id, 'name', 'fredrik' );

		the_papi_field( $this->post_id, 'name' );
		$this->expectOutputRegex( '/fredrik/' );

		the_papi_field( $this->post_id, 'numbers', [ 1, 2, 3 ] );
		$this->expectOutputRegex( '/1\, 2\, 3/' );
	}
}
