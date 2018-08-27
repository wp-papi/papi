<?php

class Papi_Lib_Core_Utilities_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$_GET  = [];
		$_POST = [];
	}

	public function tearDown() {
		parent::tearDown();
		$_GET = [];
		$_POST = [];
	}

	public function test_papi_camel_case() {
		$this->assertSame( 'fooBar', papi_camel_case( 'foo-bar' ) );
		$this->assertSame( 'fooBar', papi_camel_case( 'foo bar' ) );
		$this->assertSame( 'fooBar', papi_camel_case( 'foo_bar' ) );
		$this->assertNull( papi_camel_case( null ) );
	}

	public function test_papi_convert_to_string() {
		$this->assertSame( 'false', papi_convert_to_string( false ) );
		$this->assertSame( 'true', papi_convert_to_string( true ) );
		$this->assertSame( '0.1', papi_convert_to_string( 0.1 ) );
		$this->assertSame( '1.1', papi_convert_to_string( 1.1 ) );
		$this->assertSame( '0', papi_convert_to_string( 0 ) );
		$this->assertEmpty( papi_convert_to_string( [] ) );
		$this->assertEmpty( papi_convert_to_string( new stdClass() ) );
		$this->assertNotEmpty( papi_convert_to_string( new ReflectionClass( 'ReflectionClass' ) ) );
		$this->assertEmpty( papi_convert_to_string( Papi_Loader::instance() ) );
	}

	public function test_papi_current_user_is_allowed() {
		$this->assertTrue( papi_current_user_is_allowed( null ) );
		$this->assertTrue( papi_current_user_is_allowed( true ) );
		$this->assertTrue( papi_current_user_is_allowed( false ) );
		$this->assertTrue( papi_current_user_is_allowed( 1 ) );
		$this->assertTrue( papi_current_user_is_allowed( new stdClass() ) );
		$this->assertTrue( papi_current_user_is_allowed( [] ) );
		$this->assertTrue( papi_current_user_is_allowed( '' ) );
		$this->assertTrue( papi_current_user_is_allowed() );

		$user_id = $this->factory->user->create( [ 'role' => 'administrator' ] );
		wp_set_current_user( $user_id );
		$this->assertTrue( papi_current_user_is_allowed( 'administrator' ) );
		$this->assertFalse( papi_current_user_is_allowed( 'administrator2' ) );
	}

	public function test_papi_doing_ajax() {
		$out = papi_doing_ajax();

		if ( $out ) {
			$this->assertTrue( $out );
		} else {
			$this->assertFalse( $out );
		}
	}

	public function test_papi_esc_html() {
		$this->assertSame( '&lt;script&gt;alert(1);&lt;/script&gt;', papi_esc_html( '<script>alert(1);</script>' ) );
		$this->assertSame( [ '&lt;script&gt;alert(1);&lt;/script&gt;' => 'hello world&lt;script&gt;alert(1);&lt;/script&gt;' ], papi_esc_html( [ '<script>alert(1);</script>' => 'hello world<script>alert(1);</script>' ] ) );

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

		$this->assertSame( [1], papi_esc_html( [1] ) );
	}

	public function test_papi_f() {
		$this->assertSame( '_page', papi_f( 'page' ) );
		$this->assertSame( '_page', papi_f( '_page' ) );
		$this->assertEmpty( papi_f( null ) );
		$this->assertEmpty( papi_f( true ) );
		$this->assertEmpty( papi_f( false ) );
		$this->assertEmpty( papi_f( 1 ) );
		$this->assertEmpty( papi_f( [] ) );
		$this->assertEmpty( papi_f( new stdClass() ) );
		$this->assertSame( '__page', papi_f( 'page', 2 ) );
		$this->assertSame( '__page', papi_f( '_page', 2 ) );
		$this->assertSame( '__page', papi_f( '__page', 2 ) );
	}

	public function test_papi_maybe_get_callable_value() {
		$this->assertSame( 'Hello', papi_maybe_get_callable_value( 'say_hello_stub' ) );
		$this->assertSame( 'file', papi_maybe_get_callable_value( 'file' ) );
		$this->assertSame( false, papi_maybe_get_callable_value( false ) );
		$this->assertSame( 'Hello Fredrik', papi_maybe_get_callable_value( 'say_hello_name_stub', ['Fredrik'] ) );
		$this->assertSame( 'Hello Fredrik', papi_maybe_get_callable_value( 'say_hello_name_stub', 'Fredrik' ) );
	}

	public function test_papi_get_class_name() {
		$actual = papi_get_class_name( PAPI_FIXTURE_DIR . '/page-types/simple-page-type.php' );
		$this->assertSame( 'Simple_Page_Type', $actual );
		$this->assertEmpty( papi_get_class_name( null ) );
		$this->assertEmpty( papi_get_class_name( true ) );
		$this->assertEmpty( papi_get_class_name( false ) );
		$this->assertEmpty( papi_get_class_name( 1 ) );
		$this->assertEmpty( papi_get_class_name( [] ) );
		$this->assertEmpty( papi_get_class_name( new stdClass() ) );
		$actual = papi_get_class_name( PAPI_FIXTURE_DIR . '/page-types/namespace-page-type.php' );
		$this->assertSame( '\Foo\Bar\Namespace_Page_Type', $actual );
	}

	public function test_papi_get_lang() {
		$lang = papi_get_lang();
		$this->assertEmpty( $lang );

		$_GET['lang'] = 'sv';
		$lang = papi_get_lang();
		$this->assertSame( 'sv', $lang );

		add_filter( 'papi/lang', function () {
			return 'en';
		} );

		$lang = papi_get_lang();
		$this->assertSame( 'en', $lang );

		unset( $_GET['lang'] );
	}

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

	public function test_papi_get_or_post() {
		$_GET['world'] = 'hello';

		$this->assertSame( 'hello', papi_get_or_post( 'world' ) );
		unset( $_GET['world'] );

		$_POST['world'] = 'hello';

		$this->assertSame( 'hello', papi_get_or_post( 'world' ) );
		unset( $_POST['world'] );

		$this->assertEmpty( papi_get_or_post( null ) );
		$this->assertEmpty( papi_get_or_post( true ) );
		$this->assertEmpty( papi_get_or_post( false ) );
		$this->assertEmpty( papi_get_or_post( 1 ) );
		$this->assertEmpty( papi_get_or_post( [] ) );
		$this->assertEmpty( papi_get_or_post( new stdClass() ) );
		$this->assertEMpty( papi_get_or_post( 'trams' ) );
	}

	public function test_papi_get_qs() {
		$_GET['page'] = 1;

		$this->assertSame( 1, papi_get_qs( 'page' ) );
		unset( $_GET['page'] );

        $_GET['page'] = 'papi/option/options/header-option-type';
		$this->assertSame( 'options/header-option-type', papi_get_qs( 'page' ) );
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
		$qs = papi_get_qs( [ 'good', 'bad' ], true );
		$qs2 = [ 'good' => true, 'bad' => false ];
		$this->assertSame( $qs, $qs2 );
	}

	public function test_papi_get_sanitized_post() {
		$this->assertEmpty( papi_get_sanitized_post( 'hello' ) );
		$_POST['hello'] = '<';
		$this->assertSame( '&lt;', papi_get_sanitized_post( 'hello' ) );
		unset( $_POST['hello'] );
		$_POST['tag'] = '<tag>';
		$this->assertEmpty( papi_get_sanitized_post( 'tag' ) );
		unset( $_POST['tag'] );
	}

	public function test_papi_html_name() {
		$this->assertSame( 'papi_hello_world_aao', papi_html_name( 'hello world åäö' ) );
		$this->assertSame( 'papi_hello', papi_html_name( 'papi_hello' ) );
		$this->assertSame( '_papi_hello', papi_html_name( '_papi_hello' ) );
		$this->assertSame( 'papi_hello[0][image]', papi_html_name( 'papi_hello[0][image]' ) );
		$this->assertSame( 'papi_hello[image]', papi_html_name( 'papi_hello[image]' ) );
		$this->assertEmpty( papi_html_name( null ) );
		$this->assertEmpty( papi_html_name( true ) );
		$this->assertEmpty( papi_html_name( false ) );
		$this->assertEmpty( papi_html_name( 1 ) );
		$this->assertEmpty( papi_html_name( [] ) );
		$this->assertEmpty( papi_html_name( new stdClass() ) );
	}

	public function test_papi_html_tag() {
		$this->assertSame( '<label for="test">hello</label>', papi_html_tag( 'label', [
			'for' => 'test',
			'hello'
		] ) );

		$this->assertSame( '<label for="{}">hello</label>', papi_html_tag( 'label', [
			'for' => (object) [],
			'hello'
		] ) );

		$this->assertSame( '<label for="true">hello</label>', papi_html_tag( 'label', [
			'for' => 'true',
			'hello'
		] ) );

		$this->assertSame( '<label for="false">hello</label>', papi_html_tag( 'label', [
			'for' => false,
			'hello'
		] ) );

		$this->assertSame( '<label for="{}">hello world</label>', papi_html_tag( 'label', [
			'for' => (object) [],
			['hello', 'world']
		] ) );

		$this->assertSame( '<label for="{}">', papi_html_tag( 'label', [
			'for' => (object) []
		] ) );

		$this->assertSame( '<label>Hello</label>', papi_html_tag( 'label', 'Hello' ) );

		$this->assertSame( '<label>Hello</label>', papi_html_tag( 'label', papi_maybe_get_callable_value( 'say_hello_stub' ) ) );

		$this->assertSame( '<option selected="selected">hello</option>', papi_html_tag( 'option', [
			'selected' => true,
			'hello'
		] ) );

		$this->assertSame( '<input type="checkbox" checked="checked">', papi_html_tag( 'input', [
			'type'    => 'checkbox',
			'checked' => true
		] ) );

		$this->assertSame( '<textarea></textarea>', papi_html_tag( 'textarea', '' ) );
	}

	public function test_papi_render_html_tag() {
		papi_render_html_tag( 'label', [
			'for' => 'test',
			'hello'
		] );

		$this->expectOutputRegex( '/\<label for\=\"test\"\>hello\<\/label\>/' );

		papi_render_html_tag( 'label', [
			'for' => (object) [],
			'hello'
		] );

		$this->expectOutputRegex( '/\<label for\=\"\{\}\"\>hello\<\/label\>/' );

		papi_html_tag( 'label', [
			'for' => 'true',
			'hello'
		] );

		$this->expectOutputRegex( '/\<label for\=\"true\"\>hello\<\/label\>/' );

		papi_render_html_tag( 'label', [
			'for' => false,
			'hello'
		] );

		$this->expectOutputRegex( '/\<label for\=\"false\"\>hello\<\/label\>/' );

		papi_render_html_tag( 'label', [
			'for' => (object) [],
			['hello', 'world']
		] );

		$this->expectOutputRegex( '/\<label for\=\"\{\}\"\>hello world\<\/label\>/' );

		papi_render_html_tag( 'label', [
			'for' => (object) [],
			papi_maybe_get_callable_value( 'say_hello_stub' )
		] );

		$this->expectOutputRegex( '/\<label for\=\"\{\}\"\>Hello\<\/label\>/' );
	}

	public function test_papi_is_empty() {
		$this->assertTrue( papi_is_empty( [null, null] ) );
		$this->assertFalse( papi_is_empty( [4, null] ) );
		$this->assertTrue( papi_is_empty( null ) );
		$this->assertTrue( papi_is_empty( '' ) );
		$this->assertFalse( papi_is_empty( 'false' ) );
		$this->assertFalse( papi_is_empty( true ) );
		$this->assertFalse( papi_is_empty( false ) );
		$this->assertFalse( papi_is_empty( 0 ) );
		$this->assertFalse( papi_is_empty( 0.0 ) );
		$this->assertFalse( papi_is_empty( '0' ) );
	}

	public function test_papi_is_json() {
		$this->assertTrue( papi_is_json( '{"yes":true}' ) );
		$this->assertTrue( papi_is_json( '{}' ) );
		$this->assertTrue( papi_is_json( '[1, 2, 3]' ) );
		$this->assertTrue( papi_is_json( '[]' ) );
		$this->assertFalse( papi_is_json( '12345' ) );
		$this->assertFalse( papi_is_json( 12345 ) );
		$this->assertFalse( papi_is_json( 123.45 ) );
		$this->assertFalse( papi_is_json( true ) );
		$this->assertFalse( papi_is_json( false ) );
		$this->assertFalse( papi_is_json( null ) );
		$this->assertFalse( papi_is_json( [] ) );
		$this->assertFalse( papi_is_json( (object) [] ) );
	}

	public function test_papi_maybe_convert_to_array() {
		$this->assertEquals( [], papi_maybe_convert_to_array( (object) [] ) );
		$this->assertSame( true, papi_maybe_convert_to_array( true ) );
		$this->assertSame( false, papi_maybe_convert_to_array( false ) );
		$this->assertSame( null, papi_maybe_convert_to_array( null ) );
		$this->assertSame( [], papi_maybe_convert_to_array( [] ) );
		$this->assertSame( 1, papi_maybe_convert_to_array( 1 ) );
		$this->assertSame( 'hello', papi_maybe_convert_to_array( 'hello' ) );
	}

	public function test_papi_maybe_convert_to_object() {
		$this->assertEquals( (object) [], papi_maybe_convert_to_object( [] ) );
		$this->assertSame( true, papi_maybe_convert_to_object( true ) );
		$this->assertSame( false, papi_maybe_convert_to_object( false ) );
		$this->assertSame( null, papi_maybe_convert_to_object( null ) );
		$this->assertEquals( (object) [], papi_maybe_convert_to_object( (object) [] ) );
		$this->assertSame( 1, papi_maybe_convert_to_object( 1 ) );
		$this->assertSame( 'hello', papi_maybe_convert_to_object( 'hello' ) );
	}

	public function test_papi_maybe_json_decode() {
		$this->assertEquals( (object) ['yes' => true], papi_maybe_json_decode( '{"yes":true}' ) );
		$this->assertEquals( (object) [], papi_maybe_json_decode( '{}' ) );
		$this->assertSame( [1, 2, 3], papi_maybe_json_decode( '[1, 2, 3]', true ) );
		$this->assertSame( [], papi_maybe_json_decode( '[]', true ) );
		$this->assertSame( '12345', papi_maybe_json_decode( '12345' ) );
		$this->assertSame( 12345, papi_maybe_json_decode( 12345 ) );
		$this->assertSame( 123.45, papi_maybe_json_decode( 123.45 ) );
		$this->assertSame( true, papi_maybe_json_decode( true ) );
		$this->assertSame( false, papi_maybe_json_decode( false ) );
		$this->assertSame( null, papi_maybe_json_decode( null ) );
		$this->assertSame( [], papi_maybe_json_decode( [] ) );
		$this->assertEquals( (object) [], papi_maybe_json_decode( (object) [] ) );
	}

	public function test_papi_maybe_json_encode() {
		$this->assertSame( '{"name":"Malmö"}', papi_maybe_json_encode( ['name' => 'Malmö'] ) );
		$this->assertTrue( is_string( papi_maybe_json_encode( (object) ['yes' => true] ) ) );
		$this->assertTrue( is_string( papi_maybe_json_encode( (object) [] ) ) );
		$this->assertTrue( is_string( papi_maybe_json_encode( [1, 2 , 3] ) ) );
		$this->assertTrue( is_string( papi_maybe_json_encode( [] ) ) );
		$this->assertFalse( is_string( papi_maybe_json_encode( 1 ) ) );
		$this->assertFalse( is_string( papi_maybe_json_encode( 123.4 ) ) );
		$this->assertFalse( is_string( papi_maybe_json_encode( true ) ) );
		$this->assertFalse( is_string( papi_maybe_json_encode( false ) ) );
		$this->assertFalse( is_string( papi_maybe_json_encode( null ) ) );
	}

	public function test_papi_nl2br() {
		$this->assertSame( papi_nl2br( 'Hello\nWorld' ), 'Hello<br />World' );
		$this->assertSame( papi_nl2br( "Hello\nWorld" ), "Hello<br />\nWorld" );
	}

	public function test_papi_santize_data() {
		$value = papi_santize_data( '\"hello\"' );
		$this->assertSame( '"hello"', $value );

		$value = papi_santize_data( [
			'\"hello\"',
			'\"world\"'
		] );

		$this->assertSame( [
			'"hello"',
			'"world"'
		], $value );
	}

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
		$this->assertSame( $order[0]->sort_order, 1000 );

		$order = papi_sort_order( [ papi_property( [
			'type' => 'string',
			'title' => 'Name'
		] ) ] );

		$this->assertSame( $order[0]->sort_order, 1000 );
	}

	public function test_papi_slugify() {
		$this->assertSame( 'hello-world-aao', papi_slugify( 'hello world åäö' ) );
		$this->assertEmpty( papi_slugify( null ) );
		$this->assertEmpty( papi_slugify( true ) );
		$this->assertEmpty( papi_slugify( false ) );
		$this->assertEmpty( papi_slugify( 1 ) );
		$this->assertEmpty( papi_slugify( [] ) );
		$this->assertEmpty( papi_slugify( new stdClass ) );
		$this->assertSame( 'hello-aao', papi_slugify( 'hello world åäö', [ 'world' ] ) );
	}

	public function test_papi_slugify_locale() {
		$locale = setlocale( LC_ALL, '0' );
		$this->assertSame( 'hello-world-aao', papi_slugify( 'hello world åäö' ) );
		$this->assertSame( $locale, setlocale( LC_ALL, '0' ) );
	}

	public function test_papi_to_array() {
		$this->assertSame( [ 1 ], papi_to_array( 1 ) );
		$this->assertSame( [ null ], papi_to_array( null ) );
		$this->assertSame( [ false ], papi_to_array( false ) );
		$this->assertSame( [ true ], papi_to_array( true ) );
		$this->assertSame( [], papi_to_array( [] ) );
		$this->assertEquals( [ new stdClass ], papi_to_array( new stdClass ) );
	}

	public function test_papi_underscorify() {
		$this->assertSame( 'hello_world_it', papi_underscorify( 'hello world-it' ) );
		$this->assertEmpty( papi_underscorify( null ) );
		$this->assertEmpty( papi_underscorify( true ) );
		$this->assertEmpty( papi_underscorify( false ) );
		$this->assertEmpty( papi_underscorify( 1 ) );
		$this->assertEmpty( papi_underscorify( [] ) );
		$this->assertEmpty( papi_underscorify( new stdClass ) );
	}

	public function test_papify() {
		$this->assertSame( 'papi_hello_world', papify( 'hello_world' ) );
		$this->assertSame( 'papi_hello_world', papify( 'papi_hello_world' ) );
		$this->assertSame( 'papi_hello_world', papify( '_hello_world' ) );
		$this->assertEmpty( papify( null ) );
		$this->assertEmpty( papify( true ) );
		$this->assertEmpty( papify( false ) );
		$this->assertEmpty( papify( 1 ) );
		$this->assertEmpty( papify( [] ) );
		$this->assertEmpty( papify( new stdClass ) );
	}

	public function test_unpapify() {
		$this->assertSame( 'hello-world', unpapify( 'papi-hello-world' ) );
		$this->assertEmpty( unpapify( null ) );
		$this->assertEmpty( unpapify( true ) );
		$this->assertEmpty( unpapify( false ) );
		$this->assertEmpty( unpapify( 1 ) );
		$this->assertEmpty( unpapify( [] ) );
		$this->assertEmpty( unpapify( new stdClass() ) );
	}
}
