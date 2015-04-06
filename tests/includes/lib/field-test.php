<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering field functionality.
 *
 * @package Papi
 */

class Papi_Lib_Field_Test extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 *
	 * @since 1.0.0
	 */

	public function setUp() {
		parent::setUp();
		$this->post_id = $this->factory->post->create();
	}

	/**
	 * Tear down test.
	 *
	 * @since 1.3.0
	 */

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id );
	}

	/**
	 * Test `papi_field` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_field() {
		update_post_meta( $this->post_id, 'name', 'fredrik' );
		update_post_meta( $this->post_id, '_name_property', 'string' );

		$this->assertEquals( 'fredrik', papi_field( $this->post_id, 'name' ) );
		$this->assertEquals( 'fredrik', papi_field( $this->post_id, 'name', '', true ) );
		$this->assertEquals( 'string', get_post_meta( $this->post_id, '_name_property', true ) );

		$this->assertEquals( 'world', papi_field( $this->post_id, 'hello', 'world' ) );

		$this->assertNull( papi_field( 'name' ) );
		$this->assertEquals( 'fredrik', papi_field( '', 'fredrik' ) );
	}

	/**
	 * Test `papi_fields` function.
	 *
	 * @since 1.2.0
	 */

	public function test_papi_fields() {
		$this->assertEmpty( papi_fields() );

		global $post;

		$post = get_post( $this->post_id );

		tests_add_filter( 'papi/settings/directories', function () {
			return array( 1,  papi_test_get_fixtures_path( '/page-types' ) );
		} );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );
		$this->assertTrue( ! empty ( papi_fields() ) && is_array( papi_fields() ) );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, '' );
		$this->assertEmpty( papi_fields() );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'empty-page-type' );
		$this->assertEmpty( papi_fields() );

	}

	/**
	 * Test `papi_field_value` function.
	 *
	 * @since 1.3.0
	 */

	public function test_papi_field_value() {
		$this->assertEquals( 'fredrik', papi_field_value(
			array( 'what', 'name' ),
			array( 'what' => array( 'name' => 'fredrik' ) )
		) );
		$this->assertEquals( 'fredrik', papi_field_value(
			array( 'what', 'name' ),
			(object) array( 'what' => array( 'name' => 'fredrik' ) )
		) );
	}

	/**
	 * Test `papi_field_shortcode` function.
	 *
	 * @since 1.3.0
	 */

	public function test_papi_field_shortcode() {
		update_post_meta( $this->post_id, 'name', 'fredrik' );
		update_post_meta( $this->post_id, '_name_property', 'string' );

		$this->assertEmpty( papi_field_shortcode( array( ) ) );
		$this->assertEquals( 'fredrik', papi_field_shortcode( array(
			'id'   => $this->post_id,
			'name' => 'name'
		) ) );

		global $post;

		$post = get_post( $this->post_id );

		$this->assertEquals( 'fredrik', papi_field_shortcode( array(
			'name' => 'name'
		) ) );
	}

	/**
	 * Test `the_papi_field` function.
	 *
	 * @since 1.3.0
	 */

	public function test_the_papi_field() {
		update_post_meta( $this->post_id, 'name', 'fredrik' );
		update_post_meta( $this->post_id, '_name_property', 'string' );

		the_papi_field( $this->post_id, 'name' );
		$this->expectOutputRegex( '/fredrik/' );

		the_papi_field( $this->post_id, 'numbers', array( 1, 2, 3 ) );
		$this->expectOutputRegex( '/1\,2\,3/' );
	}

}
