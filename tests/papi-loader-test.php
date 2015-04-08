<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Loader` class.
 *
 * @package Papi
 */

class Papi_Loader_Test extends WP_UnitTestCase {

	/**
	 * Test so Papi plugin is loaded correct.
	 *
	 * @since 1.0.0
	 */

	public function test_plugin_activated() {
		$this->assertTrue( class_exists( 'Papi_Loader' ) && class_exists( 'Papi_Admin' ) );
	}

	/**
	 * The action `plugins_loaded` should have the `papi` hook
	 * and should have a default priority of 10.
	 *
	 * @since 1.0.0
	 */

	public function test_after_setup_theme_action() {
		$this->assertEquals( 10, has_action( 'plugins_loaded', 'papi' ) );
	}

}
