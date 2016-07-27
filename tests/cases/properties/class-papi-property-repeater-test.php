<?php

/**
 * @group properties
 */
class Papi_Property_Repeater_Test extends Papi_Property_Test_Case {

	public $slugs = [
		'repeater_test',
		'repeater_with_child_test'
	];

	public function get_value() {
		$args = func_get_args();
		$args[0] = isset( $args[0] ) ? $args[0] : $this->slugs[0];
		switch ( $args[0] ) {
			case 'repeater_with_child_test':
				return [
					[
						'repeater_child_test'          => $this->get_expected( 'repeater_test' ),
						'repeater_child_test_property' => $this->properties[1]
					]
				];
			default:
				$items = $this->property->get_setting( 'items' );
				$value_slug1         = unpapify( $items[0]->slug );
				$value_type_slug1    = papi_get_property_type_key( $value_slug1 );
				$value_slug2         = unpapify( $items[1]->slug );
				$value_type_slug2    = papi_get_property_type_key( $value_slug2 );

				$item = [];
				$item[$value_slug1] = 'Harry Potter';
				$item[$value_type_slug1] = $items[0];
				$item[$value_slug2] = '';
				$item[$value_type_slug2] = $items[1];

				return [$item];
		}
	}

	public function get_expected() {
		$args = func_get_args();
		$args[0] = isset( $args[0] ) ? $args[0] : $this->slugs[0];
		switch ( $args[0] ) {
			case 'repeater_with_child_test':
				return [
					[
						'repeater_child_test' => $this->get_expected( 'repeater_test' )
					]
				];
			default:
				return [
					[
						'book_name' => 'Harry Potter',
						'is_open'   => false
					]
				];
		}
	}

	public function test_property_convert_type() {
		$this->assertSame( 'array', $this->property->convert_type );
	}

	public function test_property_default_value() {
		$this->assertSame( [], $this->property->default_value );
	}

	public function test_property_format_value() {
		$actual = $this->property->format_value( $this->get_value(), $this->slug, $this->post_id );
		$this->assertSame( $this->get_expected(), $actual );
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
			'repeater_test' => 0
		];
		$output = $this->property->import_value( [], $this->slug, $this->post_id );
		$this->assertSame( $expected, $output );

		$value = [
			'book_name' => 'Kod',
			'is_open'   => true
		];
		$expected = [
			'repeater_test_0_book_name' => 'Kod',
			'repeater_test_0_is_open'   => true,
			'repeater_test' => 1
		];
		$output = $this->property->import_value( $value, $this->slug, $this->post_id );
		$this->assertSame( $expected, $output );

		$value = [
			[
				'book_name' => 'Kod',
				'is_open'   => true
			]
		];
		$expected = [
			'repeater_test_0_book_name' => 'Kod',
			'repeater_test_0_is_open'   => true,
			'repeater_test' => 1
		];

		$output = $this->property->import_value( $value, $this->slug, $this->post_id );
		$this->assertSame( $expected, $output );
	}

	public function test_property_load_value() {
		$this->assertSame( [], $this->property->load_value( [], '', 0 ) );
	}

	public function test_property_options() {
		$this->assertSame( 'repeater', $this->property->get_option( 'type' ) );
		$this->assertSame( 'Repeater test', $this->property->get_option( 'title' ) );
		$this->assertSame( 'papi_repeater_test', $this->property->get_option( 'slug' ) );
	}

	public function test_property_setting_add_new_label() {
		$property = Papi_Property::factory( [
			'settings' => [
				'add_new_label' => 'Add new slide'
			],
			'title'    => 'Repeater',
			'type'     => 'repeater'
		] );

		$this->assertSame( 'Add new slide', $property->get_setting( 'add_new_label' ) );
	}

	public function test_property_render_ajax_request() {
		$this->property->render_ajax_request();
		$this->expectOutputRegex( '/.*\S.*/' );
	}

	public function test_property_render_repeater_rows_template() {
		$this->property->render_repeater_rows_template();
		$this->expectOutputRegex( '/.*\S.*/' );
	}

	public function test_property_settings() {
		$items = $this->property->get_setting( 'items' );
		$this->assertNotEmpty( $items );

		$this->assertSame( 'string', $items[0]->type );
		$this->assertSame( 'papi_book_name', $items[0]->slug );
		$this->assertSame( 'Book name', $items[0]->title );

		$this->assertSame( 'bool', $items[1]->type );
		$this->assertSame( 'papi_is_open', $items[1]->slug );
		$this->assertSame( 'Is open?', $items[1]->title );
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
			'test_0_book_name' => 'Harry Potter',
			'test_0_is_open'   => null,
			'test'             => 1
		];
		$this->assertSame( $expected, $output );
	}
}
