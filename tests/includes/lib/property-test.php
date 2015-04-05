<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
* Unit tests covering property functionality.
*
* @package Papi
*/

class Papi_Lib_Property_Test extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 *
	 * @since 1.0.0
	 */

	public function setUp() {
		parent::setUp();

		global $post;

		$this->post_id = $this->factory->post->create();

		$post = get_post( $this->post_id );
	}

	/**
	 * Test papi_from_property_array_slugs.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_from_property_array_slugs() {
		$actual = papi_from_property_array_slugs( array(
			'repeater' => 1,
			'repeater_0_image' => 1,
			'_repeater_0_image_property' => 'image'
		), 'repeater' );

		$expected = array(
			array(
				'image' => 1,
				'_image_property' => 'image'
			)
		);

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test papi_get_box_property.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_box_property() {
		$actual = papi_get_box_property( array(
			'type'  => 'string',
			'title' => 'Name'
		) );

		$this->assertEquals( 'Name', $actual[0]->title );
		$this->assertEquals( 'string', $actual[0]->type );
	}

	/**
	 * Test papi_get_options_and_properties.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_options_and_properties() {
		$simple_box = papi_test_get_files_path( '/boxes/simple.php' );

		$actual = papi_get_options_and_properties( $simple_box, array(
			'test' => 'test'
		) );

		$this->assertEquals( 'Simple', $actual[0]['title'] );
		$this->assertEquals( 'test', $actual[0]['test'] );
		$this->assertEquals( 'Name', $actual[1][0]->title );
		$this->assertEquals( 'string', $actual[1][0]->type );
	}

	/**
	 * Test papi_get_property_default_options.
	 *
	 * @since 1.3.0
	 */

	public function test_papi_get_property_default_options() {
		$actual = papi_get_property_default_options();

		$this->assertTrue( is_array( $actual ) );
		$this->assertFalse( empty( $actual ) );
	}

	/**
	 * Test papi_get_property_default_settings.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_property_default_settings() {
		$actual = papi_get_property_default_settings( 'relationship' );

		$this->assertEquals( 'page', $actual['post_type'] );

		$this->assertEmpty( papi_get_property_default_settings( 'fake' ) );
		$this->assertEmpty( papi_get_property_default_settings( 1 ) );
		$this->assertEmpty( papi_get_property_default_settings( true ) );
		$this->assertEmpty( papi_get_property_default_settings( false ) );
		$this->assertEmpty( papi_get_property_default_settings( null ) );
		$this->assertEmpty( papi_get_property_default_settings( array() ) );
		$this->assertEmpty( papi_get_property_default_settings( new stdClass() ) );
	}

	/**
	 * Test papi_get_property_options.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_property_options() {
		$actual = papi_get_property_options( array(
			'type'  => 'string',
			'title' => 'Name'
		) );

		$this->assertEquals( 'Name', $actual->title );
		$this->assertEquals( 'string', $actual->type );
	}

	/**
	 * Test papi_get_property_class_name.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_property_class_name() {
		$this->assertEquals( 'Papi_Property_String', papi_get_property_class_name( 'PropertyString' ) );
		$this->assertEquals( 'Papi_Property_String', papi_get_property_class_name( 'string' ) );
		$this->assertEquals( 'Papi_Property_Fake', papi_get_property_class_name( 'fake' ) );
		$this->assertNull( papi_get_property_class_name( null ) );
		$this->assertNull( papi_get_property_class_name( false ) );
		$this->assertNull( papi_get_property_class_name( true ) );
		$this->assertNull( papi_get_property_class_name( array() ) );
		$this->assertNull( papi_get_property_class_name( new stdClass() ) );
		$this->assertNull( papi_get_property_class_name( 1 ) );
	}

	/**
	 * Test papi_get_property_short_type.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_property_short_type() {
		$this->assertEquals( 'string', papi_get_property_short_type( 'PropertyString' ) );
		$this->assertEquals( 'string', papi_get_property_short_type( 'string' ) );
		$this->assertEquals( 'fake', papi_get_property_short_type( 'fake' ) );
		$this->assertNull(  papi_get_property_short_type( null ) );
		$this->assertNull( papi_get_property_short_type( false ) );
		$this->assertNull( papi_get_property_short_type( true ) );
		$this->assertNull( papi_get_property_short_type( array() ) );
		$this->assertNull( papi_get_property_short_type( new stdClass() ) );
		$this->assertNull( papi_get_property_short_type( 1 ) );
	}

	/**
	 * Test papi_get_property_type.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_property_type() {
		$this->assertTrue( papi_get_property_type( 'string' ) instanceof Papi_Property_String );
		$this->assertFalse( papi_get_property_type( null ) instanceof Papi_Property_String );
		$this->assertFalse( papi_get_property_type( 'fake' ) instanceof Papi_Property_String );
		$this->assertFalse( papi_get_property_type( 1 ) instanceof Papi_Property_String );
		$this->assertFalse( papi_get_property_type( true ) instanceof Papi_Property_String );
		$this->assertFalse( papi_get_property_type( false ) instanceof Papi_Property_String );
		$this->assertFalse( papi_get_property_type( array() ) instanceof Papi_Property_String );
		$this->assertFalse( papi_get_property_type( new stdClass() ) instanceof Papi_Property_String );
	}

	/**
	 * Test papi_get_property_type.
	 * Will load a custom property and test if it exists.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_property_type_custom() {
		add_action( 'papi_include_properties', function() {
			require_once papi_test_get_files_path( '/properties/class-papi-property-kvack.php' );
		} );

		do_action( 'papi_include_properties' );

		$this->assertTrue( papi_get_property_type( 'kvack' ) instanceof Papi_Property_Kvack );
	}

	/**
	 * Test papi_get_property_type_key.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_property_type_key() {
		$this->assertEquals( 'image_property', papi_get_property_type_key( 'image' ) );
		$this->assertEquals( '_property', papi_get_property_type_key( null ) );
		$this->assertEquals( '_property', papi_get_property_type_key( false ) );
		$this->assertEquals( '_property', papi_get_property_type_key( true ) );
		$this->assertEquals( '_property', papi_get_property_type_key( array() ) );
		$this->assertEquals( '_property', papi_get_property_type_key( 1 ) );
		$this->assertEquals( '_property', papi_get_property_type_key( new stdClass() ) );
	}

	/**
	 * Test papi_get_property_type_key_f.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_property_type_f() {
		$this->assertEquals( '_image_property', papi_get_property_type_key_f( 'image' ) );
		$this->assertEquals( '_property', papi_get_property_type_key_f( null ) );
		$this->assertEquals( '_property', papi_get_property_type_key_f( true ) );
		$this->assertEquals( '_property', papi_get_property_type_key_f( false ) );
		$this->assertEquals( '_property', papi_get_property_type_key_f( 1 ) );
		$this->assertEquals( '_property', papi_get_property_type_key_f( array() ) );
		$this->assertEquals( '_property', papi_get_property_type_key_f( new stdClass() ) );
	}

	/**
	 * Test papi_is_property_type_key.
	 *
	 * @since 1.0.0
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
	 * Test papi_populate_properties.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_populate_properties() {
		$actual = papi_populate_properties( array(
			'type'  => 'string',
			'title' => 'Name'
		) );

		$this->assertEquals( 'Name', $actual[0]->title );
		$this->assertEquals( 'string', $actual[0]->type );

		$actual = papi_populate_properties( array(
			array(
				'type'  => 'string',
				'title' => 'Name 1'
			),
			array(
				'type'  => 'string',
				'title' => 'Name 2'
			)
		) );

		$this->assertEquals( 'Name 1', $actual[0]->title );
		$this->assertEquals( 'string', $actual[0]->type );

		$this->assertEquals( 'Name 2', $actual[1]->title );
		$this->assertEquals( 'string', $actual[1]->type );

		$actual = papi_populate_properties( array(
			array(
				'type'  	 => 'string',
				'title' 	 => 'Name 1',
				'sort_order' => 1
			),
			array(
				'type'  	 => 'string',
				'title' 	 => 'Name 3'
			),
			array(
				'type'  	 => 'string',
				'title' 	 => 'Name 2',
				'sort_order' => 0
			)
		) );

		$this->assertEquals( 'Name 2', $actual[0]->title );
		$this->assertEquals( 'string', $actual[0]->type );

		$this->assertEquals( 'Name 1', $actual[1]->title );
		$this->assertEquals( 'string', $actual[1]->type );

		$this->assertEquals( 'Name 3', $actual[2]->title );
		$this->assertEquals( 'string', $actual[2]->type );
	}

	/**
	 * Test papi_property.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_property() {
		$actual = papi_property( array(
			'type'  => 'string',
			'title' => 'Name'
		) );

		$this->assertEquals( 'Name', $actual->title );
		$this->assertEquals( 'string', $actual->type );

		$actual = papi_property( array() );

		$this->assertEmpty( $actual->title );
		$this->assertEmpty( $actual->type );
		$this->assertTrue( $actual->sidebar );

		$this->assertEmpty( papi_property( null ) );
		$this->assertEmpty( papi_property( true ) );
		$this->assertEmpty( papi_property( false ) );
		$this->assertEmpty( papi_property( 1 ) );
		$this->assertEmpty( papi_property( new stdClass() ) );
	}

	/**
	 * Test papi_property template.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_property_template() {
		$actual = papi_property( papi_test_get_files_path( '/properties/simple.php' ) );

		$this->assertEquals( 'Name', $actual->title );
		$this->assertEquals( 'string', $actual->type );
	}

	/**
	 * Test papi_to_property_array_slugs.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_to_property_array_slugs() {
		$actual = papi_to_property_array_slugs( array(
			array(
				'image' => 1,
				'image_property' => 'image'
			)
		), 'repeater' );

		$expected = array(
			'repeater' => 1,
			'repeater_0_image' => 1,
			'_repeater_0_image_property' => 'image'
		);

		$this->assertEquals( $expected, $actual );

		$actual = papi_to_property_array_slugs( array( 1, '', true, false ), 'repeater' );

		$expected = array(
			'repeater' => 0
		);

		$this->assertEquals( $expected, $actual );
	}

}
