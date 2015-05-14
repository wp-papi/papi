<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering utilities functions.
 *
 * @package Papi
 */

class Papi_Lib_Utilities_Test extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 *
	 * @since 1.0.0
	 */

	public function setUp() {
		parent::setUp();

		$_GET  = [];
		$_POST = [];
	}

	/**
	 * Tear down test.
	 *
	 * @since 1.3.0
	 */

	public function tearDown() {
		parent::tearDown();
		unset( $_GET, $_POST );
	}

	/**
	 * Test `papi_get_cache_key` function.
	 *
	 * @since 1.3.0
	 */

	public function test_papi_get_cache_key() {
		global $post;
		$post_id = $this->factory->post->create();
		$post = get_post( $post_id );
		$this->assertEquals( 'papi_page_' . $post_id, papi_get_cache_key( 'page', $post_id ) );
		$this->assertEquals( 'papi_page_920', papi_get_cache_key( 'page', 920 ) );
		unset( $post );
	}

	/**
	 * Test `papi_convert_to_string` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_convert_to_string() {
		$this->assertEquals( 'false', papi_convert_to_string( false ) );
		$this->assertEquals( 'true', papi_convert_to_string( true ) );
		$this->assertEquals( '0.1', papi_convert_to_string( 0.1 ) );
		$this->assertEquals( '1.1', papi_convert_to_string( 1.1 ) );
		$this->assertEquals( '0', papi_convert_to_string( 0 ) );
		$this->assertEmpty( papi_convert_to_string( [] ) );
		$this->assertEmpty( papi_convert_to_string( new stdClass() ) );
		$this->assertNotEmpty( papi_convert_to_string( new ReflectionClass( 'ReflectionClass' ) ) );
		$this->assertEmpty( papi_convert_to_string( Papi_Loader::instance() ) );
	}

	/**
	 * Test `papi_current_user_is_allowed` function.
	 *
	 * @since 1.3.0
	 */

	public function test_papi_current_user_is_allowed() {
		$this->assertTrue( papi_current_user_is_allowed() );

		$user_id = $this->factory->user->create( [ 'role' => 'administrator' ] );
		wp_set_current_user( $user_id );
		$this->assertTrue( papi_current_user_is_allowed( 'administrator' ) );
		$this->assertFalse( papi_current_user_is_allowed( 'administrator2' ) );
	}

	/**
	 * Test `papi_esc_html` function.
	 *
	 * @since 1.2.0
	 */

	public function test_papi_esc_html() {
		$this->assertEquals( '&lt;script&gt;alert(1);&lt;/script&gt;', papi_esc_html( '<script>alert(1);</script>' ) );
		$this->assertEquals( [ '&lt;script&gt;alert(1);&lt;/script&gt;' => 'hello world&lt;script&gt;alert(1);&lt;/script&gt;' ], papi_esc_html( [ '<script>alert(1);</script>' => 'hello world<script>alert(1);</script>' ] ) );

		$obj = new stdClass;
		$obj->title = '<script>alert(1);</script>';
		$obj2 = new stdClass;
		$obj2->title = '&lt;script&gt;alert(1);&lt;/script&gt;';
		$this->assertEquals( $obj2, papi_esc_html( $obj ) );

		$obj = new stdClass;
		$obj->html = '<br />';
		$obj->name = '<p>name</p>';
		$obj2 = new stdClass;
		$obj2->html = '<br />';
		$obj2->name = '&lt;p&gt;name&lt;/p&gt;';
		$this->assertEquals( $obj2, papi_esc_html( $obj, ['html'] ) );

		$this->assertEquals( [1], papi_esc_html( [1] ) );
	}

	/**
	 * Test `papi_f` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_f() {
		$this->assertEquals( '_page', papi_f( 'page' ) );
		$this->assertEquals( '_page', papi_f( '_page' ) );
		$this->assertEmpty( papi_f( null ) );
		$this->assertEmpty( papi_f( true ) );
		$this->assertEmpty( papi_f( false ) );
		$this->assertEmpty( papi_f( 1 ) );
		$this->assertEmpty( papi_f( [] ) );
		$this->assertEmpty( papi_f( new stdClass() ) );
	}

	/**
	 * Test `papi_ff` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_ff() {
		$this->assertEquals( '__page', papi_ff( 'page' ) );
		$this->assertEquals( '__page', papi_ff( '_page' ) );
		$this->assertEquals( '__page', papi_ff( '__page' ) );
		$this->assertEmpty( papi_ff( null ) );
		$this->assertEmpty( papi_ff( true ) );
		$this->assertEmpty( papi_ff( false ) );
		$this->assertEmpty( papi_ff( 1 ) );
		$this->assertEmpty( papi_ff( [] ) );
		$this->assertEmpty( papi_ff( new stdClass() ) );
	}

	/**
	 * Test `papi_dashify` function.
	 *
	 * @since 1.0.0
	 */


	public function test_papi_dashify() {
		$this->assertEquals( 'hello-world', papi_dashify( 'hello world' ) );
		$this->assertEmpty( papi_dashify( null ) );
		$this->assertEmpty( papi_dashify( true ) );
		$this->assertEmpty( papi_dashify( false ) );
		$this->assertEmpty( papi_dashify( 1 ) );
		$this->assertEmpty( papi_dashify( [] ) );
		$this->assertEmpty( papi_dashify( new stdClass() ) );
	}

	/**
	 * Test `papi_get_class_name` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_class_name() {
		$actual = papi_get_class_name( papi_test_get_fixtures_path( '/page-types/simple-page-type.php' ) );
		$this->assertEquals( 'Simple_Page_Type', $actual );
		$this->assertEmpty( papi_get_class_name( null ) );
		$this->assertEmpty( papi_get_class_name( true ) );
		$this->assertEmpty( papi_get_class_name( false ) );
		$this->assertEmpty( papi_get_class_name( 1 ) );
		$this->assertEmpty( papi_get_class_name( [] ) );
		$this->assertEmpty( papi_get_class_name( new stdClass() ) );
		$actual = papi_get_class_name( papi_test_get_fixtures_path( '/page-types/namespace-page-type.php' ) );
		$this->assertEquals( '\Foo\Bar\Namespace_Page_Type', $actual );
	}

	/**
	 * Test `papi_get_only_objects` function.
	 *
	 * @since 1.1.0
	 */

	public function test_papi_get_only_objects() {
		$actual = true;

		$items = papi_get_only_objects( [ 1, 3, new stdClass, 'hej', [] ] );

		foreach ( $items as $item ) {
			$actual = is_object( $item );

			if ( ! $actual ) {
				break;
			}
		}

		$this->assertTrue( $actual );
	}

	/**
	 * Test `papi_get_or_post` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_or_post() {
		$_GET['world'] = 'hello';

		$this->assertEquals( 'hello', papi_get_or_post( 'world' ) );
		unset( $_GET['world'] );

		$_POST['world'] = 'hello';

		$this->assertEquals( 'hello', papi_get_or_post( 'world' ) );
		unset( $_POST['world'] );

		$this->assertEmpty( papi_get_or_post( null ) );
		$this->assertEmpty( papi_get_or_post( true ) );
		$this->assertEmpty( papi_get_or_post( false ) );
		$this->assertEmpty( papi_get_or_post( 1 ) );
		$this->assertEmpty( papi_get_or_post( [] ) );
		$this->assertEmpty( papi_get_or_post( new stdClass() ) );
		$this->assertEMpty( papi_get_or_post( 'trams' ) );
	}

	/**
	 * Test `papi_get_qs` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_qs() {
		$_GET['page'] = 1;

		$this->assertEquals( 1, papi_get_qs( 'page' ) );
		unset( $_GET['page'] );

		$this->assertEmpty( papi_get_qs( null ) );
		$this->assertEmpty( papi_get_qs( true ) );
		$this->assertEmpty( papi_get_qs( false ) );
		$this->assertEmpty( papi_get_qs( 1 ) );
		$this->assertEmpty( papi_get_qs( [] ) );
		$this->assertEmpty( papi_get_qs( new stdClass() ) );
		$this->assertEmpty( papi_get_qs( '' ) );

		$_GET['good'] = 'true';
		$_GET['bad'] = 'false';
		$qs = papi_get_qs( array( 'good', 'bad' ), true );
		$qs2 = array( 'good' => true, 'bad' => false );
		$this->assertEquals( $qs, $qs2 );
	}

	/**
	 * Test `papi_get_sanitized_post` function.
	 *
	 * @since 1.3.0
	 */

	public function test_papi_get_sanitized_post() {
		$this->assertEmpty( papi_get_sanitized_post( 'hello' ) );
		$_POST['hello'] = '<';
		$this->assertEquals( '&lt;', papi_get_sanitized_post( 'hello' ) );
		unset( $_POST['hello'] );
		$_POST['tag'] = '<tag>';
		$this->assertEmpty( papi_get_sanitized_post( 'tag' ) );
		unset( $_POST['tag'] );
	}

	/**
	 * Test `papi_h` function.
	 *
	 * @since 1.3.0
	 */

	public function test_papi_h() {
		$this->assertEmpty( papi_h( '', '' ) );
		$this->assertEquals( 'papi', papi_h( 'papi', '' ) );
	}

	/**
	 * Test `papi_html_name` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_html_name() {
		$this->assertEquals( 'papi_hello_world_aao', papi_html_name( 'hello world åäö' ) );
		$this->assertEquals( 'papi_hello', papi_html_name( 'papi_hello' ) );
		$this->assertEquals( '_papi_hello', papi_html_name( '_papi_hello' ) );
		$this->assertEquals( 'papi_hello[0][image]', papi_html_name( 'papi_hello[0][image]' ) );
		$this->assertEquals( 'papi_hello[image]', papi_html_name( 'papi_hello[image]' ) );
		$this->assertEmpty( papi_html_name( null ) );
		$this->assertEmpty( papi_html_name( true ) );
		$this->assertEmpty( papi_html_name( false ) );
		$this->assertEmpty( papi_html_name( 1 ) );
		$this->assertEmpty( papi_html_name( [] ) );
		$this->assertEmpty( papi_html_name( new stdClass() ) );
	}

	/**
	 * Test `papi_is_empty` function.
	 *
	 * @since 1.0.3
	 */

	public function test_papi_is_empty() {
		$this->assertTrue( papi_is_empty( null ) );
		$this->assertFalse( papi_is_empty( 'false' ) );
		$this->assertFalse( papi_is_empty( true ) );
		$this->assertFalse( papi_is_empty( false ) );
		$this->assertFalse( papi_is_empty( 0 ) );
		$this->assertFalse( papi_is_empty( 0.0 ) );
		$this->assertFalse( papi_is_empty( "0" ) );
	}

	/**
	 * Test `papi_is_ext` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_is_ext() {
		$this->assertTrue( papi_is_ext( 'index.php', 'php' ) );
		$this->assertFalse( papi_is_ext( null, 'php' ) );
		$this->assertFalse( papi_is_ext( true, 'php' ) );
		$this->assertFalse( papi_is_ext( false, 'php' ) );
		$this->assertFalse( papi_is_ext( 1, 'php' ) );
		$this->assertFalse( papi_is_ext( [], 'php' ) );
		$this->assertFalse( papi_is_ext( new stdClass(), 'php' ) );
	}

	/**
	 * Test `papi_nl2br` function.
	 *
	 * @since 1.2.0
	 */

	public function test_papi_nl2br() {
		$this->assertEquals( papi_nl2br( 'Hello\nWorld' ), 'Hello<br />World' );
		$this->assertEquals( papi_nl2br( "Hello\nWorld" ), "Hello<br />\nWorld" );
	}

	/**
	 * Test `papi_remove_papi` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_remove_papi() {
		$this->assertEquals( 'hello-world', papi_remove_papi( 'papi-hello-world' ) );
		$this->assertEmpty( papi_remove_papi( null ) );
		$this->assertEmpty( papi_remove_papi( true ) );
		$this->assertEmpty( papi_remove_papi( false ) );
		$this->assertEmpty( papi_remove_papi( 1 ) );
		$this->assertEmpty( papi_remove_papi( [] ) );
		$this->assertEmpty( papi_remove_papi( new stdClass() ) );
	}

	/**
	 * Test `papi_remove_trailing_quotes` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_remove_trailing_quotes() {
		$this->assertEquals( '"hello" "world"', papi_remove_trailing_quotes( '\"hello\" \"world\"' ) );
		$this->assertEmpty( papi_remove_trailing_quotes( null ) );
		$this->assertEmpty( papi_remove_trailing_quotes( true ) );
		$this->assertEmpty( papi_remove_trailing_quotes( false ) );
		$this->assertEmpty( papi_remove_trailing_quotes( 1 ) );
		$this->assertEmpty( papi_remove_trailing_quotes( [] ) );
		$this->assertEmpty( papi_remove_trailing_quotes( new stdClass() ) );
	}

	/**
	 * Test `papi_santize_data` function.
	 *
	 * @since 1.3.0
	 */

	public function test_papi_santize_data() {
		$value = papi_santize_data( '\"hello\"' );
		$this->assertEquals( '"hello"', $value );

		$value = papi_santize_data( [
			'\"hello\"',
			'\"world\"'
		] );

		$this->assertEquals( [
			'"hello"',
			'"world"'
		], $value );
	}

	/**
	 * Test `papi_sort_order` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_sort_order() {
		$this->assertEmpty( papi_sort_order( null ) );
		$this->assertEmpty( papi_sort_order( true ) );
		$this->assertEmpty( papi_sort_order( false ) );
		$this->assertEmpty( papi_sort_order( 0 ) );
		$this->assertEmpty( papi_sort_order( [] ) );

		$order = papi_sort_order( papi_property( [
			'type' => 'string',
			'title' => 'Name'
		] ) );
		$this->assertEquals( $order[0]->sort_order, 1000 );

		$order = (object) array(
			'options' => $order[0]
		);
		$order = papi_sort_order( $order );
		$this->assertEquals( $order[0]->options->sort_order, 1000 );

		$order = papi_sort_order( [ (array) papi_property( [
			'type' => 'string',
			'title' => 'Name'
		] ) ] );

		$this->assertEquals( $order[0]['sort_order'], 1000 );
	}

	/**
	 * Test `papi_slugify` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_slugify() {
		$this->assertEquals( 'hello-world-aao', papi_slugify( 'hello world åäö' ) );
		$this->assertEmpty( papi_slugify( null ) );
		$this->assertEmpty( papi_slugify( true ) );
		$this->assertEmpty( papi_slugify( false ) );
		$this->assertEmpty( papi_slugify( 1 ) );
		$this->assertEmpty( papi_slugify( [] ) );
		$this->assertEmpty( papi_slugify( new stdClass() ) );
		$this->assertEquals( 'hello-aao', papi_slugify( 'hello world åäö', [ 'world' ] ) );
	}

	/**
	 * Test `papi_to_array` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_to_array() {
		$this->assertEquals( [ 1 ], papi_to_array( 1 ) );
		$this->assertEquals( [ null ], papi_to_array( null ) );
		$this->assertEquals( [ false ], papi_to_array( false ) );
		$this->assertEquals( [ true ], papi_to_array( true ) );
		$this->assertEquals( [], papi_to_array( [] ) );
		$this->assertEquals( [ new stdClass() ], papi_to_array( new stdClass() ) );
	}

	/**
	 * Test `papi_underscorify` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_underscorify() {
		$this->assertEquals( 'hello_world_it', papi_underscorify( 'hello world-it' ) );
		$this->assertEmpty( papi_underscorify( null ) );
		$this->assertEmpty( papi_underscorify( true ) );
		$this->assertEmpty( papi_underscorify( false ) );
		$this->assertEmpty( papi_underscorify( 1 ) );
		$this->assertEmpty( papi_underscorify( [] ) );
		$this->assertEmpty( papi_underscorify( new stdClass() ) );
	}

	/**
	 * Test `papify` function.
	 *
	 * @since 1.0.0
	 */

	public function test_papify() {
		$this->assertEquals( 'papi_hello_world', papify( 'hello_world' ) );
		$this->assertEquals( 'papi_hello_world', papify( 'papi_hello_world' ) );
		$this->assertEmpty( papify( null ) );
		$this->assertEmpty( papify( true ) );
		$this->assertEmpty( papify( false ) );
		$this->assertEmpty( papify( 1 ) );
		$this->assertEmpty( papify( [] ) );
		$this->assertEmpty( papify( new stdClass() ) );
	}

}
