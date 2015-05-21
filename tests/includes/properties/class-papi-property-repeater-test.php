<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering property repeater.
 *
 * @package Papi
 */

class Papi_Property_Repeater_Test extends WP_UnitTestCase {

	/**
	 * Setup the test.
	 */

	public function setUp() {
		parent::setUp();

		$_POST = [];

		tests_add_filter( 'papi/settings/directories', function () {
			return [ 1,  papi_test_get_fixtures_path( '/page-types' ) ];
		} );

		$this->page_type = papi_get_page_type_by_id( 'properties-page-type' );
		$this->post_id   = $this->factory->post->create();
		$this->property  = $this->page_type->get_property( 'books' );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'properties-page-type' );
	}

	/**
	 * Tear down test.
	 */

	public function tearDown() {
		parent::tearDown();
		$_POST = [];
		unset(
			$this->post_id,
			$this->property,
			$this->page_type
		);
	}

	/**
	 * Test output to check if property slug exists and the property type value.
	 */

	public function test_output() {
		papi_render_property( $this->property );
		$this->expectOutputRegex( '/name=\"' . papi_get_property_type_key( $this->property->slug ) . '\"' );
		$this->expectOutputRegex( '/data\-property=\"' . $this->property->type . '\"/' );
	}

	/**
	 * Test property options.
	 */

	public function test_property_options() {
		// Test the property
		$this->assertEquals( 'repeater', $this->property->type );
		$this->assertEquals( 'papi_books', $this->property->slug );
		$this->assertFalse( empty( $this->property->settings->items ) );

		// Test the first item in the repeater
		$this->assertEquals( 'string', $this->property->settings->items[0]->type );
		$this->assertEquals( 'papi_book_name', $this->property->settings->items[0]->array_slug );
		$this->assertEquals( 'Book name', $this->property->settings->items[0]->title );

		// Test the second item in the repeater
		$this->assertEquals( 'bool', $this->property->settings->items[1]->type );
		$this->assertEquals( 'papi_is_open', $this->property->settings->items[1]->array_slug );
		$this->assertEquals( 'Is open?', $this->property->settings->items[1]->title );
	}

	/**
	 * Test save property value.
	 */

	public function test_save_property_value() {
		$handler = new Papi_Admin_Post_Handler();

		$value_slug1         = papi_remove_papi( $this->property->settings->items[0]->array_slug );
		$value_type_slug1    = papi_get_property_type_key( $value_slug1 );
		$value_slug2         = papi_remove_papi( $this->property->settings->items[1]->array_slug );
		$value_type_slug2    = papi_get_property_type_key( $value_slug2 );

		$item = [];
		$item[$value_slug1] = 'Harry Potter';
		$item[$value_type_slug1] = $this->property->settings->items[0];

		$item[$value_slug2] = '';
		$item[$value_type_slug2] = $this->property->settings->items[1];

		$values = [ $item ];

		$_POST = papi_test_create_property_post_data( [
			'slug'  => $this->property->slug,
			'type'  => $this->property,
			'value' => $values
		], $_POST );

		$handler->save_property( $this->post_id );

		$rows_html_name         = papi_ff( papify( $this->property->slug ) . '_rows' );
		$_POST[$rows_html_name] = 1;

		$expected = [
			[  'book_name' => 'Harry Potter', 'is_open' => false ]
		];

		$actual = papi_field( $this->post_id, $this->property->slug, null );

		$this->assertEquals( $expected, $actual );
	}

}
