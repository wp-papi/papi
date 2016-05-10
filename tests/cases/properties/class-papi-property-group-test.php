<?php

/**
 * @group properties
 */
class Papi_Property_Group_Test extends Papi_Property_Test_Case {

	public $slug = 'group_test';

	public function assert_values( $expected, $actual ) {
		foreach ( $expected as $key => $value ) {
			if ( $value instanceof WP_Post ) {
				$this->assertSame( $expected[$key]->ID, $actual[$key]->ID );
			} else {
				$this->assertSame( $expected[$key], $actual[$key] );
			}
		}
	}

	public function get_value() {
		$items = $this->property->get_setting( 'items' );
		$value_slug1         = unpapify( $items[0]->slug );
		$value_type_slug1    = papi_get_property_type_key( $value_slug1 );
		$value_slug2         = unpapify( $items[1]->slug );
		$value_type_slug2    = papi_get_property_type_key( $value_slug2 );

		$item = [];
		$item[$value_slug1] = $this->post_id;
		$item[$value_type_slug1] = $items[0];
		$item[$value_slug2] = 'Test page';
		$item[$value_type_slug2] = $items[1];

		return [$item];
	}

	public function get_expected() {
		return [
			'page'       => get_post( $this->post_id ),
			'page_title' => 'Test page'
		];
	}

	public function test_property_convert_type() {
		$this->assertSame( 'array', $this->property->convert_type );
	}

	public function test_property_default_value() {
		$this->assertSame( [], $this->property->default_value );
	}

	public function test_property_format_value() {
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

	public function test_property_import_value() {
		$this->assertEmpty( $this->property->import_value( '', $this->slug, $this->post_id ) );
		$this->assertEmpty( $this->property->import_value( (object) [], $this->slug, $this->post_id ) );
		$this->assertEmpty( $this->property->import_value( 1, $this->slug, $this->post_id ) );
		$this->assertEmpty( $this->property->import_value( null, $this->slug, $this->post_id ) );
		$this->assertEmpty( $this->property->import_value( true, $this->slug, $this->post_id ) );
		$this->assertEmpty( $this->property->import_value( false, $this->slug, $this->post_id ) );

		$expected = [
			'group_test' => 0
		];
		$output = $this->property->import_value( [], $this->slug, $this->post_id );
		$this->assertSame( $expected, $output );

		$value = [
			'page'       => $this->post_id,
			'page_title' => 'Test page'
		];
		$expected = [
			'group_test_0_page'       => $this->post_id,
			'group_test_0_page_title' => 'Test page',
			'group_test' => 1
		];
		$output = $this->property->import_value( $value, $this->slug, $this->post_id );
		$this->assertSame( $expected, $output );

		$value = [
			[
				'page'       => $this->post_id,
				'page_title' => 'Test page'
			]
		];
		$expected = [
			'group_test_0_page'       => $this->post_id,
			'group_test_0_page_title' => 'Test page',
			'group_test' => 1
		];

		$output = $this->property->import_value( $value, $this->slug, $this->post_id );
		$this->assertSame( $expected, $output );
	}

	public function test_property_load_value() {
		$this->assertSame( [], $this->property->load_value( [], '', 0 ) );
	}

	public function test_property_options() {
		$this->assertSame( 'group', $this->property->get_option( 'type' ) );
		$this->assertSame( 'Group test', $this->property->get_option( 'title' ) );
		$this->assertSame( 'papi_group_test', $this->property->get_option( 'slug' ) );
	}

	public function test_property_settings() {
		$items = $this->property->get_setting( 'items' );
		$this->assertNotEmpty( $items );

		$this->assertSame( 'post', $items[0]->type );
		$this->assertSame( 'papi_page', $items[0]->slug );
		$this->assertSame( 'Page', $items[0]->title );

		$this->assertSame( 'string', $items[1]->type );
		$this->assertSame( 'papi_page_title', $items[1]->slug );
		$this->assertSame( 'Page title', $items[1]->title );
	}

	public function test_property_update_value() {
		$input    = null;
		$output   = $this->property->update_value( $input, 'test', $this->post_id );
		$expected = ['test' => 0];
		$this->assertSame( $expected, $output );

		$input    = [];
		$output   = $this->property->update_value( $input, 'test', $this->post_id );
		$expected = ['test' => 0];
		$this->assertSame( $expected, $output );

		$input    = $this->get_value();
		$output   = $this->property->update_value( $input, 'test', $this->post_id );
		$expected = [
			'test_0_page'       => $this->post_id,
			'test_0_page_title' => 'Test page',
			'test'              => 1
		];
		$this->assertSame( $expected, $output );
	}
}
