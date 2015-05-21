<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering property flexible.
 *
 * @package Papi
 */

class Papi_Property_Flexible_Test extends WP_UnitTestCase {

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
		$this->property  = $this->page_type->get_property( 'sections' );

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
		$this->assertEquals( 'flexible', $this->property->type );
		$this->assertEquals( 'papi_sections', $this->property->slug );
		$this->assertFalse( empty( $this->property->settings->items ) );

		// Test the first item in flexible
		$this->assertEquals( 'Twitter', $this->property->settings->items['twitter']['title'] );
		$this->assertEquals( 'string', $this->property->settings->items['twitter']['items'][0]->type );
		$this->assertEquals( 'papi_twitter_name', $this->property->settings->items['twitter']['items'][0]->array_slug );
		$this->assertEquals( 'Twitter name', $this->property->settings->items['twitter']['items'][0]->title );

		// Test the second item in flexible
		$this->assertEquals( 'Posts', $this->property->settings->items['posts']['title'] );
		$this->assertEquals( 'post', $this->property->settings->items['posts']['items'][0]->type );
		$this->assertEquals( 'papi_post_one', $this->property->settings->items['posts']['items'][0]->array_slug );
		$this->assertEquals( 'Post one', $this->property->settings->items['posts']['items'][0]->title );
		$this->assertEquals( 'post', $this->property->settings->items['posts']['items'][1]->type );
		$this->assertEquals( 'papi_post_two', $this->property->settings->items['posts']['items'][1]->array_slug );
		$this->assertEquals( 'Post two', $this->property->settings->items['posts']['items'][1]->title );
	}

	/**
	 * Test save property value.
	 */

	public function test_save_property_value() {
		$handler = new Papi_Admin_Post_Handler();

		$value_slug1         = papi_remove_papi( $this->property->settings->items['twitter']['items'][0]->array_slug );
		$value_type_slug1    = papi_get_property_type_key( $value_slug1 );
		$value_slug2_1       = papi_remove_papi( $this->property->settings->items['posts']['items'][0]->array_slug );
		$value_type_slug2_1  = papi_get_property_type_key( $value_slug2_1 );
		$value_slug2_2       = papi_remove_papi( $this->property->settings->items['posts']['items'][1]->array_slug );
		$value_type_slug2_2  = papi_get_property_type_key( $value_slug2_2 );

		$item1 = [];
		$item1[$value_slug1] = '@frozzare';
		$item1[$value_type_slug1] = $this->property->settings->items['twitter']['items'][0];
		$item1[$value_slug1 . '_layout'] = '_flexible_layout_twitter';

		$item2 = [];
		$item2[$value_slug2_1] = $this->post_id;
		$item2[$value_type_slug2_1] = $this->property->settings->items['posts']['items'][0];
		$item2[$value_slug2_1 . '_layout'] = '_flexible_layout_posts';

		$item2[$value_slug2_2] = $this->post_id;
		$item2[$value_type_slug2_2] = $this->property->settings->items['posts']['items'][0];
		$item2[$value_slug2_2 . '_layout'] = '_flexible_layout_posts';

		$values = [ $item1, $item2 ];

		$_POST = papi_test_create_property_post_data( [
			'slug'  => $this->property->slug,
			'type'  => $this->property,
			'value' => $values
		], $_POST );

		$handler->save_property( $this->post_id );

		$rows_html_name         = papi_ff( papify( $this->property->slug ) . '_rows' );
		$_POST[$rows_html_name] = 2;

		$expected = [
			[ 'twitter_name' => '@frozzare', '_layout' => 'twitter' ],
			[ 'post_one' => get_post( $this->post_id ), 'post_two' => get_post( $this->post_id ), '_layout' => 'posts' ]
		];

		$actual = papi_field( $this->post_id, $this->property->slug, null );

		$this->assertEquals( $expected, $actual );
	}

}
