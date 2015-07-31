<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Core_Conditional_Rule` class.
 *
 * @package Papi
 */
class Papi_Core_Conditional_Rule_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->rule = new Papi_Core_Conditional_Rule( [
			'operator' => '=',
			'slug'     => 'name',
			'source'   => 'Elli',
			'value'    => 'Fredrik'
		] );
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->rule );
	}

	public function test_operator() {
		$this->assertEquals( '=', $this->rule->operator );
	}

	public function test_slug() {
		$this->assertEquals( 'papi_name', $this->rule->slug );
	}

	public function test_source() {
		$this->assertEquals( 'Elli', $this->rule->get_source() );
	}

	public function test_source_callable_failied() {
		$rule = new Papi_Core_Conditional_Rule( [
			'operator' => '=',
			'slug'     => 'numbers',
			'source'   => [new stdClass, 'fake']
		] );
		$this->assertEmpty( $rule->get_source() );

		$rule = new Papi_Core_Conditional_Rule( [
			'operator' => '=',
			'slug'     => 'numbers',
			'source'   => [$this, 'fake']
		] );
		$this->assertEmpty( $rule->get_source() );

		$rule = new Papi_Core_Conditional_Rule( [
			'operator' => '=',
			'slug'     => 'numbers',
			'source'   => '#source_callable'
		] );
		$this->assertEquals( '#source_callable', $rule->get_source() );
	}

	public function test_source_callable() {
		$rule = new Papi_Core_Conditional_Rule( [
			'operator' => '=',
			'slug'     => 'numbers',
			'source'   => [$this, 'source_callable']
		] );
		$this->assertEquals( [1, 2], $rule->get_source() );

		$rule = new Papi_Core_Conditional_Rule( [
			'operator' => '=',
			'slug'     => 'name',
			'source'   => [$this, 'source_callable']
		] );
		$this->assertEquals( 'Fredrik', $rule->get_source() );

		$rule = new Papi_Core_Conditional_Rule( [
			'operator' => '=',
			'slug'     => 'numbers',
			'source'   => 'source_callable'
		] );
		$this->assertEquals( [1, 2], $rule->get_source() );

		$rule = new Papi_Core_Conditional_Rule( [
			'operator' => '=',
			'slug'     => 'name',
			'source'   => 'source_callable'
		] );
		$this->assertEquals( 'Fredrik', $rule->get_source() );
	}

	public function test_source_closure() {
		$rule = new Papi_Core_Conditional_Rule( [
			'operator' => '=',
			'slug'     => 'numbers',
			'source'   => function ( $slug ) {
				if ( $slug === 'papi_numbers' ) {
					return [1, 2];
				} else {
					return 'Fredrik';
				}
			}
		] );
		$this->assertEmpty( $rule->get_source() );
	}

	public function source_callable( $slug ) {
		if ( $slug === 'papi_numbers' ) {
			return [1, 2];
		} else {
			return 'Fredrik';
		}
	}

	public function test_value() {
		$this->assertEquals( 'Fredrik', $this->rule->value );
	}

}

function source_callable( $slug ) {
	if ( $slug === 'papi_numbers' ) {
		return [1, 2];
	} else {
		return 'Fredrik';
	}
}
