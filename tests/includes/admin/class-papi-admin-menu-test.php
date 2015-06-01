<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Admin_Menu` class.
 *
 * @package Papi
 */

class Papi_Admin_Menu_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		$_GET  = [];
		$_POST = [];
		$this->menu = new Papi_Admin_Menu();
	}

	public function tearDown() {
		parent::tearDown();
		unset( $_GET, $_POST, $this->menu );
	}

	public function test_actions_admin() {
		global $current_screen;

		$this->assertNull( $current_screen );

        $current_screen = WP_Screen::get( 'admin_init' );

		$menu = new Papi_Admin_Menu();

		$this->assertEquals( 10, has_action( 'admin_init', [$menu, 'admin_bar_menu'] ) );
		$this->assertEquals( 10, has_action( 'admin_menu', [$menu, 'page_items_menu'] ) );
		$this->assertEquals( 10, has_action( 'admin_menu', [$menu, 'post_types_menu'] ) );

		$current_screen = null;
	}

	public function test_actions() {
		$this->assertEquals( 10, has_action( 'admin_bar_menu', [$this->menu, 'admin_bar_menu'] ) );
	}

	public function test_render_view() {
		$_GET['page'] = '';
		$this->menu->render_view();
		$this->expectOutputRegex( '/\<h2\>Papi\s\-\s404\<\/h2\>/' );

		$_GET['page'] = 'papi-add-new-page,page';
		$this->menu->render_view();
		$this->expectOutputRegex( '/Add new page type/' );
	}
}
