<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Admin_Ajax` class.
 *
 * @package Papi
 */

class Papi_Admin_Ajax_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$_GET  = [];
		$_POST = [];
		$this->ajax = new Papi_Admin_Ajax();
	}

	public function tearDown() {
		parent::tearDown();
		unset( $_GET, $_POST, $this->ajax );
	}

	public function test_get_property() {
		$_GET = array_merge( $_GET, [
			'type' => 'string',
			'slug' => 'hello'
		] );

		do_action( 'papi_ajax_get_property' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( '/papi\_hello/' );
	}

	public function test_get_properties() {
		$property = papi_get_property_type( [
			'type' => 'string',
			'slug' => 'name'
		] );
		$_POST = array_merge( $_POST, [
			'properties' => json_encode( [
				$property->get_options()
			] )
		] );

		do_action( 'papi_ajax_get_properties' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( '/papi\_name/' );
	}

}
