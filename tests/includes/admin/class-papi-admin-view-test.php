<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Admin_View` class.
 *
 * @package Papi
 */

class Papi_Admin_View_Test extends WP_UnitTestCase {

	/**
	 * Setup test.
	 *
	 * @since 1.3.0
	 */

	public function setUp() {
		$this->view = new Papi_Admin_View();
	}

	/**
	 * Tear down test.
	 *
	 * @since 1.3.0
	 */

	public function tearDown() {
		unset( $this->view );
	}

	/**
	 * Test `exists` method.
	 *
	 * @since 1.3.0
	 */

	public function test_exists() {
		$this->assertFalse($this->view->exists('empty'));
		$this->assertTrue($this->view->exists('add-new-page'));
	}

	/**
	 * Test `render` method.
	 *
	 * @since 1.3.0
	 */

	public function test_render() {
		$this->view->render('add-new-page');
		$this->expectOutputRegex('/.*\S.*/');
	}

}
