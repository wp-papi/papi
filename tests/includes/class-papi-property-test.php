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

		$this->property = new Papi_Property( $this->factory->post->create() );
	}

	/**
	 * Tear down property.
	 *
	 * @since 1.3.0
	 */

	public function tearDown() {
		parent::tearDown();
		unset( $this->property );
	}

	/**
	 * Test description method.
	 *
	 * @since 1.3.0
	 */

	public function test_description() {
		$this->assertNull( $this->property->render_description_html() );

		$this->property->set_options( array(
			'description' => 'A simple description'
		) );

		$this->property->render_description_html();

		$this->expectOutputRegex( '/A\ssimple\sdescription/' );
	}

	/**
	 * Test static factory method.
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
	 * Test hidden method.
	 *
	 * @since 1.3.0
	 */

	public function test_hidden() {
		$this->assertNull( $this->property->render_hidden_html() );

		$this->property->set_options( papi_get_property_options( array(
			'type' => 'string',
			'slug' => 'hello_world'
		) ) );

		$this->property->render_hidden_html();

		$this->expectOutputRegex( '/papi\_hello\_world\_property/' );

		$this->property->set_options( array(
			'type' => 'string',
			'slug' => 'hello_world[name]'
		) );

		$this->property->render_hidden_html();

		$this->expectOutputRegex( '/papi\_hello\_world\[name\_property\]/' );
	}

	/**
	 * Test empty html.
	 *
	 * @since 1.3.0
	 */

	public function test_html() {
		$this->assertEmpty( $this->property->html() );
	}

	/**
	 * Test `override_property_options` method.
	 *
	 * @since 1.3.0
	 */

	public function test_override_property_options() {
		$this->assertEquals( array(), $this->property->override_property_options() );
	}

	/**
	 * Test `get_default_settings` method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_default_settings() {
		$this->assertEquals( array(), $this->property->get_default_settings() );
	}

	/**
	 * Test `get_options` method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_options() {
		$this->assertEmpty( $this->property->get_options() );
	}

	/**
	 * Test `set_options` method.
	 *
	 * @since 1.3.0
	 */

	public function test_set_options() {
		$this->property->set_options( array(
			'title' => 'Name'
		) );

		$value = $this->property->get_option( 'title' );

		$this->assertEquals( 'Name', $value );
	}

	/**
	 * Test `get_value` method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_value() {
		//$this->assertEmpty( $this->property->get_value() );

		$this->property->set_options( papi_get_property_options( array(
			'slug'  => 'name',
			'value' => 'Fredrik'
		) ) );

		$this->assertEquals( 'Fredrik', $this->property->get_value() );
	}

	/**
	 * Test get_default_value method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_default_value() {
		$this->property->set_options( papi_get_property_options( array(
			'slug'  => 'name',
			'value' => ''
		) ) );

		$this->assertEmpty( $this->property->get_value() );

		$this->property->default_value = array();

		$this->assertTrue( is_array( $this->property->get_value() ) );
	}

	/**
	 * Test get_settings method.
	 *
	 * @since 1.3.0
	 */

	public function test_get_settings() {
		$this->assertEmpty( $this->property->get_settings() );

		$this->property->set_options( array( 'settings' => array() ) );

		$this->assertTrue( is_object( $this->property->get_settings() ) );
	}

	/**
	 * Test `render_label_html` method.
	 *
	 * @since 1.3.0
	 */

	public function test_render_label_html() {
		$this->assertNull( $this->property->render_label_html() );

		$this->property->set_options( papi_get_property_options( array(
			'slug'  => 'kvack',
			'title' => 'A simple label'
		) ) );

		$this->property->render_label_html();

		$this->expectOutputRegex( '/A\ssimple\slabel/' );
		$this->expectOutputRegex( '/papi\_kvack/' );
	}

	/**
	 * Test `render_row_html` method.
	 *
	 * @since 1.3.0
	 */

	public function test_render_row_html() {
		$this->assertNull( $this->property->render_row_html() );

		$this->property->set_options( papi_get_property_options( array(
			'description' => 'A simple description',
			'title'       => 'A simple label'
		) ) );

		$this->property->render_row_html();

		$this->expectOutputRegex( '/A\ssimple\sdescription/' );
		$this->expectOutputRegex( '/A\ssimple\slabel/' );

		$this->property->set_options( papi_get_property_options( array(
			'raw' => true
		) ) );

		$this->property->render_row_html();
	}

	/**
	 * Test format_value method.
	 *
	 * @since 1.3.0
	 */

	public function test_format_value() {
		$actual = $this->property->format_value( 'Fredrik', '', 0 );

		$this->assertEquals( 'Fredrik', $actual );
	}

	/**
	 * Test load_value method.
	 *
	 * @since 1.3.0
	 */

	public function test_load_value() {
		$actual = $this->property->load_value( 'Fredrik', '', 0 );

		$this->assertEquals( 'Fredrik', $actual );
	}

	/**
	 * Test update_value method.
	 *
	 * @since 1.3.0
	 */

	public function test_update_value() {
		$actual = $this->property->update_value( 'Fredrik', '', 0 );

		$this->assertEquals( 'Fredrik', $actual );
	}

}
