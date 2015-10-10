<?php

/**
 * Unit tests covering `Papi_Admin_Management_Pages` class.
 *
 * @package Papi
 */
class Papi_Admin_Management_Pages_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$_GET  = [];
		$this->management_pages = new Papi_Admin_Management_Pages();
	}

	public function tearDown() {
		parent::tearDown();
		unset( $_GET, $_POST, $this->management_pages );
	}

	public function test_actions() {
		$this->assertSame( 10, has_action( 'admin_menu', [$this->management_pages, 'admin_menu'] ) );
	}

	public function test_admin_menu() {
		global $_wp_submenu_nopriv;
		$this->assertFalse( isset( $_wp_submenu_nopriv['tools.php'] ) );
		$this->assertFalse( isset( $_wp_submenu_nopriv['tools.php']['papi'] ) );
		$this->management_pages->admin_menu();
		$this->assertTrue( isset( $_wp_submenu_nopriv['tools.php'] ) );
		$this->assertTrue( isset( $_wp_submenu_nopriv['tools.php']['papi'] ) );
		$this->assertTrue( $_wp_submenu_nopriv['tools.php']['papi'] );
	}

	public function test_render_view() {
		$_GET['page'] = 'papi';
		$this->management_pages->render_view();
		$this->expectOutputRegex( '/\<h3\>Page types<\/h3\>/' );

		$_GET['view'] = 'management-page-type';
		$this->management_pages->render_view();
		$this->expectOutputRegex( '/\<h3\>Overview of page type\<\/h3\>/' );

		$_GET['page_type'] = 'simple-page-type';
		$this->management_pages->render_view();
		$this->expectOutputRegex( '/\<h3\>Overview of page type: Simple page\<\/h3\>/' );
	}
}
