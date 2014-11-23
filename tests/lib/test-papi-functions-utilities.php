<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Unit tests covering utilities functionality.
 *
 * @package Papi
 */

class WP_Papi_Functions_Utilities extends WP_UnitTestCase {

	/**
	 * Setup the test and register the page types directory.
	 *
	 * @since 1.0.0
	 */

	public function setUp() {
		parent::setUp();

		$_GET  = array();
		$_POST = array();

		//$this->post_id = $this->factory->post->create();
	}

	/**
	 * Test _papi_f.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_f() {
		$this->assertEquals( '_page', _papi_f( 'page' ) );
	}

	/**
	 * Test _papi_dashify.
	 *
	 * @since 1.0.0
	 */


	public function test_papi_dashify() {
		$this->assertEquals( 'hello-world', _papi_dashify( 'hello world' ) );
	}

	/**
	 * Test _papi_get_class_name.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_class_name() {
		$actual = _papi_get_class_name( dirname( __FILE__ ).'/../data/page-types/simple-page-type.php' );
		$this->assertEquals( 'Simple_Page_Type', $actual );
	}

	/**
	 * Test _papi_get_or_post.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_or_post() {
		$_GET['world'] = 'hello';

		$this->assertEquals( 'hello', _papi_get_or_post( 'world' ) );
		unset( $_GET['world'] );

		$_POST['world'] = 'hello';

		$this->assertEquals( 'hello', _papi_get_or_post( 'world' ) );
		unset( $_POST['world'] );
	}

	/**
	 * Test _papi_get_qs.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_qs() {
		$_GET['page'] = 1;

		$this->assertEquals( 1, _papi_get_qs( 'page' ) );
		unset( $_GET['page'] );
	}

	/**
	 * Test _papi_html_name.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_html_name() {
		$this->assertEquals( 'papi_hello_world_aao', _papi_html_name( 'hello world åäö' ) );
	}

	/**
	 * Test _papi_is_ext.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_is_ext() {
		$this->assertEquals( true, _papi_is_ext( 'index.php', 'php' ) );
	}

	/**
	 * Test _papi_polylang.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_polylang() {
		$this->assertFalse( _papi_polylang() );
	}

	/**
	 * Test _papi_remove_papi.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_remove_papi() {
		$this->assertEquals( 'hello-world', _papi_remove_papi( 'papi-hello-world' ) );
	}

	/**
	 * Test _papi_remove_trailing_quotes.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_remove_trailing_quotes() {
		$this->assertEquals( '"hello" "world"', _papi_remove_trailing_quotes( '\"hello\" \"world\"' ) );
	}

	/**
	 * Test _papi_slugify.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_slugify() {
		$this->assertEquals( 'hello-world-aao', _papi_slugify( 'hello world åäö' ) );
	}

	/**
	 * Test _papi_to_array.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_to_array() {
		$this->assertEquals( array( 1 ), _papi_to_array( 1 ) );
	}

	/**
	 * Test _papi_underscorify.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_underscorify() {
		$this->assertEquals( 'hello_world_it', _papi_underscorify( 'hello world-it' ) );
	}

	/**
	 * Test _papify.
	 *
	 * @since 1.0.0
	 */

	public function test_papify() {
		$this->assertEquals( 'papi_hello_world', _papify( 'hello_world' ) );
		$this->assertEquals( 'papi_hello_world', _papify( 'papi_hello_world' ) );
	}

}
