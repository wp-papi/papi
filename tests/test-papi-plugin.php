<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Unit tests to check so Page Tyep Builder is loaded correctly.
 *
 * @package Papi
 */

class WP_Papi_Plugin extends WP_UnitTestCase {

	/**
	 * Test so Papi plugin is loaded correct.
	 *
	 * @since 1.0.0
	 */

	public function test_plugin_activated() {
		$this->assertTrue( class_exists( 'Papi_Loader' ) && class_exists( 'Papi_Admin' ) );
	}

	/**
	 * The action `after_theme_setup` should have the `papi` hook
	 * and should have a default priority of 10.
	 *
	 * @since 1.0.0
	 */

	public function test_after_setup_theme_action() {
		$this->assertEquals( 10, has_action( 'after_setup_theme', 'papi' ) );
	}

}
