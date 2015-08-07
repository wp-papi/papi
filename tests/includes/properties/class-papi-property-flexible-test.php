<?php

/**
 * Unit tests covering property flexible.
 *
 * @package Papi
 */
class Papi_Property_Flexible_Test extends Papi_Property_Test_Case {

	public $slug = 'flexible_test';

	public function get_value() {
		$items = $this->property->get_setting( 'items' );

		$value_slug1         = papi_remove_papi( $items['twitter']['items'][0]->slug );
		$value_type_slug1    = papi_get_property_type_key( $value_slug1 );
		$value_slug2_1       = papi_remove_papi( $items['posts']['items'][0]->slug );
		$value_type_slug2_1  = papi_get_property_type_key( $value_slug2_1 );
		$value_slug2_2       = papi_remove_papi( $items['posts']['items'][1]->slug );
		$value_type_slug2_2  = papi_get_property_type_key( $value_slug2_2 );

		$item1 = [];
		$item1[$value_slug1] = '@frozzare';
		$item1[$value_type_slug1] = $items['twitter']['items'][0];
		$item1[$value_slug1 . '_layout'] = '_flexible_layout_twitter';

		$item2 = [];
		$item2[$value_slug2_1] = $this->post_id;
		$item2[$value_type_slug2_1] = $items['posts']['items'][0];
		$item2[$value_slug2_1 . '_layout'] = '_flexible_layout_posts';
		$item2[$value_slug2_2] = $this->post_id;
		$item2[$value_type_slug2_2] = $items['posts']['items'][0];
		$item2[$value_slug2_2 . '_layout'] = '_flexible_layout_posts';

		return [ $item1, $item2 ];
	}

	public function get_expected() {
		return [
			[ 'twitter_name' => '@frozzare', '_layout' => 'twitter' ],
			[ 'post_one' => get_post( $this->post_id ), 'post_two' => get_post( $this->post_id ), '_layout' => 'posts' ]
		];
	}

	public function test_property_convert_type() {
		$this->assertEquals( 'array', $this->property->convert_type );
	}

	public function test_property_default_value() {
		$this->assertEquals( [], $this->property->default_value );
	}

	public function test_property_format_value() {
		$this->assertEmpty( $this->property->format_value( '', $this->slug, $this->post_id ) );
		$this->assertEmpty( $this->property->format_value( (object) [], $this->slug, $this->post_id ) );
		$this->assertEmpty( $this->property->format_value( 1, $this->slug, $this->post_id ) );
		$this->assertEmpty( $this->property->format_value( null, $this->slug, $this->post_id ) );
		$this->assertEmpty( $this->property->format_value( [], $this->slug, $this->post_id ) );
		$this->assertEmpty( $this->property->format_value( true, $this->slug, $this->post_id ) );
		$this->assertEmpty( $this->property->format_value( false, $this->slug, $this->post_id ) );
	}

	public function test_property_import_value() {
		// todo
		$this->assertTrue( true );
	}

	public function test_property_options() {
		$this->assertEquals( 'flexible', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'Flexible test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_flexible_test', $this->property->get_option( 'slug' ) );
	}

	public function test_property_settings() {
		$items = $this->property->get_setting( 'items' );
		$this->assertNotEmpty( $items );

		$this->assertEquals( 'Twitter', $items['twitter']['title'] );
		$this->assertEquals( 'string', $items['twitter']['items'][0]->type );
		$this->assertEquals( 'papi_twitter_name', $items['twitter']['items'][0]->slug );
		$this->assertEquals( 'Twitter name', $items['twitter']['items'][0]->title );
		$this->assertEquals( 'Posts', $items['posts']['title'] );

		$this->assertEquals( 'post', $items['posts']['items'][0]->type );
		$this->assertEquals( 'papi_post_one', $items['posts']['items'][0]->slug );
		$this->assertEquals( 'Post one', $items['posts']['items'][0]->title );
		$this->assertEquals( 'post', $items['posts']['items'][1]->type );
		$this->assertEquals( 'papi_post_two', $items['posts']['items'][1]->slug );
		$this->assertEquals( 'Post two', $items['posts']['items'][1]->title );
	}

}
