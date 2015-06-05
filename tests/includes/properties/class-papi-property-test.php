<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Property` class.
 *
 * @package Papi
 */

class Papi_Property_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		$this->post_id = $this->factory->post->create();

		$_GET['post'] = $this->post_id;
	}

	public function tearDown() {
		parent::tearDown();
		unset( $_GET, $this->post_id );
	}

	public function test_create() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$this->assertEquals( $property->get_option( 'type' ), 'string' );
		$this->assertEquals( $property->get_option( 'title' ), 'Name' );
		$this->assertEquals( $property->get_option( 'slug' ), 'papi_name' );
	}

	public function test_default_options() {
		$default_options = Papi_Property::default_options();
		$this->assertTrue( is_array( $default_options ) );
		$this->assertEmpty( $default_options['title'] );
		$this->assertEmpty( $default_options['type'] );
		$this->assertEmpty( $default_options['slug'] );
		$this->assertEquals( 1000, $default_options['sort_order'] );
	}

	public function test_factory() {
		require_once PAPI_FIXTURE_DIR . '/properties/class-papi-property-fake.php';

		$this->assertNull( Papi_Property::factory( null ) );
		$this->assertNull( Papi_Property::factory( '' ) );
		$this->assertNull( Papi_Property::factory( 'fake' ) );

		$this->assertTrue( is_object( Papi_Property::factory( 'string' ) ) );
	}

	public function test_format_value() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'title' => 'Hello'
		] );

		$actual = $property->format_value( 'Fredrik', '', 0 );

		$this->assertEquals( 'Fredrik', $actual );
	}

	public function test_get_default_settings() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'title' => 'Hello'
		] );

		$this->assertEquals( [], $property->get_default_settings() );
	}

	public function test_get_default_value() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'title' => 'Hello'
		] );

		$this->assertNull( $property->get_value() );

		$property->set_options( [
			'type'  => 'string',
			'slug'  => 'name',
			'value' => 'hello world'
		] );

		$this->assertEquals( 'hello world', $property->get_value() );
	}

	public function test_get_option() {
		$property = Papi_Property::create( [
			'title' => 'Name'
		] );

		$this->assertNull( $property->fake );
		$this->assertNull( $property->get_option( 'fake' ) );
		$this->assertEquals( 'Name', $property->title );
		$this->assertEquals( 'Name', $property->get_option( 'title' ) );
		$this->assertEquals( 1000, $property->sort_order );
		$this->assertEquals( 1000, $property->get_option( 'sort_order' ) );

		$property->title = 'Link';

		$this->assertEquals( 'Link', $property->title );

		$settings = $property->get_option( 'settings' );
		$this->assertTrue( is_object( $settings ) );
	}

	public function test_get_options() {
		$property = new Papi_Property();

		$this->assertEmpty( $property->get_options() );

		$property = Papi_Property::create( [
			'title' => 'Name'
		] );

		$options = $property->get_options();

		$this->assertEquals( 'Name', $options->title );
	}

	public function test_get_post_id() {
		$property = Papi_Property::create();

		$this->assertEquals( $this->post_id, $property->get_post_id() );
	}

	public function test_get_setting() {
		$property = new Papi_Property();
		$this->assertNull( $property->get_setting( 'length' ) );

		$property = Papi_Property::create( [
			'type'     => 'string',
			'settings' => [
				'length' => 50
			]
		] );

		$this->assertEquals( 50, $property->get_setting( 'length' ) );
	}

	public function test_get_settings() {
		$property = Papi_Property::create( [
			'settings' => [
				'items' => [
					[
						'type' => 'faker'
					],
					[
						'type' => 'fake'
					]
				]
			]
		] );

		$settings = $property->get_settings();

		$this->assertTrue( is_object( $settings ) );
		$this->assertEmpty( $settings->items );
	}

	public function test_get_slug() {
		$property = Papi_Property::create();

		// this will not be empty since a property without a slug will get a generated uniq id.
		$this->assertRegExp( '/papi\_\w+/', $property->get_slug() );

		$property->set_options( [
			'type'  => 'string',
			'slug'  => 'name',
			'value' => 'Fredrik'
		] );

		$this->assertEquals( 'papi_name', $property->get_slug() );

		$this->assertEquals( 'name', $property->get_slug( true ) );
	}

	public function test_match_slug() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'slug'  => 'name',
			'value' => 'Fredrik'
		] );

		$this->assertTrue( $property->match_slug( 'name' ) );
		$this->assertTrue( $property->match_slug( 'papi_name' ) );

		$this->assertFalse( $property->match_slug( 'kvack' ) );
		$this->assertFalse( $property->match_slug( 'papi_kvack' ) );
		$this->assertFalse( $property->match_slug( null ) );
		$this->assertFalse( $property->match_slug( true ) );
		$this->assertFalse( $property->match_slug( false ) );
		$this->assertFalse( $property->match_slug( 1 ) );
		$this->assertFalse( $property->match_slug( 0 ) );
		$this->assertFalse( $property->match_slug( [] ) );
		$this->assertFalse( $property->match_slug( (object) [] ) );
		$this->assertFalse( $property->match_slug( '' ) );
	}

	public function test_get_value() {
		$property = new Papi_Property();

		$this->assertNull( $property->get_value() );

		$property = Papi_Property::create();

		$this->assertNull( $property->get_value() );

		$property->set_options( [
			'type'  => 'string',
			'slug'  => 'name',
			'value' => 'Fredrik'
		] );

		$this->assertEquals( 'Fredrik', $property->get_value() );
	}

	public function test_html() {
		$property = Papi_Property::create();
		$this->assertEmpty( $property->html() );
	}

	public function test_html_name() {
		$property = Papi_Property::create();
		// this will not be empty since a property without a slug will get a generated uniq id.
		$this->assertRegExp( '/papi\_\w+/', $property->html_name() );

		$property->set_options( [
			'type'  => 'string',
			'slug'  => 'name'
		] );

		$this->assertEquals( 'papi_name', $property->html_name() );

		$sub_property = Papi_Property::create( [
			'type' => 'number',
			'slug' => 'age'
		] );

		$this->assertEquals( 'papi_name[age]', $property->html_name( $sub_property ) );
		$this->assertEquals( 'papi_name[0][age]', $property->html_name( $sub_property, 0 ) );

		$sub_property = (object) array(
			'type' => 'number',
			'slug' => 'age'
		);

		$this->assertEquals( 'papi_name[age]', $property->html_name( $sub_property ) );
		$this->assertEquals( 'papi_name[0][age]', $property->html_name( $sub_property, 0 ) );

		$sub_property = 'non array or object';

		$this->assertEquals( 'papi_name', $property->html_name( $sub_property ) );
		$this->assertEquals( 'papi_name[0]', $property->html_name( $sub_property, 0 ) );
	}

	public function test_load_value() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$actual = $property->load_value( 'Fredrik', '', 0 );

		$this->assertEquals( 'Fredrik', $actual );
	}

	public function test_render_description_html() {
		$property = new Papi_Property();

		$this->assertNull( $property->render_description_html() );

		$property->set_options( [
			'description' => 'A simple description'
		] );

		$property->render_description_html();

		$this->expectOutputRegex( '/A\ssimple\sdescription/' );
	}

	public function test_render_hidden_html() {
		$property = new Papi_Property();

		$this->assertNull( $property->render_hidden_html() );

		$property->set_options( papi_get_property_options( [
			'type' => 'string',
			'slug' => 'hello_world'
		] ) );

		$property->render_hidden_html();

		$this->expectOutputRegex( '/papi\_hello\_world\_property/' );

		$property->set_options( [
			'type' => 'string',
			'slug' => 'hello_world[name]'
		] );

		$property->render_hidden_html();

		$this->expectOutputRegex( '/papi\_hello\_world\[name\_property\]/' );
	}

	public function test_render_label_html() {
		$property = new Papi_Property();

		$this->assertNull( $property->render_label_html() );

		$property->set_options( [
			'slug'  => 'kvack',
			'title' => 'A simple label'
		] );

		$property->render_label_html();

		$this->expectOutputRegex( '/A\ssimple\slabel/' );
		$this->expectOutputRegex( '/papi\_kvack/' );
	}

	public function test_render_row_html() {
		$property = new Papi_Property();

		$this->assertNull( $property->render_row_html() );

		$property->set_options( [
			'title' 	  => 'A simple label',
			'description' => 'A simple description'
		] );

		$property->render_row_html();

		$this->expectOutputRegex( '/A\ssimple\sdescription/' );
		$this->expectOutputRegex( '/A\ssimple\slabel/' );

		$property->set_options( [
			'raw' => true
		] );

		$property->render_row_html();
	}

	public function test_set_option() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'title' => 'Hello'
		] );

		$property->set_option( 'title', 'Name' );

		$value = $property->get_option( 'title' );

		$this->assertEquals( 'Name', $value );
	}

	public function test_set_options() {
		$property = Papi_Property::create( [
			'type'     => 'string',
			'title'    => 'Hello'
		] );

		$property->set_options( [
			'title' => 'Name'
		] );

		$this->assertEquals( 'Name', $property->get_option( 'title' ) );

		$property = Papi_Property::create( [
			'type'     => 'string'
		] );

		$this->assertEquals( 'papi_string', $property->get_option( 'slug' ) );
	}

	public function test_update_value() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$actual = $property->update_value( 'Fredrik', '', 0 );

		$this->assertEquals( 'Fredrik', $actual );
	}

}
