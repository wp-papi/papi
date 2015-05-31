<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Admin_Option_Handler` class.
 *
 * @package Papi
 */

class Papi_Admin_Option_Handler_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->page_type = papi_get_page_type_by_id( 'options/header-option-type' );
		$this->property  = $this->page_type->get_property( 'name' );
	}

	public function tearDown() {
		parent::tearDown();
		unset(
			$_POST,
			$_SERVER['REQUEST_METHOD'],
			$this->handler,
			$this->property,
			$this->page_type
		);
	}

	public function test_save_property() {
		$_POST = papi_test_create_property_post_data( [
			'slug'  => $this->property->slug,
			'type'  => $this->property,
			'value' => 'Hello, world!'
		], $_POST );

		$_POST['papi_meta_nonce'] = wp_create_nonce( 'papi_save_data' );

		$old_request_uri = $_SERVER['REQUEST_URI'];

		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_SERVER['REQUEST_URI'] = 'http://site.com/wp-admin/options-general.php?page=papi/options/header-option-type';

		new Papi_Admin_Option_Handler;

		$value = papi_option( $this->property->slug );

		$_SERVER['REQUEST_URI'] = $old_request_uri;

		$this->assertEquals( 'Hello, world!', $value );
	}

}
