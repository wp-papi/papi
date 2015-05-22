<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering property functions.
 *
 * @package Papi
 */

class Papi_Lib_Property_Test extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 */

	public function setUp() {
		parent::setUp();

		global $post;

		$this->post_id = $this->factory->post->create();

		$post = get_post( $this->post_id );
	}

	/**
	 * Tear down test.
	 */

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id );
	}

	/**
	 * Test `papi_from_property_array_slugs` function.
	 */

	public function test_papi_from_property_array_slugs() {
		$actual = papi_from_property_array_slugs( [
			'repeater' => 1,
			'repeater_0_image' => 1,
			'_repeater_0_image_property' => 'image'
		], 'repeater' );

		$expected = [
			[
				'image' => 1,
				'_image_property' => 'image'
			]
		];

		$this->assertEquals( $expected, $actual );

		$this->assertEmpty( papi_from_property_array_slugs( [], 'custom_slug' ) );
	}

	/**
	 * Test `papi_get_box_property` function.
	 */

	public function test_papi_get_box_property() {
		$actual = papi_get_box_property( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$this->assertEquals( 'Name', $actual[0]->title );
		$this->assertEquals( 'string', $actual[0]->type );
	}

	/**
	 * Test `papi_get_options_and_properties` function.
	 */

	public function test_papi_get_options_and_properties() {
		$simple_box = PAPI_FIXTURE_DIR . '/boxes/simple.php';

		$options = papi_get_options_and_properties( $simple_box, [
			'test' => 'test'
		] );

		$this->assertEquals( 'Simple', $options[0]['title'] );
		$this->assertEquals( 'test', $options[0]['test'] );
		$this->assertEquals( 'Name', $options[1][0]->title );
		$this->assertEquals( 'string', $options[1][0]->type );

		$options = papi_get_options_and_properties( 'hello' );
		$this->assertEquals( 'hello', $options[0]['title'] );

		$options = papi_get_options_and_properties( [
			'title' => 'Simple',
			papi_property( [
				'type'  => 'string',
				'title' => 'Name'
			] )
		], [], false );

		$this->assertEquals( 'Simple', $options[0]['title'] );
		$this->assertEquals( 'Name', $options[1][0]->title );
		$this->assertEquals( 'string', $options[1][0]->type );

		$options = papi_get_options_and_properties( [
			'title' => 'Simple'
		] );

		$this->assertEquals( 'Simple', $options[0]['title'] );

		$options = papi_get_options_and_properties( [] );

		$this->assertEmpty( $options[0]['title'] );

		$options = papi_get_options_and_properties( [ papi_property( [
			'type'  => 'string',
			'title' => 'Name'
		] ) ] );

		$this->assertEquals( 'Name', $options[0]['title'] );
		$this->assertEquals( 'Name', $options[1][0]->title );
		$this->assertEquals( 'string', $options[1][0]->type );

		$options = papi_get_options_and_properties( [ papi_property( [
			'type'     => 'string',
			'title'    => 'Name',
			'sidebar'  => false,
			'required' => true
		] ) ] );

		$this->assertEquals( 'Name', $options[0]['title'] );
		$this->assertEquals( 'Name', $options[1][0]->title );
		$this->assertEquals( 'string', $options[1][0]->type );
		$this->assertTrue( $options[0]['_required'] );

		$options = papi_get_options_and_properties( [ (object) [
			'options' => [
				'title' => 'Name'
			]
		] ] );

		$this->assertEquals( 'Name', $options[0]['title'] );
	}

	/**
	 * Test `papi_get_property_options` function.
	 */

	public function test_papi_get_property_options() {
		$options = papi_get_property_options( [
			'type' 		   => 'string',
			'title' 	   => 'Name',
			'capabilities' => 'admin'
		] );

		$this->assertEquals( 'Name', $options->title );
		$this->assertEquals( 'string', $options->type );
		$this->assertEquals( 'papi_name', $options->slug );
		$this->assertEquals( ['admin'], $options->capabilities );

		$options = papi_get_property_options( [
			'type' 	   => 'string'
		] );

		$this->assertEquals( 'papi_string', $options->slug );

		$this->assertEquals( new stdClass(), papi_get_property_options( new stdClass() ) );
		$this->assertEmpty( papi_get_property_options( null ) );
	}

	/**
	 * Test `papi_get_property_class_name` function.
	 */

	public function test_papi_get_property_class_name() {
		$this->assertEquals( 'Papi_Property_String', papi_get_property_class_name( 'PropertyString' ) );
		$this->assertEquals( 'Papi_Property_String', papi_get_property_class_name( 'string' ) );
		$this->assertEquals( 'Papi_Property_Fake', papi_get_property_class_name( 'fake' ) );
		$this->assertNull( papi_get_property_class_name( null ) );
		$this->assertNull( papi_get_property_class_name( false ) );
		$this->assertNull( papi_get_property_class_name( true ) );
		$this->assertNull( papi_get_property_class_name( [] ) );
		$this->assertNull( papi_get_property_class_name( new stdClass() ) );
		$this->assertNull( papi_get_property_class_name( 1 ) );
	}

	/**
	 * Test `papi_get_property_type` function.
	 */

	public function test_papi_get_property_type() {
		$this->assertTrue( papi_get_property_type( 'string' ) instanceof Papi_Property_String );
		$this->assertFalse( papi_get_property_type( null ) instanceof Papi_Property_String );
		$this->assertFalse( papi_get_property_type( 'fake' ) instanceof Papi_Property_String );
		$this->assertFalse( papi_get_property_type( 1 ) instanceof Papi_Property_String );
		$this->assertFalse( papi_get_property_type( true ) instanceof Papi_Property_String );
		$this->assertFalse( papi_get_property_type( false ) instanceof Papi_Property_String );
		$this->assertFalse( papi_get_property_type( [] ) instanceof Papi_Property_String );
		$this->assertFalse( papi_get_property_type( new stdClass() ) instanceof Papi_Property_String );
		$options = papi_get_property_options( [
			'type' => 'string'
		] );
		$this->assertTrue( papi_get_property_type( $options ) instanceof Papi_Property_String );
	}

	/**
	 * Test `papi_get_property_type_from_base64` function.
	 */

	public function test_papi_get_property_type_from_base64() {
		$this->assertNull( papi_get_property_type_from_base64( 'hello, world' ) );
		$base64_str = 'Tzo4OiJzdGRDbGFzcyI6MTU6e3M6MTA6ImFsbG93X2h0bWwiO2I6MDtzOjEyOiJjYXBhYmlsaXRpZXMiO2E6MDp7fXM6NzoiZGVmYXVsdCI7czowOiIiO3M6MTE6ImRlc2NyaXB0aW9uIjtzOjA6IiI7czo4OiJkaXNhYmxlZCI7YjowO3M6NDoibGFuZyI7YjowO3M6MzoicmF3IjtiOjE7czo4OiJzZXR0aW5ncyI7Tzo4OiJzdGRDbGFzcyI6MTp7czoxMzoibWVkaWF1cGxvYWRlciI7YjowO31zOjc6InNpZGViYXIiO2I6MTtzOjQ6InNsdWciO3M6NDg6InBhcGlfY29sbGFib3JhdG9yc19yZXBlYXRlclswXVtjb2xsYWJvcmF0b3JfdXJsXSI7czoxMDoic29ydF9vcmRlciI7aToxMDAwO3M6ODoicmVxdWlyZWQiO2I6MDtzOjU6InRpdGxlIjtzOjQ6IkxpbmsiO3M6NDoidHlwZSI7czozOiJ1cmwiO3M6NToidmFsdWUiO3M6MDoiIjt9';
		$actual = papi_get_property_type_from_base64( $base64_str );
		$this->assertEquals( 'url', $actual );
		$base64_str = 'YToxOntpOjA7czoxMToiaGVsbG8sd29ybGQiO30';
		$actual = papi_get_property_type_from_base64( $base64_str );
		$this->assertNull( $actual );
	}

	/**
	 * Test `papi_get_property_type` function.
	 * Will load a custom property and test if it exists.
	 */

	public function test_papi_get_property_type_custom() {
		add_action( 'papi_include_properties', function() {
			require_once PAPI_FIXTURE_DIR . '/properties/class-papi-property-kvack.php';
		} );

		do_action( 'papi_include_properties' );

		$this->assertTrue( papi_get_property_type( 'kvack' ) instanceof Papi_Property_Kvack );
	}

	/**
	 * Test `papi_get_property_type_key` function.
	 */

	public function test_papi_get_property_type_key() {
		$this->assertEquals( 'image_property', papi_get_property_type_key( 'image' ) );
		$this->assertEquals( '_property', papi_get_property_type_key( null ) );
		$this->assertEquals( '_property', papi_get_property_type_key( false ) );
		$this->assertEquals( '_property', papi_get_property_type_key( true ) );
		$this->assertEquals( '_property', papi_get_property_type_key( [] ) );
		$this->assertEquals( '_property', papi_get_property_type_key( 1 ) );
		$this->assertEquals( '_property', papi_get_property_type_key( new stdClass() ) );
		$this->assertEquals( 'my_slug[0][key_property]', papi_get_property_type_key( 'my_slug[0][key]' ) );
	}

	/**
	 * Test `papi_get_property_type_key_f` function.
	 */

	public function test_papi_get_property_type_f() {
		$this->assertEquals( '_image_property', papi_get_property_type_key_f( 'image' ) );
		$this->assertEquals( '_property', papi_get_property_type_key_f( null ) );
		$this->assertEquals( '_property', papi_get_property_type_key_f( true ) );
		$this->assertEquals( '_property', papi_get_property_type_key_f( false ) );
		$this->assertEquals( '_property', papi_get_property_type_key_f( 1 ) );
		$this->assertEquals( '_property', papi_get_property_type_key_f( [] ) );
		$this->assertEquals( '_property', papi_get_property_type_key_f( new stdClass() ) );
	}

	/**
	 * Test `papi_is_property_type_key` function.
	 */

	public function test_papi_is_property_type_key() {
		$this->assertTrue( papi_is_property_type_key( 'image_property' ) );
		$this->assertTrue( papi_is_property_type_key( '_image_property' ) );
		$this->assertFalse( papi_is_property_type_key( 'kvack' ) );
		$this->assertFalse( papi_is_property_type_key( 1 ) );
		$this->assertFalse( papi_is_property_type_key( false ) );
		$this->assertFalse( papi_is_property_type_key( true ) );
	}

	/**
	 * Test `papi_render_property` function.
	 */

	public function test_papi_render_property() {
		$property = papi_property( [
			'title' => 'Name'
		] );
		$this->assertEmpty( papi_render_property( $property ) );

		$property = papi_property( [
			'type'  => 'fake',
			'title' => 'Name'
		] );
		$this->assertEmpty( papi_render_property( $property ) );

		$user_id = $this->factory->user->create( [ 'role' => 'administrator' ] );
		wp_set_current_user( $user_id );

		$_GET = [ 'lang' => 'se' ];
		$property = papi_property( [
			'type'  => 'string',
			'title' => 'Name',
			'lang'  => 'dk',
			'capabilities' => 'administrator'
		] );
		$this->assertEmpty( papi_render_property( $property ) );
		$this->expectOutputRegex( '//' );
		unset( $_GET );
	}

	/**
	 * Test `papi_render_properties` function.
	 */

	public function test_papi_render_properties() {
		$this->assertEmpty( papi_render_properties( null ) );

		$tab = papi_tab( 'Content', [] );
		papi_render_properties( [ $tab ] );
		$this->expectOutputRegex( '/class\=\"papi\-tabs\-wrapper\"/' );

		$property = papi_property( [
			'type'  => 'string',
			'title' => 'Name'
		] );
		papi_render_properties( [ $property ] );
		$this->expectOutputRegex( '/table\s*class\=\"papi\-table\"/' );
	}

	/**
	 * Test `papi_require_text` function.
	 */

	public function test_papi_require_text() {
		$this->assertEmpty( papi_require_text( null ) );
		$property = papi_property( [
			'type'     => 'string',
			'title'    => 'Name',
			'required' => true
		] );
		$this->assertEquals( '(required field)', papi_require_text( $property ) );
	}

	/**
	 * Test `papi_required_html` function.
	 */

	public function test_papi_required_html() {
		$this->assertEmpty( papi_required_html( null ) );
		$property = papi_property( [
			'type'     => 'string',
			'title'    => 'Name',
			'required' => true
		] );
		$this->assertRegExp( '/class\=\"papi\-rq\"/', papi_required_html( $property ) );
	}

	/**
	 * Test `papi_populate_properties` function.
	 */

	public function test_papi_populate_properties() {
		$actual = papi_populate_properties( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$this->assertEquals( 'Name', $actual[0]->title );
		$this->assertEquals( 'string', $actual[0]->type );

		$this->assertEquals( [ $actual[0] ], papi_populate_properties( $actual[0] ) );

		$actual = papi_populate_properties( [
			[
				'type'  => 'string',
				'title' => 'Name 1'
			],
			[
				'type'  => 'string',
				'title' => 'Name 2'
			]
		] );

		$this->assertEquals( 'Name 1', $actual[0]->title );
		$this->assertEquals( 'string', $actual[0]->type );

		$this->assertEquals( 'Name 2', $actual[1]->title );
		$this->assertEquals( 'string', $actual[1]->type );

		$tab = papi_tab( 'Content', [] );
		$actual = papi_populate_properties( [$tab] );

		$this->assertEquals( 'Content', $actual[0]->options['title'] );
		$this->assertEmpty( $actual[0]->properties );

		$actual = papi_populate_properties( [
			[
				'type'  	 => 'string',
				'title' 	 => 'Name 1',
				'sort_order' => 1
			],
			[
				'type'  	 => 'string',
				'title' 	 => 'Name 3'
			],
			[
				'type'  	 => 'string',
				'title' 	 => 'Name 2',
				'sort_order' => 0
			]
		] );

		$this->assertEquals( 'Name 2', $actual[0]->title );
		$this->assertEquals( 'string', $actual[0]->type );

		$this->assertEquals( 'Name 1', $actual[1]->title );
		$this->assertEquals( 'string', $actual[1]->type );

		$this->assertEquals( 'Name 3', $actual[2]->title );
		$this->assertEquals( 'string', $actual[2]->type );
	}

	/**
	 * Test `papi_property` function.
	 */

	public function test_papi_property() {
		$actual = papi_property( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$this->assertEquals( 'Name', $actual->title );
		$this->assertEquals( 'string', $actual->type );

		$actual = papi_property( [] );

		$this->assertEmpty( $actual->title );
		$this->assertEmpty( $actual->type );
		$this->assertTrue( $actual->sidebar );

		$this->assertEquals( new stdClass(), papi_property( new stdClass() ) );

		$this->assertEmpty( papi_property( null ) );
		$this->assertEmpty( papi_property( true ) );
		$this->assertEmpty( papi_property( false ) );
		$this->assertEmpty( papi_property( 1 ) );
	}

	/**
	 * Test papi property template.
	 */

	public function test_papi_property_template() {
		$actual = papi_property( PAPI_FIXTURE_DIR . '/properties/simple.php' );

		$this->assertEquals( 'Name', $actual->title );
		$this->assertEquals( 'string', $actual->type );
	}

	/**
	 * Test `papi_property_update_meta` function.
	 */

	public function test_papi_property_update_meta() {
		$this->assertEmpty( papi_property_update_meta( [
			'post_id'       => $this->post_id,
			'slug'          => 'name',
			'value'         => 'Fredrik'
		] ) );

		papi_property_update_meta( [
			'post_id'       => $this->post_id,
			'slug'          => 'name',
			'value'         => 'Fredrik'
		] );

		$this->assertEquals( 'Fredrik', get_post_meta( $this->post_id, 'name', true ) );

		papi_property_update_meta( [
			'post_id'       => $this->post_id,
			'slug'          => 'name',
			'value'         => ''
		] );

		$this->assertEmpty( get_post_meta( $this->post_id, 'name', true ) );

		papi_property_update_meta( [
			'post_id'       => $this->post_id,
			'slug'          => 'what',
			'value'         => [
				'firstname' => 'Fredrik'
			]
		] );

		$this->assertEquals( 'Fredrik', get_post_meta( $this->post_id, 'firstname', true ) );

		papi_property_update_meta( [
			'post_id'       => $this->post_id,
			'slug'          => 'what',
			'value'         => [
				'Fredrik'
			]
		] );

		$this->assertEquals( ['Fredrik'], get_post_meta( $this->post_id, 'what', true ) );
	}

	/**
	 * Test `papi_to_property_array_slugs` function.
	 */

	public function test_papi_to_property_array_slugs() {
		$actual = papi_to_property_array_slugs( [
			[
				'image' => 1,
				'image_property' => 'image'
			]
		], 'repeater' );

		$expected = [
			'repeater' => 1,
			'repeater_0_image' => 1,
			'_repeater_0_image_property' => 'image'
		];

		$this->assertEquals( $expected, $actual );

		$actual = papi_to_property_array_slugs( [ 1, '', true, false ], 'repeater' );

		$expected = [
			'repeater' => 0
		];

		$this->assertEquals( $expected, $actual );
	}

}
