<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Property` class.
 *
 * @package Papi
 */

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

	public function test_converter() {
		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'properties-page-type' );

		$page_type = papi_get_page_type_by_id( 'properties-page-type' );
		$flexible  = $page_type->get_property( 'flexible_test_other' );

		$this->assertEquals( 'flexible', $flexible->type );
		$this->assertEquals( 'flexible', $flexible->get_option( 'type' ) );
		$this->assertEquals( 'Flexible test', $flexible->title );
		$this->assertEquals( 'Flexible test', $flexible->get_option( 'title' ) );
		$this->assertEquals( 'papi_flexible_test_other', $flexible->slug );
		$this->assertEquals( 'papi_flexible_test_other', $flexible->get_option( 'slug' ) );
		$this->assertEquals( 'papi_flexible_test_other', $flexible->get_slug() );
		$this->assertEquals( 'flexible_test_other', $flexible->get_slug( true ) );

		$flexible_children = $flexible->get_child_properties();

		$this->assertEquals( 'Twitter', $flexible_children['twitter']['title'] );
		$this->assertEquals( 'string', $flexible_children['twitter']['items'][0]->type );
		$this->assertEquals( 'string', $flexible_children['twitter']['items'][0]->get_option( 'type' ) );
		$this->assertEquals( 'papi_twitter_name', $flexible_children['twitter']['items'][0]->slug );
		$this->assertEquals( 'papi_twitter_name', $flexible_children['twitter']['items'][0]->get_option( 'slug' ) );
		$this->assertEquals( 'papi_twitter_name', $flexible_children['twitter']['items'][0]->get_slug() );
		$this->assertEquals( 'twitter_name', $flexible_children['twitter']['items'][0]->get_slug( true ) );
		$this->assertEquals( 'Twitter name', $flexible_children['twitter']['items'][0]->title );
		$this->assertEquals( 'Twitter name', $flexible_children['twitter']['items'][0]->get_option( 'title' ) );
		$this->assertEquals( 'Posts', $flexible_children['posts']['title'] );

		$this->assertEquals( 'Posts', $flexible_children['posts']['title'] );
		$this->assertEquals( 'post', $flexible_children['posts']['items'][0]->type );
		$this->assertEquals( 'post', $flexible_children['posts']['items'][0]->get_option( 'type' ) );
		$this->assertEquals( 'papi_post_one', $flexible_children['posts']['items'][0]->slug );
		$this->assertEquals( 'papi_post_one', $flexible_children['posts']['items'][0]->get_option( 'slug' ) );
		$this->assertEquals( 'papi_post_one', $flexible_children['posts']['items'][0]->get_slug() );
		$this->assertEquals( 'post_one', $flexible_children['posts']['items'][0]->get_slug( true ) );
		$this->assertEquals( 'Post one', $flexible_children['posts']['items'][0]->title );
		$this->assertEquals( 'Post one', $flexible_children['posts']['items'][0]->get_option( 'title' ) );
		$this->assertEquals( 'post', $flexible_children['posts']['items'][1]->type );
		$this->assertEquals( 'post', $flexible_children['posts']['items'][1]->get_option( 'type' ) );
		$this->assertEquals( 'papi_post_two', $flexible_children['posts']['items'][1]->slug );
		$this->assertEquals( 'papi_post_two', $flexible_children['posts']['items'][1]->get_option( 'slug' ) );
		$this->assertEquals( 'papi_post_two', $flexible_children['posts']['items'][1]->get_slug() );
		$this->assertEquals( 'post_two', $flexible_children['posts']['items'][1]->get_slug( true ) );
		$this->assertEquals( 'Post two', $flexible_children['posts']['items'][1]->title );
		$this->assertEquals( 'Post two', $flexible_children['posts']['items'][1]->get_option( 'title' ) );

		$this->assertEquals( 'List', $flexible_children['list']['title'] );
		$repeater = $flexible_children['list']['items'][0];

		$this->assertEquals( 'repeater', $repeater->type );
		$this->assertEquals( 'repeater', $repeater->get_option( 'type' ) );
		$this->assertEquals( 'Repeater test', $repeater->title );
		$this->assertEquals( 'Repeater test', $repeater->get_option( 'title' ) );
		$this->assertEquals( 'papi_repeater_test_other', $repeater->slug );
		$this->assertEquals( 'papi_repeater_test_other', $repeater->get_option( 'slug' ) );
		$this->assertEquals( 'papi_repeater_test_other', $repeater->get_slug() );
		$this->assertEquals( 'repeater_test_other', $repeater->get_slug( true ) );

		$repeater_children = $repeater->get_child_properties();

		$this->assertEquals( 'string', $repeater_children[0]->type );
		$this->assertEquals( 'string', $repeater_children[0]->get_option( 'type' ) );
		$this->assertEquals( 'papi_book_name', $repeater_children[0]->slug );
		$this->assertEquals( 'papi_book_name', $repeater_children[0]->get_option( 'slug' ) );
		$this->assertEquals( 'papi_book_name', $repeater_children[0]->get_slug() );
		$this->assertEquals( 'book_name', $repeater_children[0]->get_slug( true ) );
		$this->assertEquals( 'Book name', $repeater_children[0]->title );
		$this->assertEquals( 'Book name', $repeater_children[0]->get_option( 'title' ) );

		$this->assertEquals( 'bool', $repeater_children[1]->type );
		$this->assertEquals( 'bool', $repeater_children[1]->get_option( 'type' ) );
		$this->assertEquals( 'papi_is_open', $repeater_children[1]->slug );
		$this->assertEquals( 'papi_is_open', $repeater_children[1]->get_option( 'slug' ) );
		$this->assertEquals( 'papi_is_open', $repeater_children[1]->get_slug() );
		$this->assertEquals( 'is_open', $repeater_children[1]->get_slug( true ) );
		$this->assertEquals( 'Is open?', $repeater_children[1]->title );
		$this->assertEquals( 'Is open?', $repeater_children[1]->get_option( 'title' ) );

		$this->assertEquals( 'List 2', $flexible_children['list2']['title'] );
		$repeater2 = $flexible_children['list2']['items'][0];

		$this->assertEquals( 'repeater', $repeater2->type );
		$this->assertEquals( 'repeater', $repeater2->get_option( 'type' ) );
		$this->assertEquals( 'Repeater test 2', $repeater2->title );
		$this->assertEquals( 'Repeater test 2', $repeater2->get_option( 'title' ) );
		$this->assertEquals( 'papi_repeater_test_other_2', $repeater2->slug );
		$this->assertEquals( 'papi_repeater_test_other_2', $repeater2->get_option( 'slug' ) );
		$this->assertEquals( 'papi_repeater_test_other_2', $repeater2->get_slug() );
		$this->assertEquals( 'repeater_test_other_2', $repeater2->get_slug( true ) );

		$repeater_children2 = $repeater->get_child_properties();

		$this->assertEquals( 'string', $repeater_children2[0]->type );
		$this->assertEquals( 'string', $repeater_children2[0]->get_option( 'type' ) );
		$this->assertEquals( 'papi_book_name', $repeater_children2[0]->slug );
		$this->assertEquals( 'papi_book_name', $repeater_children2[0]->get_option( 'slug' ) );
		$this->assertEquals( 'papi_book_name', $repeater_children2[0]->get_slug() );
		$this->assertEquals( 'book_name', $repeater_children2[0]->get_slug( true ) );
		$this->assertEquals( 'Book name', $repeater_children2[0]->title );
		$this->assertEquals( 'Book name', $repeater_children2[0]->get_option( 'title' ) );

		$this->assertEquals( 'bool', $repeater_children2[1]->type );
		$this->assertEquals( 'bool', $repeater_children2[1]->get_option( 'type' ) );
		$this->assertEquals( 'papi_is_open', $repeater_children2[1]->slug );
		$this->assertEquals( 'papi_is_open', $repeater_children2[1]->get_option( 'slug' ) );
		$this->assertEquals( 'papi_is_open', $repeater_children2[1]->get_slug() );
		$this->assertEquals( 'is_open', $repeater_children2[1]->get_slug( true ) );
		$this->assertEquals( 'Is open?', $repeater_children2[1]->title );
		$this->assertEquals( 'Is open?', $repeater_children2[1]->get_option( 'title' ) );
	}

	public function test_create() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$this->assertEquals( $property->get_option( 'type' ), 'string' );
		$this->assertEquals( $property->get_option( 'title' ), 'Name' );
		$this->assertEquals( $property->get_option( 'slug' ), 'papi_name' );
	}

	public function test_default_options() {
		$default_options = Papi_Property::default_options();
		$this->assertTrue( is_array( $default_options ) );
		$this->assertEmpty( $default_options['title'] );
		$this->assertEquals( 'string', $default_options['type'] );
		$this->assertEmpty( $default_options['slug'] );
		$this->assertEquals( 1000, $default_options['sort_order'] );
	}

	public function test_factory_fake() {
		require_once PAPI_FIXTURE_DIR . '/properties/class-papi-property-fake.php';

		$this->assertNull( Papi_Property::factory( null ) );
		$this->assertNull( Papi_Property::factory( '' ) );
		$this->assertNull( Papi_Property::factory( 'fake' ) );
	}

	public function test_factory_bad_value() {
		papi()->bind( 'Papi_Property_String', '' );
		$this->assertTrue( Papi_Property::factory( 'string' ) instanceof Papi_Property );
	}

	public function test_factory_property() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'title' => 'Hello'
		] );
		$this->assertTrue( Papi_Property::factory( $property ) instanceof Papi_Property );
	}

	public function test_format_value() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'title' => 'Hello'
		] );

		$actual = $property->format_value( 'Fredrik', '', 0 );

		$this->assertEquals( 'Fredrik', $actual );
	}

	public function test_get_default_settings() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'title' => 'Hello'
		] );

		$this->assertEquals( [], $property->get_default_settings() );
	}

	public function test_get_default_value() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'title' => 'Hello'
		] );

		$this->assertNull( $property->get_value() );

		$property->set_options( [
			'type'  => 'string',
			'slug'  => 'name',
			'value' => 'hello value'
		] );

		$this->assertEquals( 'hello value', $property->get_value() );

		update_post_meta( $this->post_id, PAPI_PAGE_TYPE_KEY, 'simple-page-type' );

		$page_type = papi_get_page_type_by_id( 'simple-page-type' );
		$property  = $page_type->get_property( 'name_default' );

		$this->assertEquals( 'Fredrik', $property->get_value() );
	}

	public function test_get_option() {
		$property = new Papi_Property();

		$property->set_option( 'title', 'Name' );

		$this->assertEquals( 'Name', $property->title );
		$this->assertEquals( 'Name', $property->get_option( 'title' ) );

		$property = Papi_Property::create( [
			'title' => 'Name'
		] );

		$this->assertNull( $property->fake );
		$this->assertNull( $property->get_option( 'fake' ) );
		$this->assertEquals( 'Name', $property->title );
		$this->assertEquals( 'Name', $property->get_option( 'title' ) );
		$this->assertEquals( 1000, $property->sort_order );
		$this->assertEquals( 1000, $property->get_option( 'sort_order' ) );

		$property->title = 'Link';

		$this->assertEquals( 'Link', $property->title );

		$settings = $property->get_option( 'settings' );
		$this->assertTrue( is_object( $settings ) );
	}

	public function test_get_options() {
		$property = new Papi_Property();

		$this->assertEmpty( $property->get_options() );

		$property = Papi_Property::create( [
			'title' => 'Name'
		] );

		$options = $property->get_options();

		$this->assertEquals( 'Name', $options->title );
	}

	public function test_get_page() {
		$property = new Papi_Property();
		$this->assertTrue( $property->get_page() instanceof Papi_Post_Page );
	}

	public function test_get_post_id() {
		$property = Papi_Property::create();

		$this->assertEquals( $this->post_id, $property->get_post_id() );
	}

	public function test_get_setting() {
		$property = new Papi_Property();
		$this->assertNull( $property->get_setting( 'length' ) );

		$property = Papi_Property::create( [
			'type'     => 'string',
			'settings' => [
				'length' => 50
			]
		] );

		$this->assertEquals( 50, $property->get_setting( 'length' ) );
	}

	public function test_get_settings() {
		$property = Papi_Property::create( [
			'settings' => [
				'items' => [
					[
						'type' => 'faker'
					],
					[
						'type' => 'fake'
					]
				]
			]
		] );

		$settings = $property->get_settings();

		$this->assertTrue( is_object( $settings ) );
		$this->assertEmpty( $settings->items );
	}

	public function test_get_slug() {
		$property = new Papi_Property();

		$this->assertEmpty( $property->get_slug() );

		$property = Papi_Property::create( [
			'type'  => 'string',
			'slug'  => 'name',
			'value' => 'Fredrik'
		] );

		$this->assertEquals( 'papi_name', $property->get_slug() );

		$this->assertEquals( 'name', $property->get_slug( true ) );
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

		$this->assertEquals( 'Fredrik', $property->get_value() );
	}

	public function test_get_value_option() {
		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$_GET['page'] = 'papi/options/header-option-type';

		$page = papi_get_page( 0, 'option' );
		$property = $page->get_property( 'name' );
		$this->assertEquals( 'string', $property->get_option( 'type' ) );
		$this->assertEquals( 'string', $property->type );
		$this->assertEquals( 'papi_name', $property->slug );
		$this->assertEquals( 'papi_name', $property->get_option( 'slug' ) );
		$this->assertEquals( 'Name', $property->get_option( 'title' ) );
		$this->assertEquals( 'Name', $property->title );

		$this->assertEmpty( $property->get_value() );

		$old_request_uri = $_SERVER['REQUEST_URI'];

		$_SERVER['REQUEST_URI'] = 'http://site.com/wp-admin/options-general.php?page=papi%2Foptions%2Fheader-option-type';

		update_option( 'name', 'Fredrik' );

		$this->assertEquals( 'Fredrik', $property->get_value() );

		$_SERVER['REQUEST_URI'] = $old_request_uri;
	}

	public function test_html() {
		$property = Papi_Property::create();
		$this->assertEmpty( $property->html() );
	}

	public function test_html_id() {
		$property = Papi_Property::create();

		$property->set_options( [
			'type'  => 'string',
			'slug'  => 'name'
		] );

		$this->assertEquals( '_papi_name', $property->html_id() );
		$this->assertEquals( '_papi_name_suffix', $property->html_id( 'suffix' ) );
		$this->assertEquals( '_papi_name_black', $property->html_id( 'Black' ) );
		$this->assertEquals( '_papi_name_lank', $property->html_id( 'Länk' ) );

		$sub_property = Papi_Property::create( [
			'type' => 'number',
			'slug' => 'age'
		] );

		$this->assertEquals( '_papi_name[age]', $property->html_id( $sub_property ) );
		$this->assertEquals( '_papi_name[0][age]', $property->html_id( $sub_property, 0 ) );
	}

	public function test_html_name() {
		$property = Papi_Property::create();

		$property->set_options( [
			'type'  => 'string',
			'slug'  => 'name'
		] );

		$this->assertEquals( 'papi_name', $property->html_name() );

		$sub_property = Papi_Property::create( [
			'type' => 'number',
			'slug' => 'age'
		] );

		$this->assertEquals( 'papi_name[age]', $property->html_name( $sub_property ) );
		$this->assertEquals( 'papi_name[0][age]', $property->html_name( $sub_property, 0 ) );

		$sub_property = (object) array(
			'type' => 'number',
			'slug' => 'age'
		);

		$this->assertEquals( 'papi_name[age]', $property->html_name( $sub_property ) );
		$this->assertEquals( 'papi_name[0][age]', $property->html_name( $sub_property, 0 ) );

		$sub_property = 'non array or object';

		$this->assertEquals( 'papi_name', $property->html_name( $sub_property ) );
		$this->assertEquals( 'papi_name[0]', $property->html_name( $sub_property, 0 ) );
	}

	public function test_load_value() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$actual = $property->load_value( 'Fredrik', '', 0 );

		$this->assertEquals( 'Fredrik', $actual );
	}

	public function test_match_slug() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'slug'  => 'name',
			'value' => 'Fredrik'
		] );

		$this->assertTrue( $property->match_slug( 'name' ) );
		$this->assertTrue( $property->match_slug( 'papi_name' ) );

		$this->assertFalse( $property->match_slug( 'kvack' ) );
		$this->assertFalse( $property->match_slug( 'papi_kvack' ) );
		$this->assertFalse( $property->match_slug( null ) );
		$this->assertFalse( $property->match_slug( true ) );
		$this->assertFalse( $property->match_slug( false ) );
		$this->assertFalse( $property->match_slug( 1 ) );
		$this->assertFalse( $property->match_slug( 0 ) );
		$this->assertFalse( $property->match_slug( [] ) );
		$this->assertFalse( $property->match_slug( (object) [] ) );
		$this->assertFalse( $property->match_slug( '' ) );
	}

	public function test_render_description_html() {
		$property = new Papi_Property();

		$this->assertNull( $property->render_description_html() );

		$property->set_options( [
			'description' => 'A simple description'
		] );

		$property->render_description_html();

		$this->expectOutputRegex( '/A\ssimple\sdescription/' );
	}

	public function test_render_hidden_html() {
		$property = new Papi_Property();

		$this->assertNull( $property->render_hidden_html() );

		$property->set_options( papi_get_property_options( [
			'type' => 'string',
			'slug' => 'hello_world'
		] ) );

		$property->render_hidden_html();

		$this->expectOutputRegex( '/papi\_hello\_world\_property/' );

		$property->set_options( [
			'type' => 'string',
			'slug' => 'hello_world[name]'
		] );

		$property->render_hidden_html();

		$this->expectOutputRegex( '/papi\_hello\_world\[name\_property\]/' );
	}

	public function test_render_label_html() {
		$property = new Papi_Property();

		$this->assertNull( $property->render_label_html() );

		$property->set_options( [
			'slug'  => 'kvack',
			'title' => 'A simple label'
		] );

		$property->render_label_html();

		$this->expectOutputRegex( '/A\ssimple\slabel/' );
		$this->expectOutputRegex( '/papi\_kvack/' );
	}

	public function test_render_row_html() {
		$property = new Papi_Property();

		$this->assertNull( $property->render_row_html() );

		$property->set_options( [
			'title' 	  => 'A simple label',
			'description' => 'A simple description'
		] );

		$property->render_row_html();

		$this->expectOutputRegex( '/A\ssimple\sdescription/' );
		$this->expectOutputRegex( '/A\ssimple\slabel/' );

		$property->set_options( [
			'raw' => true
		] );

		$property->render_row_html();
	}

	public function test_render_row_html_hidden() {
		$property = Papi_Property::factory( [
			'raw'   => true,
			'slug'  => 'hidden_test_2',
			'title' => 'Hidden test 2',
			'type'  => 'hidden'
		] );

		$property->render_row_html();

		$this->expectOutputRegex( '/class=\"papi\-hide\"/' );
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

	public function test_set_option() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'title' => 'Hello'
		] );

		$property->set_option( 'title', 'Name' );

		$value = $property->get_option( 'title' );

		$this->assertEquals( 'Name', $value );
	}

	public function test_set_options() {
		$property = Papi_Property::create( [
			'type'     => 'string',
			'title'    => 'Hello'
		] );

		$property->set_options( [
			'title' => 'Name'
		] );

		$this->assertEquals( 'Name', $property->get_option( 'title' ) );

		$property = Papi_Property::create( [
			'type' => 'string'
		] );

		$this->assertEquals( 'papi_string', $property->get_option( 'slug' ) );
	}

	public function test_set_settings() {
		$property = Papi_Property::create( [
			'type'     => 'string',
			'title'    => 'Hello',
			'settings' => []
		] );

		$this->assertFalse( $property->get_setting( 'allow_html' ) );

		$property->set_setting( 'allow_html', true );

		$this->assertTrue( $property->get_setting( 'allow_html' ) );
	}

	public function test_update_value() {
		$property = Papi_Property::create( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$actual = $property->update_value( 'Fredrik', '', 0 );

		$this->assertEquals( 'Fredrik', $actual );
	}

}
