<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Conditional_Rules` class.
 *
 * @package Papi
 */

class Papi_Conditional_Rule_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->post_id = $this->factory->post->create();

		$_GET = [];
		$_GET['post'] = $this->post_id;

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );
	}

	public function tearDown() {
		parent::tearDown();
		unset(
			$this->post_id,
			$_GET
		);
	}

	private function call_rule( $rule ) {
		$rule   = $this->get_rule( $rule );
		$result =  apply_filters( 'papi/conditional/rule/' . strtoupper( $rule->operator ), $rule );

		if ( $result === true || $result === false ) {
			return $result;
		}

		return false;
	}

	private function get_rule( $rule ) {
		if ( ! is_array( $rule ) ) {
			return;
		}

		return new Papi_Core_Conditional_Rule( $rule );
	}

	private function save_property( $property ) {
		$handler = new Papi_Admin_Post_Handler();

		$_POST = papi_test_create_property_post_data( [
			'slug'  => $property->slug,
			'type'  => $property,
			'value' => $property->value
		], $_POST );

		$handler->save_property( $this->post_id );
	}

	public function test_rule_equal() {
		$property = papi_property( [
			'title' => 'Name',
			'type'  => 'string',
			'value' => 'Fredrik'
		] );

		$this->save_property( $property );

		$result = $this->call_rule( [
			'operator' => '=',
			'slug'     => 'name',
			'value'    => ''
		] );

		$this->assertFalse( $result );

		$result = $this->call_rule( [
			'operator' => '=',
			'slug'     => 'name',
			'value'    => 'Fredrik'
		] );

		$this->assertTrue( $result );
	}

	public function test_rule_not_equal() {
		$property = papi_property( [
			'title' => 'Name',
			'type'  => 'string',
			'slug'  => 'name',
			'value' => 'Fredrik'
		] );

		$this->save_property( $property );

		$result = $this->call_rule( [
			'operator' => '!=',
			'slug'     => 'name',
			'value'    => 'Fredrik'
		] );

		$this->assertFalse( $result );

		$result = $this->call_rule( [
			'operator' => '!=',
			'slug'     => 'name',
			'value'    => ''
		] );

		$this->assertTrue( $result );
	}

	public function test_rule_greater_then() {
		$property = papi_property( [
			'title' => 'Number',
			'type'  => 'number',
			'slug'  => 'number',
			'value' => 1
		] );

		$this->save_property( $property );

		$result = $this->call_rule( [
			'operator' => '>',
			'slug'     => 'number',
			'value'    => 1
		] );

		$this->assertFalse( $result );

		$result = $this->call_rule( [
			'operator' => '>',
			'slug'     => 'number',
			'value'    => 0
		] );

		$this->assertTrue( $result );
	}

	public function test_rule_greater_then_or_equal() {
		$property = papi_property( [
			'title' => 'Number',
			'type'  => 'number',
			'slug'  => 'number',
			'value' => 1
		] );

		$this->save_property( $property );

		$result = $this->call_rule( [
			'operator' => '>=',
			'slug'     => 'number',
			'value'    => 1
		] );

		$this->assertTrue( $result );

		$result = $this->call_rule( [
			'operator' => '>=',
			'slug'     => 'number',
			'value'    => 0
		] );

		$this->assertTrue( $result );
	}

	public function test_rule_less_then() {
		$property = papi_property( [
			'title' => 'Number',
			'type'  => 'number',
			'slug'  => 'number',
			'value' => 1
		] );

		$this->save_property( $property );

		$result = $this->call_rule( [
			'operator' => '<',
			'slug'     => 'number',
			'value'    => 1
		] );

		$this->assertFalse( $result );

		$result = $this->call_rule( [
			'operator' => '<',
			'slug'     => 'number',
			'value'    => 2
		] );

		$this->assertTrue( $result );
	}

	public function test_rule_less_then_or_equal() {
		$property = papi_property( [
			'title' => 'Number',
			'type'  => 'number',
			'slug'  => 'number',
			'value' => 1
		] );

		$this->save_property( $property );

		$result = $this->call_rule( [
			'operator' => '<=',
			'slug'     => 'number',
			'value'    => 1
		] );

		$this->assertTrue( $result );

		$result = $this->call_rule( [
			'operator' => '<=',
			'slug'     => 'number',
			'value'    => 2
		] );

		$this->assertTrue( $result );
	}

	public function test_rule_in() {
		$property = papi_property( [
			'title' => 'Number',
			'type'  => 'number',
			'slug'  => 'number',
			'value' => 1
		] );

		$this->save_property( $property );

		$result = $this->call_rule( [
			'operator' => 'IN',
			'slug'     => 'number',
			'value'    => array( 10, 20 )
		] );

		$this->assertFalse( $result );

		$result = $this->call_rule( [
			'operator' => 'IN',
			'slug'     => 'number',
			'value'    => array( 1, 2 )
		] );

		$this->assertTrue( $result );
	}

	public function test_rule_like() {
		$property = papi_property( [
			'title' => 'Name',
			'type'  => 'string',
			'slug'  => 'name',
			'value' => 'Fredrik'
		] );

		$this->save_property( $property );

		$result = $this->call_rule( [
			'operator' => 'LIKE',
			'slug'     => 'name',
			'value'    => 'Elli'
		] );

		$this->assertFalse( $result );

		$result = $this->call_rule( [
			'operator' => 'LIKE',
			'slug'     => 'name',
			'value'    => 'rik'
		] );

		$this->assertTrue( $result );

		$property = papi_property( [
			'title' => 'Name',
			'type'  => 'string',
			'slug'  => 'name2',
			'value' => 124
		] );

		$this->save_property( $property );

		$result = $this->call_rule( [
			'operator' => 'LIKE',
			'slug'     => 'name2',
			'value'    => 1
		] );

		$this->assertTrue( $result );
	}

	public function test_rule_between() {
		$property = papi_property( [
			'title' => 'Number',
			'type'  => 'number',
			'slug'  => 'number',
			'value' => 1
		] );

		$this->save_property( $property );

		$result = $this->call_rule( [
			'operator' => 'BETWEEN',
			'slug'     => 'number',
			'value'    => array( 10, 20 )
		] );

		$this->assertFalse( $result );

		$result = $this->call_rule( [
			'operator' => 'BETWEEN',
			'slug'     => 'number',
			'value'    => array( 0, 2 )
		] );

		$this->assertTrue( $result );
	}

	public function test_rule_not_exists() {
		$result = $this->call_rule( [
			'operator' => 'NOT EXISTS',
			'slug'     => 'fake'
		] );

		$this->assertTrue( $result );
	}
}
