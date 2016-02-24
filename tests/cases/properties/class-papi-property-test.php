<?php

class Papi_Property_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->post_id = $this->factory->post->create();
		$_GET['post'] = $this->post_id;
	}

	public function tearDown() {
		parent::tearDown();
		unset( $_GET, $this->post_id );
	}

	public function test_get_value() {
		$property = new Papi_Property();

		$this->assertNull( $property->get_value() );

		$property = Papi_Property::create();

		$this->assertNull( $property->get_value() );

		$property->set_options( [
			'type'  => 'string',
			'slug'  => 'name',
			'value' => 'Fredrik'
		] );

		$this->assertSame( 'Fredrik', $property->get_value() );
	}

	public function test_get_value_hardcoded() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'slug'  => 'name',
			'value' => 'hello value'
		] );

		$this->assertSame( 'hello value', $property->get_value() );

		update_post_meta( $this->post_id, papi_get_page_type_key(), 'simple-page-type' );

		$page_type = papi_get_page_type_by_id( 'simple-page-type' );
		$property  = $page_type->get_property( 'name_default' );

		$this->assertSame( 'Fredrik', $property->get_value() );
	}

	public function test_get_value_option() {
		global $current_screen;

		$current_screen = WP_Screen::get( 'admin_init' );

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$_GET['page'] = 'papi/option/options/header-option-type';

		$page = papi_get_page( 0, 'option' );
		$property = $page->get_property( 'name' );
		$this->assertSame( 'string', $property->get_option( 'type' ) );
		$this->assertSame( 'string', $property->type );
		$this->assertSame( 'papi_name', $property->slug );
		$this->assertSame( 'papi_name', $property->get_option( 'slug' ) );
		$this->assertSame( 'Name', $property->get_option( 'title' ) );
		$this->assertSame( 'Name', $property->title );

		$this->assertEmpty( $property->get_value() );

		$_SERVER['REQUEST_URI'] = 'http://site.com/wp-admin/options-general.php?page=papi/options/header-option-type';

		update_option( 'name', 'Fredrik' );

		$this->assertSame( 'Fredrik', $property->get_value() );

		$current_screen = null;
	}

	public function test_html() {
		$property = Papi_Property::create();
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

		$this->expectOutputRegex( '/class=\"papi\-hide\"/' );

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
		$page_type = papi_get_page_type_by_id( 'simple-page-type' );
		$property  = $page_type->get_property( 'siffran' );
		$property->render();
		$this->expectOutputRegex( '/class=\"papi-after-html tva-siffra\"/' );
	}

	public function test_render_with_after_html() {
		$page_type = papi_get_page_type_by_id( 'simple-page-type' );
		$property  = $page_type->get_property( 'siffran' );
		$property->render();
		$this->expectOutputRegex( '/\>Två siffra\<\/div\>/' );
	}

	public function test_render_with_before_class() {
		$page_type = papi_get_page_type_by_id( 'simple-page-type' );
		$property  = $page_type->get_property( 'siffran' );
		$property->render();
		$this->expectOutputRegex( '/class=\"papi-before-html en-siffra\"/' );
	}

	public function test_render_with_before_html() {
		$page_type = papi_get_page_type_by_id( 'simple-page-type' );
		$property  = $page_type->get_property( 'siffran' );
		$property->render();
		$this->expectOutputRegex( '/\>En siffra\<\/div\>/' );
	}
}
