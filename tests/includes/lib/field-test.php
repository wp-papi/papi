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
		add_post_meta( $this->post_id, 'name', 'fredrik' );
		add_post_meta( $this->post_id, '_name_property', 'string' );

		$this->assertEquals( 'fredrik', papi_field( $this->post_id, 'name' ) );
		$this->assertEquals( 'string', get_post_meta( $this->post_id, '_name_property', true ) );

		$this->assertEquals( 'world', papi_field( $this->post_id, 'hello', 'world' ) );
	}

	/**
	 * Test `papi_fields` function.
	 *
	 * @since 1.2.0
	 */

	public function test_current_properties() {
		$actual = papi_fields();
		$this->assertEmpty( $actual );
	}

}
