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

	public function test_actions() {
		$this->assertEquals( 10, has_action( 'init', [$this->ajax, 'add_endpoint'] ) );
		$this->assertEquals( 10, has_action( 'parse_query', [$this->ajax, 'handle_papi_ajax'] ) );
		$this->assertEquals( 10, has_action( 'admin_enqueue_scripts', [$this->ajax, 'ajax_url'] ) );
		$this->assertEquals( 10, has_action( 'papi/ajax/get_property', [$this->ajax, 'get_property'] ) );
		$this->assertEquals( 10, has_action( 'papi/ajax/get_properties', [$this->ajax, 'get_properties'] ) );
	}

	public function test_endpoint() {
		global $wp_rewrite;
		$this->assertNotNull( $wp_rewrite->extra_rules_top['papi-ajax/([^/]*)/?'] );
		$this->assertEquals( 'index.php?action=$matches[1]', $wp_rewrite->extra_rules_top['papi-ajax/([^/]*)/?'] );
	}

	public function test_get_property() {
		$_GET = array_merge( $_GET, [
			'type' => 'string',
			'slug' => 'hello'
		] );

		do_action( 'papi/ajax/get_property' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( '/papi\_hello/' );
	}

	public function test_get_property_fail() {
		$_GET = array_merge( $_GET, [
			'type' => 'kvack',
			'slug' => 'hello'
		] );

		do_action( 'papi/ajax/get_property' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( '/No\sproperty\sfound/' );
	}

	public function test_get_properties() {
		$property = papi_get_property_type( [
			'type' => 'string',
			'slug' => 'name'
		] );

		$_POST = array_merge( $_POST, [
			'properties' => json_encode( [
				$property->get_options(),
				[
					'type'  => 'string',
					'title'	=> 'nyckel'
				]
			] )
		] );

		do_action( 'papi/ajax/get_properties' );

		$this->expectOutputRegex( '/.*\S.*/' );
		$this->expectOutputRegex( '/papi\_name/' );
		$this->expectOutputRegex( '/papi\_nyckel/' );
	}

}
