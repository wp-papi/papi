<?php

/**
 * Unit tests covering field functionality.
 *
 * @package Papi
 */

class WP_Papi_Functions_Field extends WP_UnitTestCase {

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
	 * Test papi_field.
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

}
