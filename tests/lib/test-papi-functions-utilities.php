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
	}

	/**
	 * Test _papi_convert_to_string.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_convert_to_string() {
		$this->assertEquals( 'false', _papi_convert_to_string( false ) );
		$this->assertEquals( 'true', _papi_convert_to_string( true ) );
		$this->assertEquals( '0.1', _papi_convert_to_string( 0.1 ) );
		$this->assertEquals( '1.1', _papi_convert_to_string( 1.1 ) );
		$this->assertEquals( '0', _papi_convert_to_string( 0 ) );
		$this->assertEmpty( _papi_convert_to_string( array() ) );
		$this->assertEmpty( _papi_convert_to_string( new stdClass() ) );
		$this->assertNotEmpty( _papi_convert_to_string( new ReflectionClass( 'ReflectionClass' ) ) );
		$this->assertEmpty( _papi_convert_to_string( Papi_Loader::instance() ) );
	}

	/**
	 * Test _papi_f.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_f() {
		$this->assertEquals( '_page', _papi_f( 'page' ) );
		$this->assertEmpty( _papi_f( null ) );
		$this->assertEmpty( _papi_f( true ) );
		$this->assertEmpty( _papi_f( false ) );
		$this->assertEmpty( _papi_f( 1 ) );
		$this->assertEmpty( _papi_f( array() ) );
		$this->assertEmpty( _papi_f( new stdClass() ) );
	}

	/**
	 * Test _papi_ff.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_ff() {
		$this->assertEquals( '__page', _papi_ff( 'page' ) );
		$this->assertEmpty( _papi_ff( null ) );
		$this->assertEmpty( _papi_ff( true ) );
		$this->assertEmpty( _papi_ff( false ) );
		$this->assertEmpty( _papi_ff( 1 ) );
		$this->assertEmpty( _papi_ff( array() ) );
		$this->assertEmpty( _papi_ff( new stdClass() ) );
	}

	/**
	 * Test _papi_dashify.
	 *
	 * @since 1.0.0
	 */


	public function test_papi_dashify() {
		$this->assertEquals( 'hello-world', _papi_dashify( 'hello world' ) );
		$this->assertEmpty( _papi_dashify( null ) );
		$this->assertEmpty( _papi_dashify( true ) );
		$this->assertEmpty( _papi_dashify( false ) );
		$this->assertEmpty( _papi_dashify( 1 ) );
		$this->assertEmpty( _papi_dashify( array() ) );
		$this->assertEmpty( _papi_dashify( new stdClass() ) );
	}

	/**
	 * Test _papi_get_class_name.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_class_name() {
		$actual = _papi_get_class_name( dirname( __FILE__ ).'/../data/page-types/simple-page-type.php' );
		$this->assertEquals( 'Simple_Page_Type', $actual );
		$this->assertEmpty( _papi_get_class_name( null ) );
		$this->assertEmpty( _papi_get_class_name( true ) );
		$this->assertEmpty( _papi_get_class_name( false ) );
		$this->assertEmpty( _papi_get_class_name( 1 ) );
		$this->assertEmpty( _papi_get_class_name( array() ) );
		$this->assertEmpty( _papi_get_class_name( new stdClass() ) );
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

		$this->assertEmpty( _papi_get_or_post( null ) );
		$this->assertEmpty( _papi_get_or_post( true ) );
		$this->assertEmpty( _papi_get_or_post( false ) );
		$this->assertEmpty( _papi_get_or_post( 1 ) );
		$this->assertEmpty( _papi_get_or_post( array() ) );
		$this->assertEmpty( _papi_get_or_post( new stdClass() ) );
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

		$this->assertEmpty( _papi_get_qs( null ) );
		$this->assertEmpty( _papi_get_qs( true ) );
		$this->assertEmpty( _papi_get_qs( false ) );
		$this->assertEmpty( _papi_get_qs( 1 ) );
		$this->assertEmpty( _papi_get_qs( array() ) );
		$this->assertEmpty( _papi_get_qs( new stdClass() ) );
	}

	/**
	 * Test _papi_html_name.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_html_name() {
		$this->assertEquals( 'papi_hello_world_aao', _papi_html_name( 'hello world åäö' ) );
		$this->assertEmpty( _papi_html_name( null ) );
		$this->assertEmpty( _papi_html_name( true ) );
		$this->assertEmpty( _papi_html_name( false ) );
		$this->assertEmpty( _papi_html_name( 1 ) );
		$this->assertEmpty( _papi_html_name( array() ) );
		$this->assertEmpty( _papi_html_name( new stdClass() ) );
	}

	/**
	 * Test _papi_is_ext.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_is_ext() {
		$this->assertTrue( _papi_is_ext( 'index.php', 'php' ) );
		$this->assertFalse( _papi_is_ext( null, 'php' ) );
		$this->assertFalse( _papi_is_ext( true, 'php' ) );
		$this->assertFalse( _papi_is_ext( false, 'php' ) );
		$this->assertFalse( _papi_is_ext( 1, 'php' ) );
		$this->assertFalse( _papi_is_ext( array(), 'php' ) );
		$this->assertFalse( _papi_is_ext( new stdClass(), 'php' ) );
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
		$this->assertEmpty( _papi_remove_papi( null ) );
		$this->assertEmpty( _papi_remove_papi( true ) );
		$this->assertEmpty( _papi_remove_papi( false ) );
		$this->assertEmpty( _papi_remove_papi( 1 ) );
		$this->assertEmpty( _papi_remove_papi( array() ) );
		$this->assertEmpty( _papi_remove_papi( new stdClass() ) );
	}

	/**
	 * Test _papi_remove_trailing_quotes.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_remove_trailing_quotes() {
		$this->assertEquals( '"hello" "world"', _papi_remove_trailing_quotes( '\"hello\" \"world\"' ) );
		$this->assertEmpty( _papi_remove_trailing_quotes( null ) );
		$this->assertEmpty( _papi_remove_trailing_quotes( true ) );
		$this->assertEmpty( _papi_remove_trailing_quotes( false ) );
		$this->assertEmpty( _papi_remove_trailing_quotes( 1 ) );
		$this->assertEmpty( _papi_remove_trailing_quotes( array() ) );
		$this->assertEmpty( _papi_remove_trailing_quotes( new stdClass() ) );
	}

	/**
	 * Test _papi_slugify.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_slugify() {
		$this->assertEquals( 'hello-world-aao', _papi_slugify( 'hello world åäö' ) );
		$this->assertEmpty( _papi_slugify( null ) );
		$this->assertEmpty( _papi_slugify( true ) );
		$this->assertEmpty( _papi_slugify( false ) );
		$this->assertEmpty( _papi_slugify( 1 ) );
		$this->assertEmpty( _papi_slugify( array() ) );
		$this->assertEmpty( _papi_slugify( new stdClass() ) );
	}

	/**
	 * Test _papi_to_array.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_to_array() {
		$this->assertEquals( array( 1 ), _papi_to_array( 1 ) );
		$this->assertEquals( array( null ), _papi_to_array( null ) );
		$this->assertEquals( array( false ), _papi_to_array( false ) );
		$this->assertEquals( array( true ), _papi_to_array( true ) );
		$this->assertEquals( array( ), _papi_to_array( array() ) );
		$this->assertEquals( array( new stdClass() ), _papi_to_array( new stdClass() ) );
	}

	/**
	 * Test _papi_underscorify.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_underscorify() {
		$this->assertEquals( 'hello_world_it', _papi_underscorify( 'hello world-it' ) );
		$this->assertEmpty( _papi_underscorify( null ) );
		$this->assertEmpty( _papi_underscorify( true ) );
		$this->assertEmpty( _papi_underscorify( false ) );
		$this->assertEmpty( _papi_underscorify( 1 ) );
		$this->assertEmpty( _papi_underscorify( array() ) );
		$this->assertEmpty( _papi_underscorify( new stdClass() ) );
	}

	/**
	 * Test _papify.
	 *
	 * @since 1.0.0
	 */

	public function test_papify() {
		$this->assertEquals( 'papi_hello_world', _papify( 'hello_world' ) );
		$this->assertEquals( 'papi_hello_world', _papify( 'papi_hello_world' ) );
		$this->assertEmpty( _papify( null ) );
		$this->assertEmpty( _papify( true ) );
		$this->assertEmpty( _papify( false ) );
		$this->assertEmpty( _papify( 1 ) );
		$this->assertEmpty( _papify( array() ) );
		$this->assertEmpty( _papify( new stdClass() ) );
	}

}
