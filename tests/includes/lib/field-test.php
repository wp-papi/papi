<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering field functions.
 *
 * @package Papi
 */

class Papi_Lib_Field_Test extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 */

	public function setUp() {
		parent::setUp();
		$this->post_id = $this->factory->post->create();
	}

	/**
	 * Tear down test.
	 */

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id );
	}

	/**
	 * Test `papi_field` function.
	 */

	public function test_papi_field() {
		update_post_meta( $this->post_id, 'name', 'fredrik' );
		update_post_meta( $this->post_id, '_name_property', 'string' );

		$this->assertEquals( 'fredrik', papi_field( $this->post_id, 'name' ) );
		$this->assertEquals( 'fredrik', papi_field( $this->post_id, 'name', '', 'post' ) );
		$this->assertEquals( 'string', get_post_meta( $this->post_id, '_name_property', 'post' ) );

		$this->assertEquals( 'world', papi_field( $this->post_id, 'hello', 'world' ) );

		$this->assertNull( papi_field( 'name' ) );
		$this->assertEquals( 'fredrik', papi_field( '', 'fredrik' ) );
	}

	/**
	 * Test `papi_fields` function.
	 */

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

	/**
	 * Test `papi_field_value` function.
	 */

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

	/**
	 * Test `papi_field_shortcode` function.
	 */

	public function test_papi_field_shortcode() {
		update_post_meta( $this->post_id, 'name', 'fredrik' );
		update_post_meta( $this->post_id, '_name_property', 'string' );

		$this->assertEmpty( papi_field_shortcode( [] ) );
		$this->assertEquals( 'fredrik', papi_field_shortcode( [
			'id'   => $this->post_id,
			'name' => 'name'
		] ) );

		global $post;

		$post = get_post( $this->post_id );

		$this->assertEquals( 'fredrik', papi_field_shortcode( [
			'name' => 'name'
		] ) );
	}

	/**
	 * Test `the_papi_field` function.
	 */

	public function test_the_papi_field() {
		update_post_meta( $this->post_id, 'name', 'fredrik' );
		update_post_meta( $this->post_id, '_name_property', 'string' );

		the_papi_field( $this->post_id, 'name' );
		$this->expectOutputRegex( '/fredrik/' );

		the_papi_field( $this->post_id, 'numbers', [ 1, 2, 3 ] );
		$this->expectOutputRegex( '/1\,2\,3/' );
	}

}
