<?php

/**
 * @group admin
 */
class Papi_Admin_Assets_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->assets = new Papi_Admin_Assets;
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->assets );
	}

	public function test_actions() {
		global $current_screen;

	    $current_screen = WP_Screen::get( 'admin_init' );

		$assets = new Papi_Admin_Assets;

		$this->assertGreaterThan( 0, has_action( 'admin_head', [$assets, 'enqueue_css'] ) );
		$this->assertGreaterThan( 0, has_action( 'admin_enqueue_scripts', [$assets, 'enqueue_js'] ) );
		$this->assertGreaterThan( 0, has_action( 'admin_enqueue_scripts', [$assets, 'enqueue_locale'] ) );

		$current_screen = null;
	}

	public function test_enqueue_css() {
		$this->assertNull( $this->assets->enqueue_css() );
	}

	public function test_enqueue_js() {
		$_SERVER['REQUEST_URI'] = 'plugins.php';
		$this->assertNull( $this->assets->enqueue_js() );
		$_SERVER['REQUEST_URI'] = '';
		$this->assertNull( $this->assets->enqueue_js() );
	}

	public function test_enqueue_locale() {
		$this->assertNull( $this->assets->enqueue_locale() );
	}
}
