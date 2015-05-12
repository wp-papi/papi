<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Property` class.
 *
 * @package Papi
 */

class Papi_Property_Test extends WP_UnitTestCase {

	/**
	 * Setup test.
	 *
	 * @since 1.3.0
	 */

	public function setUp() {
		parent::setUp();

		$this->post_id = $this->factory->post->create();

		global $post;
		$post = get_post( $this->post_id );
	}

	/**
	 * Tear down property.
	 *
	 * @since 1.3.0
	 */

	public function tearDown() {
		parent::tearDown();
		global $post;
		unset( $post, $this->post_id );
	}

	/**
	 * Test static `create` method.
	 *
	 * @since 1.3.0
	 */

	public function test_create() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$this->assertEquals( $property->get_option( 'type' ), 'string' );
		$this->assertEquals( $property->get_option( 'title' ), 'Name' );
		$this->assertEquals( $property->get_option( 'slug' ), 'papi_name' );
	}

	/**
	 * Test static `default_options` method.
	 *
	 * @since 1.3.0
	 */

	public function test_default_options() {
		$default_options = Papi_Property::default_options();

		$this->assertTrue( is_array( $default_options ) );
		$this->assertEmpty( $default_options['title'] );
		$this->assertEmpty( $default_options['type'] );
		$this->assertEmpty( $default_options['slug'] );
		$this->assertEquals( 1000, $default_options['sort_order'] );
	}

	/**
	 * Test static `factory` method.
	 *
	 * @since 1.3.0
	 */

	public function test_factory() {
		require_once papi_test_get_fixtures_path( '/properties/class-papi-property-fake.php' );

		$this->assertNull( Papi_Property::factory( null ) );
		$this->assertNull( Papi_Property::factory( '' ) );
		$this->assertNull( Papi_Property::factory( 'fake' ) );

		$this->assertTrue( is_object( Papi_Property::factory( 'string' ) ) );
	}

	/**
	 * Test `format_value` method.
	 *
	 * @since 1.3.0
	 */

	public function test_format_value() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'title' => 'Hello'
		] );

		$actual = $property->format_value( 'Fredrik', '', 0 );

		$this->assertEquals( 'Fredrik', $actual );
	}

	/**
	 * Test `override_property_options` method.
	 *
	 * @since 1.3.0
	 */

	public function test_override_property_options() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'title' => 'Hello'
		] );

		$this->assertEquals( [], $property->override_property_options() );
	}

	/**
	 * Test `get_default_settings` method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_default_settings() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'title' => 'Hello'
		] );

		$this->assertEquals( [], $property->get_default_settings() );
	}

	/**
	 * Test `get_default_value` method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_default_value() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'title' => 'Hello'
		] );

		$this->assertEmpty( $property->get_value() );

		$property->set_options( [
			'type'  => 'string',
			'slug'  => 'name',
			'value' => 'hello world'
		] );

		$this->assertEquals( 'hello world', $property->get_value() );
	}

	/**
	 * Test `get_option` method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_option() {
		$property = Papi_Property::create( [
			'title' => 'Name'
		] );

		$this->assertNull( $property->get_option( 'fake' ) );
		$this->assertEquals( 'Name', $property->get_option( 'title' ) );
		$this->assertEquals( 1000, $property->get_option( 'sort_order' ) );
	}

	/**
	 * Test `get_options` method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_options() {
		$property = new Papi_Property();

		$this->assertEmpty( $property->get_options() );
	}

	/**
	 * Test `get_post_id` method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_post_id() {
		$property = Papi_Property::create();

		$this->assertEquals( $this->post_id, $property->get_post_id() );
	}

	/**
	 * Test `get_settings` method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_settings() {
		$property = new Papi_Property();

		$this->assertEmpty( $property->get_settings() );

		$property->set_options( [
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
		$this->assertEquals( 'faker', $settings->items[0]['type'] );
		$this->assertEquals( 'fake', $settings->items[1]->type );
		$this->assertEquals( 'papi_fake', $settings->items[1]->slug );
	}

	/**
	 * Test `get_value` method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_value() {
		$property = new Papi_Property();

		$this->assertNull( $property->get_value() );

		$property = Papi_Property::create();

		$this->assertEmpty( $property->get_value() );

		$property->set_options( [
			'type'  => 'string',
			'slug'  => 'name',
			'value' => 'Fredrik'
		] );

		$this->assertEquals( 'Fredrik', $property->get_value() );
	}

	/**
	 * Test `html` method.
	 *
	 * @since 1.3.0
	 */

	public function test_html() {
		$property = Papi_Property::create();
		$this->assertEmpty( $property->html() );
	}

	/**
	 * Test `load_value` method.
	 *
	 * @since 1.3.0
	 */

	public function test_load_value() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$actual = $property->load_value( 'Fredrik', '', 0 );

		$this->assertEquals( 'Fredrik', $actual );
	}

	/**
	 * Test `render_description_html` method.
	 *
	 * @since 1.3.0
	 */

	public function test_render_description_html() {
		$property = new Papi_Property();

		$this->assertNull( $property->render_description_html() );

		$property->set_options( [
			'description' => 'A simple description'
		] );

		$property->render_description_html();

		$this->expectOutputRegex( '/A\ssimple\sdescription/' );
	}

	/**
	 * Test `render_hidden_html` method.
	 *
	 * @since 1.3.0
	 */

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

	/**
	 * Test `render_label_html` method.
	 *
	 * @since 1.3.0
	 */

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

	/**
	 * Test `render_row_html` method.
	 *
	 * @since 1.3.0
	 */

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

	/**
	 * Test `set_option` method.
	 *
	 * @since 1.3.0
	 */

	public function test_set_option() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'title' => 'Hello'
		] );

		$property->set_option( 'title', 'Name' );

		$value = $property->get_option( 'title' );

		$this->assertEquals( 'Name', $value );
	}

	/**
	 * Test `set_options` method.
	 *
	 * @since 1.3.0
	 */

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

	/**
	 * Test `update_value` method.
	 *
	 * @since 1.3.0
	 */

	public function test_update_value() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$actual = $property->update_value( 'Fredrik', '', 0 );

		$this->assertEquals( 'Fredrik', $actual );
	}

}
