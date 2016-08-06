<?php

class Papi_Property_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );
	}

	public function test_html() {
		$property = Papi_Property::factory();
		$this->assertEmpty( $property->html() );
	}

	public function test_render_with_description() {
		$property = Papi_Property::factory( [
			'description' => 'Hello, world',
			'slug'        => 'render_with_description_test',
			'type'        => 'string'
		] );

		$property->render();

		$this->expectOutputRegex( '/Hello\,\sworld/' );
	}

	public function test_render_with_html_array_slug() {
		$property = Papi_Property::factory( [
			'slug' => 'sections[0][render_with_description_test]',
			'type' => 'string'
		] );

		$property->render();

		$this->expectOutputRegex( '/papi\_sections\[0\]\[render\_with\_description\_test\_property\]/' );
	}

	public function test_render_with_lang() {
		$property = Papi_Property::factory( [
			'lang'  => 'dk',
			'raw'   => true,
			'slug'  => 'hidden_test_2',
			'title' => 'Hidden test 2',
			'type'  => 'hidden'
		] );

		$property->render();

		$this->expectOutputRegex( '//' );

		$property = Papi_Property::factory( [
			'lang'  => 'dk',
			'raw'   => true,
			'slug'  => 'hidden_test_2',
			'title' => 'Hidden test 2',
			'type'  => 'hidden'
		] );

		$_GET['lang'] = 'dk';

		$property->render();

		$this->expectOutputRegex( '/class=\"papi\-hide/' );

		unset( $_GET['lang'] );
	}

	public function test_render_fail() {
		$property = Papi_Property::factory( [
			'disabled' => true,
			'slug'     => 'render_fail',
			'type'     => 'string',
			'title'    => 'Render fail'
		] );

		$property->render();

		$this->expectOutputRegex( '//' );

		$property = Papi_Property::factory( [
			'rules' => [
				[
					'operator' => '=',
					'slug'     => 'render_fail',
					'value'	   => 'Fredrik'
				]
			],
			'slug'  => 'render_fail',
			'type'  => 'string',
			'title' => 'Render fail'
		] );

		$property->render();

		$this->expectOutputRegex( '//' );
	}

	public function test_render_with_after_class() {
		$page_type = papi_get_entry_type_by_id( 'simple-page-type' );
		$property  = $page_type->get_property( 'siffran' );
		$property->render();
		$this->expectOutputRegex( '/class=\"papi-after-html tva-siffra\"/' );
	}

	public function test_render_with_after_html() {
		$page_type = papi_get_entry_type_by_id( 'simple-page-type' );
		$property  = $page_type->get_property( 'siffran' );
		$property->render();
		$this->expectOutputRegex( '/\>Två siffra\<\/div\>/' );
	}

	public function test_render_with_before_class() {
		$page_type = papi_get_entry_type_by_id( 'simple-page-type' );
		$property  = $page_type->get_property( 'siffran' );
		$property->render();
		$this->expectOutputRegex( '/class=\"papi-before-html en-siffra\"/' );
	}

	public function test_render_with_before_html() {
		$page_type = papi_get_entry_type_by_id( 'simple-page-type' );
		$property  = $page_type->get_property( 'siffran' );
		$property->render();
		$this->expectOutputRegex( '/\>En siffra\<\/div\>/' );
	}
}
