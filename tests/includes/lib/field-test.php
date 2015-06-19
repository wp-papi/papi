<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering field functions.
 *
 * @package Papi
 */

class Papi_Lib_Field_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		$_GET = [];

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->post_id = $this->factory->post->create();

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );
	}

	public function tearDown() {
		parent::tearDown();
		unset( $_GET, $this->post_id );
	}

	public function test_papi_delete_field() {
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

		$this->assertNull( papi_get_field( '' ) );
		$this->assertNull( papi_get_field( $this->post_id, '' ) );

		$this->assertEquals( 'fredrik', papi_get_field( $this->post_id, 'name' ) );
		$this->assertEquals( 'fredrik', papi_get_field( $this->post_id, 'name', '', 'post' ) );

		$this->assertEquals( 'world', papi_get_field( $this->post_id, 'hello', 'world' ) );

		$_GET['post_id'] = $this->post_id;
		$this->assertNull( papi_get_field( 'name' ) );
		$this->assertEquals( 'fredrik', papi_get_field( '', 'fredrik' ) );
	}

	public function test_papi_fields() {
		$this->assertEmpty( papi_fields() );

		global $post;

		$post = get_post( $this->post_id );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );
		$actual = papi_fields( $this->post_id );

		$this->assertTrue( ! empty( $actual ) );
		$this->assertTrue( is_array( $actual ) );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, '' );
		$this->flush_cache();
		$this->assertEmpty( papi_fields() );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'empty-page-type' );
		$this->flush_cache();
		$this->assertEmpty( papi_fields() );
	}

	public function test_papi_field_value() {
		$this->assertEquals( 'fredrik', papi_field_value(
			[ 'what', 'name' ],
			[ 'what' => [ 'name' => 'fredrik' ] ]
		) );
		$this->assertEquals( 'fredrik', papi_field_value(
			[ 'what', 'name' ],
			(object) [ 'what' => [ 'name' => 'fredrik' ] ]
		) );
	}

	public function test_papi_field_shortcode() {
		update_post_meta( $this->post_id, 'name', 'fredrik' );

		$this->assertEmpty( papi_field_shortcode( [] ) );
		$this->assertEquals( 'fredrik', papi_field_shortcode( [
			'id'   => $this->post_id,
			'slug' => 'name'
		] ) );

		$this->assertEquals( '1, 2, 3', papi_field_shortcode( [
			'slug'    => 'numbers',
			'default' => [1, 2, 3]
		] ) );

		$this->assertEquals( '1, 2, 3', papi_field_shortcode( [
			'id'      => $this->post_id,
			'slug'    => 'numbers',
			'default' => [1, 2, 3]
		] ) );

		global $post;

		$post = get_post( $this->post_id );

		$this->assertEquals( 'fredrik', papi_field_shortcode( [
			'slug' => 'name'
		] ) );
	}

	public function test_papi_update_field() {
		$this->assertFalse( papi_update_field( 0, 'fake_slug' ) );
		$this->assertFalse( papi_update_field( 0, 'fake_slug', 'value' ) );
		$this->assertFalse( papi_update_field( 'fake_slug', 'value' ) );
		$this->assertFalse( papi_update_field( 93099, 'fake_slug', 'value' ) );
		$this->assertFalse( papi_update_field( $this->post_id, 'fake_slug', 'value' ) );
		$this->assertTrue( papi_update_field( $this->post_id, 'name', 'Kalle' ) );
		$this->assertEquals( 'Kalle', papi_get_field( $this->post_id, 'name' ) );
	}

	public function test_the_papi_field() {
		update_post_meta( $this->post_id, 'name', 'fredrik' );

		the_papi_field( $this->post_id, 'name' );
		$this->expectOutputRegex( '/fredrik/' );

		the_papi_field( $this->post_id, 'numbers', [ 1, 2, 3 ] );
		$this->expectOutputRegex( '/1\, 2\, 3/' );
	}

}
