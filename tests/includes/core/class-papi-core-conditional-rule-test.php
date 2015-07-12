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
		$this->assertEquals( 'name', $this->rule->slug );
	}

	public function test_value() {
		$this->assertEquals( 'Fredrik', $this->rule->value );
	}

}
