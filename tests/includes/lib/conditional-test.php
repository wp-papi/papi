<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering conditional functions.
 *
 * @package Papi
 */
class Papi_Lib_Conditional_Test extends WP_UnitTestCase {

	public function test_papi_is_rule() {
		$this->assertFalse( papi_is_rule( null ) );
		$this->assertFalse( papi_is_rule( true ) );
		$this->assertFalse( papi_is_rule( false ) );
		$this->assertFalse( papi_is_rule( 1 ) );
		$this->assertFalse( papi_is_rule( '' ) );
		$this->assertFalse( papi_is_rule( [] ) );
		$this->assertFalse( papi_is_rule( (object) [] ) );
		$this->assertTrue( papi_is_rule( papi_rule( [
			'operator' => '>',
			'slug'     => 'name',
			'value'    => 'Fredrik'
		] ) ) );
	}

	public function test_papi_rule() {
		$this->assertNull( papi_rule( null ) );
		$this->assertNull( papi_rule( true ) );
		$this->assertNull( papi_rule( false ) );
		$this->assertNull( papi_rule( 1 ) );
		$this->assertNull( papi_rule( '' ) );
		$this->assertNull( papi_rule( [] ) );
		$this->assertNull( papi_rule( (object) [] ) );
		$rule = papi_rule( [
			'operator' => '>',
			'slug'     => 'name',
			'value'    => 'Fredrik'
		] );
		$this->assertTrue( papi_is_rule( $rule ) );
		$this->assertTrue( papi_is_rule( papi_rule( $rule ) ) );
	}
}
