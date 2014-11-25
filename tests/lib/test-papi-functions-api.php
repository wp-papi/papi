<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Unit tests covering api functionality.
 *
 * @package Papi
 */

class WP_Papi_Functions_API extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 *
	 * @since 1.0.0
	 */

	public function setUp() {
		parent::setUp();

		$this->post_id = $this->factory->post->create();

		$post = get_post( $this->post_id );
	}

	/**
	 * Test current_page.
	 *
	 * @since 1.0.0
	 */

	public function test_current_page() {
		$this->assertNull( current_page() );
	}

	/**
	 * Test papi_field.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_field() {
		add_post_meta( $this->post_id, 'name', 'fredrik' );
		add_post_meta( $this->post_id, '_name_property', 'string' );

		$this->assertEquals( 'fredrik', papi_field( $this->post_id, 'name' ) );
		$this->assertEquals( 'string', get_post_meta( $this->post_id, '_name_property', true ) );

		$this->assertEquals( 'world', papi_field( $this->post_id, 'hello', 'world' ) );
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
		$actual = papi_property( dirname( __FILE__ ) . '/../data/properties/simple.php' );

		$this->assertEquals( 'Name', $actual->title );
		$this->assertEquals( 'string', $actual->type );
	}

	/**
	 * Test papi_tab.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_tab() {
		$actual = papi_tab( 'Content', array(
			papi_property( array(
				'type'  => 'string',
				'title' => 'Name'
			) )
		) );

		$this->assertTrue( $actual->tab );
		$this->assertEquals( 'Content', $actual->options['title'] );
		$this->assertEquals( 'Name', $actual->properties[0]->title );
		$this->assertEquals( 'string', $actual->properties[0]->type );
	}

	/**
	 * Test papi_tab template.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_tab_template() {
		$actual = papi_property( dirname( __FILE__ ) . '/../data/tabs/content.php' );

		$this->assertTrue( $actual->tab );
		$this->assertEquals( 'Content', $actual->options['title'] );
		$this->assertEquals( 'Name', $actual->properties[0]->title );
		$this->assertEquals( 'string', $actual->properties[0]->type );
	}

	/**
	 * Test papi_template.
	 *
	 * @since 1.0.0
	 */

	public function test_papi_template() {
		$actual = papi_template( dirname( __FILE__ ) . '/../data/properties/simple.php' );

		$this->assertEquals( 'Name', $actual['title'] );
		$this->assertEquals( 'string', $actual['type'] );

		$this->assertEmpty( papi_template( null ) );
		$this->assertEmpty( papi_template( true ) );
		$this->assertEmpty( papi_template( false ) );
		$this->assertEmpty( papi_template( 1 ) );
		$this->assertEmpty( papi_template( array() ) );
		$this->assertEmpty( papi_template( new stdClass() ) );
	}

}
