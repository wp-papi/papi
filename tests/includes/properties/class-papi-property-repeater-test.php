<?php

/**
 * Unit tests covering property repeater.
 *
 * @package Papi
 */

class Papi_Property_Repeater_Test extends Papi_Property_Test_Case {

	public $slug = 'repeater_test';

	public function test_convert_type() {
		$this->assertEquals( 'array', $this->property->convert_type );
	}

	public function test_default_value() {
		$this->assertEquals( [], $this->property->default_value );
	}

	public function get_value() {
		$items = $this->property->get_setting( 'items' );
		$value_slug1         = papi_remove_papi( $items[0]->slug );
		$value_type_slug1    = papi_get_property_type_key( $value_slug1 );
		$value_slug2         = papi_remove_papi( $items[1]->slug );
		$value_type_slug2    = papi_get_property_type_key( $value_slug2 );

		$item = [];
		$item[$value_slug1] = 'Harry Potter';
		$item[$value_type_slug1] = $items[0];
		$item[$value_slug2] = '';
		$item[$value_type_slug2] = $items[1];

		return [ $item ];
	}

	public function get_expected() {
		return [
			[ 'book_name' => 'Harry Potter', 'is_open' => false ]
		];
	}

	public function test_format_value() {
		$actual = $this->property->format_value( $this->get_value(), $this->slug, $this->post_id );
		$this->assertEquals( $this->get_expected(), $actual );
		$this->assertEmpty( $this->property->format_value( '', $this->slug, $this->post_id ) );
		$this->assertEmpty( $this->property->format_value( (object) [], $this->slug, $this->post_id ) );
		$this->assertEmpty( $this->property->format_value( 1, $this->slug, $this->post_id ) );
		$this->assertEmpty( $this->property->format_value( null, $this->slug, $this->post_id ) );
		$this->assertEmpty( $this->property->format_value( [], $this->slug, $this->post_id ) );
		$this->assertEmpty( $this->property->format_value( true, $this->slug, $this->post_id ) );
		$this->assertEmpty( $this->property->format_value( false, $this->slug, $this->post_id ) );
	}

	public function test_property_options() {
		$this->assertEquals( 'repeater', $this->property->get_option( 'type' ) );
		$this->assertEquals( 'Repeater test', $this->property->get_option( 'title' ) );
		$this->assertEquals( 'papi_repeater_test', $this->property->get_option( 'slug' ) );
	}

	public function test_property_settings() {
		$items = $this->property->get_setting( 'items' );
		$this->assertNotEmpty( $items );

		$this->assertEquals( 'string', $items[0]->type );
		$this->assertEquals( 'papi_book_name', $items[0]->slug );
		$this->assertEquals( 'Book name', $items[0]->title );

		$this->assertEquals( 'bool', $items[1]->type );
		$this->assertEquals( 'papi_is_open', $items[1]->slug );
		$this->assertEquals( 'Is open?', $items[1]->title );
	}

}
