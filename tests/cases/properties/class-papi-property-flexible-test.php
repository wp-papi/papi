<?php

class Papi_Property_Flexible_Test extends Papi_Property_Test_Case {

	public $slug = 'flexible_test';

	public function assert_values( $expected, $actual ) {
		for ( $i = 0, $l = count( $expected ); $i < $l; $i++ ) {
			foreach ( $expected[$i] as $key => $value ) {
				if ( $value instanceof WP_Post ) {
					$this->assertSame( $expected[$i][$key]->ID, $actual[$i][$key]->ID );
				} else {
					$this->assertSame( $expected[$i][$key], $actual[$i][$key] );
				}
			}
		}
	}

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
		$this->assertSame( 'array', $this->property->convert_type );
	}

	public function test_property_default_value() {
		$this->assertSame( [], $this->property->default_value );
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
		$this->assertEmpty( $this->property->import_value( '', $this->slug, $this->post_id ) );
		$this->assertEmpty( $this->property->import_value( (object) [], $this->slug, $this->post_id ) );
		$this->assertEmpty( $this->property->import_value( 1, $this->slug, $this->post_id ) );
		$this->assertEmpty( $this->property->import_value( null, $this->slug, $this->post_id ) );
		$this->assertEmpty( $this->property->import_value( true, $this->slug, $this->post_id ) );
		$this->assertEmpty( $this->property->import_value( false, $this->slug, $this->post_id ) );

		$expected = [
			'flexible_test' => 0
		];
		$output = $this->property->import_value( [], $this->slug, $this->post_id );
		$this->assertSame( $expected, $output );

		$value = [
			'twitter_name' => 'Kod',
			'_layout'      => 'twitter'
		];
		$expected = [
			'flexible_test_0_twitter_name' => 'Kod',
			'flexible_test_0_layout'       => 'twitter',
			'flexible_test' => 1
		];
		$output = $this->property->import_value( $value, $this->slug, $this->post_id );
		$this->assertSame( $expected, $output );

		$value = [
			[
				'twitter_name' => 'Kod',
				'_layout'      => 'twitter'
			]
		];
		$expected = [
			'flexible_test_0_twitter_name' => 'Kod',
			'flexible_test_0_layout'       => 'twitter',
			'flexible_test' => 1
		];

		$output = $this->property->import_value( $value, $this->slug, $this->post_id );
		$this->assertSame( $expected, $output );
	}

	public function test_property_load_value() {
		$this->assertSame( [], $this->property->load_value( [], '', 0 ) );
	}

	public function test_property_options() {
		$this->assertSame( 'flexible', $this->property->get_option( 'type' ) );
		$this->assertSame( 'Flexible test', $this->property->get_option( 'title' ) );
		$this->assertSame( 'papi_flexible_test', $this->property->get_option( 'slug' ) );
	}

	public function test_property_setting_add_new_label() {
		$property = Papi_Property::create( [
			'settings' => [
				'add_new_label' => 'Add new slide'
			],
			'title'    => 'Flexible',
			'type'     => 'flexible'
		] );

		$this->assertSame( 'Add new slide', $property->get_setting( 'add_new_label' ) );
	}

	public function test_property_render_repeater_rows_template() {
		$this->property->render_repeater_rows_template();
		$this->expectOutputRegex( '/.*\S.*/' );
	}

	public function test_property_settings() {
		$items = $this->property->get_setting( 'items' );
		$this->assertNotEmpty( $items );

		$this->assertSame( 'Twitter', $items['twitter']['title'] );
		$this->assertSame( 'string', $items['twitter']['items'][0]->type );
		$this->assertSame( 'papi_twitter_name', $items['twitter']['items'][0]->slug );
		$this->assertSame( 'Twitter name', $items['twitter']['items'][0]->title );
		$this->assertSame( 'Posts', $items['posts']['title'] );

		$this->assertSame( 'post', $items['posts']['items'][0]->type );
		$this->assertSame( 'papi_post_one', $items['posts']['items'][0]->slug );
		$this->assertSame( 'Post one', $items['posts']['items'][0]->title );
		$this->assertSame( 'post', $items['posts']['items'][1]->type );
		$this->assertSame( 'papi_post_two', $items['posts']['items'][1]->slug );
		$this->assertSame( 'Post two', $items['posts']['items'][1]->title );
	}
}
