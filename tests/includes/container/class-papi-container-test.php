<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Container` class.
 *
 * @package Papi
 */

class Papi_Container_Test extends WP_UnitTestCase {

	/**
	 * Setup test.
	 *
	 * @since 1.3.0
	 */

	public function setUp() {
		parent::setUp();
		$this->container = new Papi_Container;
	}

	/**
	 * Tear down test.
	 *
	 * @since 1.3.0
	 */

	public function tearDown() {
		parent::tearDown();
		unset( $this->container );
	}

	/**
	 * Test `make` method.
	 *
	 * @since 1.3.0
	 */

	public function test_make_not_defined() {
		$this->setExpectedException( 'InvalidArgumentException', 'Identifier [fredrik] is not defined' );
		$this->container->make( 'fredrik' );
	}

	/**
	 * Test `bind` method.
	 *
	 * @since 1.3.0
	 */

	public function test_bind() {
		$this->container->bind( 'name', 'Fredrik' );
		$this->assertEquals( 'Fredrik', $this->container->make( 'name' ) );
	}

	/**
	 * Test `exists` method.
	 *
	 * @since 1.3.0
	 */

	public function test_exists() {
		$this->container->bind( 'name', 'Fredrik' );
		$this->assertTrue( $this->container->exists( 'name' ) );
	}

	/**
	 * Test `offsetExists` method.
	 *
	 * @since 1.3.0
	 */

	public function test_offset_exists() {
		$this->container->bind( 'name', 'Fredrik' );
		$this->assertTrue( isset( $this->container['name'] ) );
	}

	/**
	 * Test `offsetGet` method.
	 *
	 * @since 1.3.0
	 */

	public function test_offset_get() {
		$this->container->bind( 'name', 'Fredrik' );
		$this->assertEquals( 'Fredrik', $this->container['name'] );
	}

	/**
	 * Test `offsetSet` method.
	 *
	 * @since 1.3.0
	 */

	public function test_offset_set() {
		$this->container['plugin'] = 'Papi';
		$this->assertEquals( 'Papi', $this->container['plugin'] );
	}

	/**
	 * Test `offsetUnset` method.
	 *
	 * @since 1.3.0
	 */

	public function test_offset_unset() {
		$this->container['plugin'] = 'Papi';
		unset( $this->container['plugin'] );
		$this->assertFalse( isset( $this->container['plugin'] ) );
	}

}
