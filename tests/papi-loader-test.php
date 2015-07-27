<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Loader` class.
 *
 * @package Papi
 */

class Papi_Loader_Test extends WP_UnitTestCase {

	public function test_plugin_activated() {
		$this->assertTrue( class_exists( 'Papi_Loader' ) && class_exists( 'Papi_Admin' ) );
	}

	public function test_after_setup_theme_action() {
		$this->assertEquals( 10, has_action( 'plugins_loaded', 'papi' ) );
	}

}
