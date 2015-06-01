<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Container` class.
 *
 * @package Papi
 */

class Papi_Container_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->container = new Papi_Container;
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->container );
	}

	public function test_make_not_defined() {
		$this->setExpectedException( 'InvalidArgumentException', 'Identifier [fredrik] is not defined' );
		$this->container->make( 'fredrik' );
	}

	public function test_bind() {
		$this->container->bind( 'name', 'Fredrik' );
		$this->assertEquals( 'Fredrik', $this->container->make( 'name' ) );
	}

	public function test_exists() {
		$this->container->bind( 'name', 'Fredrik' );
		$this->assertTrue( $this->container->exists( 'name' ) );
	}

	public function test_offset_exists() {
		$this->container->bind( 'name', 'Fredrik' );
		$this->assertTrue( isset( $this->container['name'] ) );
	}

	public function test_offset_get() {
		$this->container->bind( 'name', 'Fredrik' );
		$this->assertEquals( 'Fredrik', $this->container['name'] );
	}

	public function test_offset_set() {
		$this->container['plugin'] = 'Papi';
		$this->assertEquals( 'Papi', $this->container['plugin'] );
	}

	public function test_offset_unset() {
		$this->container['plugin'] = 'Papi';
		unset( $this->container['plugin'] );
		$this->assertFalse( isset( $this->container['plugin'] ) );
	}

}
