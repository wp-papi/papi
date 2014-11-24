<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
* Unit tests covering property functionality.
*
* @package Papi
*/

class WP_Papi_Functions_Property extends WP_UnitTestCase {

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
	 * Test _papi_from_property_array_slugs.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_from_property_array_slugs() {
		$actual = _papi_from_property_array_slugs( array(
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
	 * Test _papi_get_box_property.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_box_property() {
		$actual = _papi_get_box_property( array(
			'type'  => 'string',
			'title' => 'Name'
		) );

		$this->assertEquals( 'Name', $actual[0]->title );
		$this->assertEquals( 'string', $actual[0]->type );
	}

	/**
	* Test _papi_get_only_property_values.
	*
	* @since 1.0.0
	*/

	public function test_papi_get_only_property_values() {
		$actual = _papi_get_only_property_values( array(
			'image'  => 1,
			'_image_property' => 'image'
		) );

		$this->assertEquals( array( 'image' => 1 ), $actual );
	}

	/**
	* Test _papi_get_options_and_properties.
	*
	* @since 1.0.0
	*/

	public function test_papi_get_options_and_properties() {
		$actual = _papi_get_options_and_properties( dirname( __FILE__ ) . '/../data/boxes/simple.php', array(
			'test' => 'test'
		) );

		$this->assertEquals( 'Simple', $actual[0]['title'] );
		$this->assertEquals( 'test', $actual[0]['test'] );
		$this->assertEquals( 'Name', $actual[1][0]->title );
		$this->assertEquals( 'string', $actual[1][0]->type );
	}

	/**
	 * Test _papi_get_property_default_settings.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_property_default_settings() {
		$actual = _papi_get_property_default_settings( 'relationship' );

		$this->assertEquals( 'page', $actual['post_type'] );

		$this->assertEmpty( _papi_get_property_default_settings( 'fake' ) );
		$this->assertEmpty( _papi_get_property_default_settings( 1 ) );
		$this->assertEmpty( _papi_get_property_default_settings( true ) );
		$this->assertEmpty( _papi_get_property_default_settings( false ) );
		$this->assertEmpty( _papi_get_property_default_settings( null ) );
		$this->assertEmpty( _papi_get_property_default_settings( array() ) );
		$this->assertEmpty( _papi_get_property_default_settings( new stdClass() ) );
	}

	/**
	 * Test _papi_get_property_options.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_property_options() {
		$actual = _papi_get_property_options( array(
			'type'  => 'string',
			'title' => 'Name'
		) );

		$this->assertEquals( 'Name', $actual->title );
		$this->assertEquals( 'string', $actual->type );
	}

	/**
	 * Test _papi_get_property_class_name.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_property_class_name() {
		$this->assertEquals( 'Papi_Property_String', _papi_get_property_class_name( 'PropertyString' ) );
		$this->assertEquals( 'Papi_Property_String', _papi_get_property_class_name( 'string' ) );
		$this->assertEquals( 'Papi_Property_Fake', _papi_get_property_class_name( 'fake' ) );
		$this->assertNull( _papi_get_property_class_name( null ) );
		$this->assertNull( _papi_get_property_class_name( false ) );
		$this->assertNull( _papi_get_property_class_name( true ) );
		$this->assertNull( _papi_get_property_class_name( array() ) );
		$this->assertNull( _papi_get_property_class_name( new stdClass() ) );
		$this->assertNull( _papi_get_property_class_name( 1 ) );
	}

	/**
	 * Test _papi_get_property_short_type.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_property_short_type() {
		$this->assertEquals( 'string', _papi_get_property_short_type( 'PropertyString' ) );
		$this->assertEquals( 'string', _papi_get_property_short_type( 'string' ) );
		$this->assertEquals( 'fake', _papi_get_property_short_type( 'fake' ) );
		$this->assertNull(  _papi_get_property_short_type( null ) );
		$this->assertNull( _papi_get_property_short_type( false ) );
		$this->assertNull( _papi_get_property_short_type( true ) );
		$this->assertNull( _papi_get_property_short_type( array() ) );
		$this->assertNull( _papi_get_property_short_type( new stdClass() ) );
		$this->assertNull( _papi_get_property_short_type( 1 ) );
	}

	/**
	 * Test _papi_get_property_type.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_property_type() {
		$this->assertTrue( _papi_get_property_type( 'string' ) instanceof Papi_Property_String );
		$this->assertFalse( _papi_get_property_type( null ) instanceof Papi_Property_String );
		$this->assertFalse( _papi_get_property_type( 'fake' ) instanceof Papi_Property_String );
		$this->assertFalse( _papi_get_property_type( 1 ) instanceof Papi_Property_String );
		$this->assertFalse( _papi_get_property_type( true ) instanceof Papi_Property_String );
		$this->assertFalse( _papi_get_property_type( false ) instanceof Papi_Property_String );
		$this->assertFalse( _papi_get_property_type( array() ) instanceof Papi_Property_String );
		$this->assertFalse( _papi_get_property_type( new stdClass() ) instanceof Papi_Property_String );
	}

	/**
	 * Test _papi_get_property_type_key.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_property_type_key() {
		$this->assertEquals( 'image_property', _papi_get_property_type_key( 'image' ) );
		$this->assertEquals( '_property', _papi_get_property_type_key( null ) );
		$this->assertEquals( '_property', _papi_get_property_type_key( false ) );
		$this->assertEquals( '_property', _papi_get_property_type_key( true ) );
		$this->assertEquals( '_property', _papi_get_property_type_key( array() ) );
		$this->assertEquals( '_property', _papi_get_property_type_key( 1 ) );
		$this->assertEquals( '_property', _papi_get_property_type_key( new stdClass() ) );
	}

	/**
	 * Test _papi_get_property_type_key_f.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_get_property_type_f() {
		$this->assertEquals( '_image_property', _papi_get_property_type_key_f( 'image' ) );
		$this->assertEquals( '_property', _papi_get_property_type_key_f( null ) );
		$this->assertEquals( '_property', _papi_get_property_type_key_f( true ) );
		$this->assertEquals( '_property', _papi_get_property_type_key_f( false ) );
		$this->assertEquals( '_property', _papi_get_property_type_key_f( 1 ) );
		$this->assertEquals( '_property', _papi_get_property_type_key_f( array() ) );
		$this->assertEquals( '_property', _papi_get_property_type_key_f( new stdClass() ) );
	}

	/**
	 * Test _papi_is_property_type_key.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_is_property_type_key() {
		$this->assertTrue( _papi_is_property_type_key( 'image_property' ) );
		$this->assertTrue( _papi_is_property_type_key( '_image_property' ) );
		$this->assertFalse( _papi_is_property_type_key( 'kvack' ) );
		$this->assertFalse( _papi_is_property_type_key( 1 ) );
		$this->assertFalse( _papi_is_property_type_key( false ) );
		$this->assertFalse( _papi_is_property_type_key( true ) );
	}

	/**
	 * Test _papi_populate_properties.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_populate_properties() {
		$actual = _papi_populate_properties( array(
			'type'  => 'string',
			'title' => 'Name'
		) );

		$this->assertEquals( 'Name', $actual[0]->title );
		$this->assertEquals( 'string', $actual[0]->type );

		$actual = _papi_populate_properties( array(
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

		$actual = _papi_populate_properties( array(
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
	 * Test _papi_to_property_array_slugs.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_to_property_array_slugs() {
		$actual = _papi_to_property_array_slugs( array(
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

		$actual = _papi_to_property_array_slugs( array( 1, '', true, false ), 'repeater' );

		$expected = array(
			'repeater' => 0
		);

		$this->assertEquals( $expected, $actual );
	}

}
