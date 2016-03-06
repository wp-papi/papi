<?php

/**
 * @group core
 */
class Papi_Core_Conditional_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$_GET = [];
		$this->conditional = new Papi_Core_Conditional();
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->conditional, $_GET );
	}

	public function test_failed_display() {
		$result = $this->conditional->display( [
			[
				'operator' => '=',
				'slug'     => 'fake',
				'value'    => ''
			]
		] );

		$this->assertFalse( $result );

		$result = $this->conditional->display( [
			'relation' => 'AND',
			[
				'operator' => '=',
				'slug'     => 'fake',
				'value'    => ''
			],
		] );

		$this->assertFalse( $result );

		$result = $this->conditional->display( [
			[
				'operator' => '=',
				'slug'     => 'fake',
				'value'    => ''
			],
			false,
			null
		] );

		$this->assertFalse( $result );

		$result = $this->conditional->display( [
			'relation' => 'AND',
			[
				'operator' => '=',
				'slug'     => 'fake',
				'value'    => ''
			],
			false,
			null
		] );

		$this->assertFalse( $result );
	}

	public function test_success_display() {
		$result = $this->conditional->display( [] );
		$this->assertTrue( $result );

		$result = $this->conditional->display( [null] );
		$this->assertTrue( $result );

		$result = $this->conditional->display( [true] );
		$this->assertTrue( $result );

		$result = $this->conditional->display( [false] );
		$this->assertTrue( $result );

		$result = $this->conditional->display( ['hello'] );
		$this->assertTrue( $result );

		$result = $this->conditional->display( [ (object) []] );
		$this->assertTrue( $result );

		$result = $this->conditional->display( [
			'relation' => 'nil',
			[
				(object) []
			]
		] );
		$this->assertTrue( $result );
	}

	public function test_custom_display() {
		tests_add_filter( 'papi/conditional/rule/==', function ( $rule ) {
			return $rule->value === 'hello';
		} );

		$result = $this->conditional->display( [
			[
				'operator' => '==',
				'slug'     => 'fake',
				'value'    => ''
			]
		] );
		$this->assertFalse( $result );

		$result = $this->conditional->display( [
			[
				'operator' => '==',
				'slug'     => 'fake',
				'value'    => 'hello'
			]
		] );
		$this->assertTrue( $result );

		tests_add_filter( 'papi/conditional/rule/===', function ( $rule ) {
			return null;
		} );

		$result = $this->conditional->display( [
			[
				'operator' => '===',
				'slug'     => 'fake',
				'value'    => ''
			]
		] );
		$this->assertFalse( $result );
	}

	public function test_display_with_simple_slug() {
		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$post_id = $this->factory->post->create();
		$_GET['post'] = $post_id;

		update_post_meta( $post_id, papi_get_page_type_key(), 'simple-page-type' );

		$result = $this->conditional->display( [
			[
				'operator' => 'NOT EXISTS',
				'slug'     => 'name',
				'value'    => ''
			]
		] );

		$this->assertTrue( $result );

		$property = papi_property( [
			'title' => 'Name',
			'type'  => 'string',
			'value' => 'Fredrik'
		] );

		$handler = new Papi_Admin_Meta_Handler();

		$_POST = papi_test_create_property_post_data( [
			'slug'  => $property->slug,
			'type'  => $property,
			'value' => $property->value
		], $_POST );

		$handler->save_properties( $post_id );

		$result = $this->conditional->display( [
			[
				'operator' => 'EXISTS',
				'slug'     => 'name',
				'value'    => ''
			]
		] );

		$this->assertTrue( $result );

		$result = $this->conditional->display( [
			[
				'operator' => '=',
				'slug'     => 'name',
				'value'    => 'Fredrik'
			]
		] );

		$this->assertTrue( $result );
	}

	public function test_display_with_array_slug() {
		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$post_id = $this->factory->post->create();
		$_GET['post'] = $post_id;

		update_post_meta( $post_id, papi_get_page_type_key(), 'simple-page-type' );

		$simple_page_type  = papi_get_entry_type_by_id( 'simple-page-type' );
		$sections_prop     = $simple_page_type->get_property( 'sections' );
		$title_prop        = $simple_page_type->get_property( 'sections[0][title]' );
		$title_prop2       = clone $title_prop->get_options();
		$title_prop3       = clone $title_prop->get_options();
		$title_prop2       = Papi_Property::factory( $title_prop2 );
		$title_prop2->slug = $sections_prop->html_name( $title_prop, 0 );
		$title_prop3       = Papi_Property::factory( $title_prop3 );
		$title_prop3->slug = $sections_prop->html_name( $title_prop, 0 );
		$title_prop3->slug = str_replace( 'title', 'name', $title_prop3->slug );

		$result = $this->conditional->display( [
			[
				'operator' => 'NOT EXISTS',
				'slug'     => 'title',
				'value'    => ''
			]
		], $title_prop );

		$this->assertTrue( $result );

		$result = $this->conditional->display( [
			[
				'operator' => 'NOT EXISTS',
				'slug'     => 'title',
				'value'    => ''
			]
		], $title_prop2 );

		$this->assertTrue( $result );

		$result = $this->conditional->display( [
			[
				'operator' => 'NOT EXISTS',
				'slug'     => 'sections.0.title',
				'value'    => ''
			]
		] );

		$this->assertTrue( $result );

		$result = $this->conditional->display( [
			[
				'operator' => 'NOT EXISTS',
				'slug'     => 'name',
				'value'    => ''
			]
		], $title_prop3 );

		$this->assertTrue( $result );

		$value_slug1         = $title_prop->get_slug( true );
		$value_type_slug1    = papi_get_property_type_key( $value_slug1 );

		$item = [];
		$item[$value_slug1] = 'Hello, world!';
		$item[$value_type_slug1] = $title_prop;

		$handler = new Papi_Admin_Meta_Handler();

		$_POST = papi_test_create_property_post_data( [
			'slug'  => $sections_prop->slug,
			'type'  => $sections_prop,
			'value' => [$item]
		], $_POST );

		$handler->save_properties( $post_id );

		$result = $this->conditional->display( [
			[
				'operator' => 'EXISTS',
				'slug'     => 'title',
				'value'    => ''
			]
		], $title_prop2 );

		$this->assertTrue( $result );

		$result = $this->conditional->display( [
			[
				'operator' => 'EXISTS',
				'slug'     => 'sections.0.title',
				'value'    => ''
			]
		] );

		$this->assertTrue( $result );

		$result = $this->conditional->display( [
			[
				'operator' => '=',
				'slug'     => 'sections.0.title',
				'value'    => 'Hello, world!'
			]
		] );

		$this->assertTrue( $result );
	}
}
