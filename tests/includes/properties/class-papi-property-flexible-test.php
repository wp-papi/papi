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
	 *
	 * @since 1.3.0
	 */

	public function setUp() {
		parent::setUp();

		$_POST = array();

		$this->post_id = $this->factory->post->create();

		$this->property = papi_property( array(
			'type'     => 'flexible',
			'title'    => 'Sections',
			'slug'     => 'sections',
			'settings' => array(
				'items' => array(
					'twitter' => array(
						'title' => 'Twitter',
						'items' => array(
							papi_property( array(
								'type'  => 'string',
								'title' => 'Twitter name',
								'slug'  => 'twitter_name'
							) )
						)
					),
					'posts' => array(
						'title' => 'Posts',
						'items' => array(
							papi_property( array(
								'type'  => 'post',
								'title' => 'Post one',
								'slug'  => 'post_one'
							) ),
							papi_property( array(
								'type'  => 'post',
								'title' => 'Post two',
								'slug'  => 'post_two'
							) )
						)
					)
				)
			)
		) );
	}

	/**
	 * Tear down test.
	 *
	 * @since 1.3.0
	 */

	public function tearDown() {
		parent::tearDown();
		$_POST = array();
		unset( $this->post_id, $this->property );
	}

	/**
	 * Test output to check if property slug exists and the property type value.
	 *
	 * @since 1.3.0
	 */

	public function test_output() {
		papi_render_property( $this->property );
		$this->expectOutputRegex( '/name=\"' . papi_get_property_type_key( $this->property->slug ) . '\"' );
		$this->expectOutputRegex( '/data\-property=\"' . $this->property->type . '\"/' );
	}

	/**
	 * Test property options.
	 *
	 * @since 1.3.0
	 */

	public function test_property_options() {
		// Test the property
		$this->assertEquals( 'flexible', $this->property->type );
		$this->assertEquals( 'papi_sections', $this->property->slug );
		$this->assertFalse( empty( $this->property->settings->items ) );

		// Test the first item in flexible
		$this->assertEquals( 'Twitter', $this->property->settings->items['twitter']['title'] );
		$this->assertEquals( 'string', $this->property->settings->items['twitter']['items'][0]->type );
		$this->assertEquals( 'papi_twitter_name', $this->property->settings->items['twitter']['items'][0]->slug );
		$this->assertEquals( 'Twitter name', $this->property->settings->items['twitter']['items'][0]->title );

		// Test the second item in flexible
		$this->assertEquals( 'Posts', $this->property->settings->items['posts']['title'] );
		$this->assertEquals( 'post', $this->property->settings->items['posts']['items'][0]->type );
		$this->assertEquals( 'papi_post_one', $this->property->settings->items['posts']['items'][0]->slug );
		$this->assertEquals( 'Post one', $this->property->settings->items['posts']['items'][0]->title );
		$this->assertEquals( 'post', $this->property->settings->items['posts']['items'][1]->type );
		$this->assertEquals( 'papi_post_two', $this->property->settings->items['posts']['items'][1]->slug );
		$this->assertEquals( 'Post two', $this->property->settings->items['posts']['items'][1]->title );
	}

	/**
	 * Test save property value.
	 *
	 * @since 1.3.0
	 */

	public function test_save_property_value() {
		$handler = new Papi_Admin_Meta_Boxes();

		// Generate correct property meta key and property type meta key for string property.
		$value_slug1         = papi_remove_papi( $this->property->settings->items['twitter']['items'][0]->slug );
		$value_type_slug1    = papi_get_property_type_key( $value_slug1 );
		$value_slug2_1       = papi_remove_papi( $this->property->settings->items['posts']['items'][0]->slug );
		$value_type_slug2_1  = papi_get_property_type_key( $value_slug2_1 );
		$value_slug2_2       = papi_remove_papi( $this->property->settings->items['posts']['items'][1]->slug );
		$value_type_slug2_2  = papi_get_property_type_key( $value_slug2_2 );

		// Create the repeater item
		$item1 = array();
		$item1[$value_slug1] = '@frozzare';
		$item1[$value_type_slug1] = $this->property->settings->items['twitter']['items'][0];
		$item1[$value_slug1 . '_layout'] = '_flexible_layout_twitter';

		$item2 = array();
		$item2[$value_slug2_1] = $this->post_id;
		$item2[$value_type_slug2_1] = $this->property->settings->items['posts']['items'][0];
		$item2[$value_slug2_1 . '_layout'] = '_flexible_layout_posts';

		$item2[$value_slug2_2] = $this->post_id;
		$item2[$value_type_slug2_2] = $this->property->settings->items['posts']['items'][0];
		$item2[$value_slug2_2 . '_layout'] = '_flexible_layout_posts';

		$values = array( $item1, $item2 );

		// Create post data.
		$_POST = papi_test_create_property_post_data( array(
			'slug'  => $this->property->slug,
			'type'  => $this->property,
			'value' => $values
		), $_POST );

		$handler->save_property( $this->post_id );

		// Rows
		$rows_html_name         = papi_ff( papify( $this->property->slug ) . '_rows' );
		$_POST[$rows_html_name] = 2;

		$expected = array(
			array( 'twitter_name' => '@frozzare', '_layout' => 'twitter' ),
			array( 'post_one' => get_post( $this->post_id ), 'post_two' => get_post( $this->post_id ), '_layout' => 'posts' )
		);

		$actual   = papi_field( $this->post_id, $this->property->slug, null, array(
			'property' => Papi_Property::create( (array) $this->property )
		) );

		$this->assertEquals( $expected, $actual );
	}

}
