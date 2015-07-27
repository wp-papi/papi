<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Core_Conditional` class.
 *
 * @package Papi
 */

class Papi_Core_Conditional_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->conditional = new Papi_Core_Conditional();
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->conditional );
	}

	public function test_failed_display() {
		$result = $this->conditional->display( [
			[
				'operator' => '=',
				'slug'     => 'fake',
				'value'    => ''
			]
		] );

		$this->assertFalse( $result );

		$result = $this->conditional->display( [
			'relation' => 'AND',
			[
				'operator' => '=',
				'slug'     => 'fake',
				'value'    => ''
			],
		] );

		$this->assertFalse( $result );

		$result = $this->conditional->display( [
			[
				'operator' => '=',
				'slug'     => 'fake',
				'value'    => ''
			],
			false,
			null
		] );

		$this->assertFalse( $result );

		$result = $this->conditional->display( [
			'relation' => 'AND',
			[
				'operator' => '=',
				'slug'     => 'fake',
				'value'    => ''
			],
			false,
			null
		] );

		$this->assertFalse( $result );
	}

	public function test_success_display() {
		$result = $this->conditional->display( [] );
		$this->assertTrue( $result );

		$result = $this->conditional->display( [null] );
		$this->assertTrue( $result );

		$result = $this->conditional->display( [true] );
		$this->assertTrue( $result );

		$result = $this->conditional->display( [false] );
		$this->assertTrue( $result );

		$result = $this->conditional->display( ['hello'] );
		$this->assertTrue( $result );

		$result = $this->conditional->display( [(object) []] );
		$this->assertTrue( $result );

		$result = $this->conditional->display( [
			'relation' => 'nil',
			[
				(object) []
			]
		] );
		$this->assertTrue( $result );
	}

	public function test_custom_display() {
		tests_add_filter( 'papi/conditional/rule/==', function ( $rule ) {
			return $rule->value === 'hello';
		} );

		$result = $this->conditional->display( [
			[
				'operator' => '==',
				'slug'     => 'fake',
				'value'    => ''
			]
		] );
		$this->assertFalse( $result );

		$result = $this->conditional->display( [
			[
				'operator' => '==',
				'slug'     => 'fake',
				'value'    => 'hello'
			]
		] );
		$this->assertTrue( $result );

		tests_add_filter( 'papi/conditional/rule/===', function ( $rule ) {
			return null;
		} );

		$result = $this->conditional->display( [
			[
				'operator' => '===',
				'slug'     => 'fake',
				'value'    => ''
			]
		] );
		$this->assertFalse( $result );
	}

}
