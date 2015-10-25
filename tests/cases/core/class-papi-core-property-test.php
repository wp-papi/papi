<?php

class Papi_Core_Property_Test extends WP_UnitTestCase {

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
		$property = Papi_Core_Property::create( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$this->assertEquals( $property->get_option( 'type' ), 'string' );
		$this->assertEquals( $property->get_option( 'title' ), 'Name' );
		$this->assertEquals( $property->get_option( 'slug' ), 'papi_name' );
	}

	public function test_default_options() {
		$_GET['post'] = 0;
		$_GET['post_type'] = '';
		$property = Papi_Core_Property::create();
		$options = $property->get_options();
		$this->assertTrue( is_object( $options ) );
		$this->assertEmpty( $options->after_class );
		$this->assertEmpty( $options->after_html );
		$this->assertEmpty( $options->before_class );
		$this->assertEmpty( $options->before_html );
		$this->assertSame( [], $options->capabilities );
		$this->assertEmpty( $options->default );
		$this->assertEmpty( $options->description );
		$this->assertFalse( $options->disabled );
		$this->assertTrue( $options->display );
		$this->assertFalse( $options->lang );
		$this->assertFalse( $options->overwrite );
		$this->assertEmpty( $options->post_type );
		$this->assertFalse( $options->raw );
		$this->assertFalse( $options->required );
		$this->assertSame( [], $options->rules );
		$this->assertTrue( is_object( $options->settings ) );
		$this->assertSame( 'papi_', $options->slug );
		$this->assertEquals( 1000, $options->sort_order );
		$this->assertEmpty( $options->title );
		$this->assertEmpty( $options->type );
		$this->assertEmpty( $options->value );
		unset( $_GET['post'] );
		unset( $_GET['post_type'] );
	}

	public function test_disabled() {
		$page_type = papi_get_page_type_by_id( 'properties-page-type' );
		$property  = $page_type->get_property( 'string_test' );
		$this->assertFalse( $property->disabled() );

		$page_type2 = papi_get_page_type_by_id( 'faq-page-type' );
		$property2  = $page_type2->get_property( 'type' );
		$this->assertTrue( $property2->disabled() );
	}

	public function test_display() {
		$page_type = papi_get_page_type_by_id( 'properties-page-type' );
		$property  = $page_type->get_property( 'string_test' );
		$this->assertTrue( $property->display() );

		$page_type2 = papi_get_page_type_by_id( 'faq-page-type' );
		$property2  = $page_type2->get_property( 'type' );
		$this->assertFalse( $property2->display() );
	}

	public function test_factory_fake() {
		require_once PAPI_FIXTURE_DIR . '/properties/class-papi-property-fake.php';

		$this->assertNull( Papi_Core_Property::factory( null ) );
		$this->assertNull( Papi_Core_Property::factory( '' ) );
		$this->assertNull( Papi_Core_Property::factory( 'fake' ) );
	}

	public function test_factory_bad_value() {
		papi()->bind( 'Papi_Core_Property_String', '' );
		$this->assertTrue( Papi_Core_Property::factory( 'string' ) instanceof Papi_Core_Property );
	}

	public function test_factory_property() {
		$property = Papi_Core_Property::create( [
			'type'  => 'string',
			'title' => 'Hello'
		] );
		$this->assertTrue( Papi_Core_Property::factory( $property ) instanceof Papi_Core_Property );
	}

	public function test_format_value() {
		$property = Papi_Core_Property::create( [
			'type'  => 'string',
			'title' => 'Hello'
		] );

		$actual = $property->format_value( 'Fredrik', '', 0 );

		$this->assertEquals( 'Fredrik', $actual );
	}

	public function test_get_default_settings() {
		$property = Papi_Core_Property::create( [
			'type'  => 'string',
			'title' => 'Hello'
		] );

		$this->assertEquals( [], $property->get_default_settings() );
	}

	public function test_get_default_value_fail() {
		$property = Papi_Core_Property::create( [
			'type'  => 'string',
			'title' => 'Hello'
		] );

		$this->assertNull( $property->get_value() );

		$property = Papi_Core_Property::create( [
			'default' => 'Hello',
			'type'    => 'string',
			'title'   => 'Hello'
		] );

		$this->assertNull( $property->get_value() );
	}

	public function test_get_default_value_success() {
		$_GET['post'] = 0;

		$property = Papi_Core_Property::create( [
			'default' => 'Hello',
			'type'    => 'string',
			'title'   => 'Hello'
		] );

		$this->assertEquals( 'Hello', $property->get_value() );

		unset( $_GET['post'] );
	}

	public function test_get_option() {
		$property = new Papi_Core_Property();

		$property->set_option( 'title', 'Name' );

		$this->assertEquals( 'Name', $property->title );
		$this->assertEquals( 'Name', $property->get_option( 'title' ) );

		$property = Papi_Core_Property::create( [
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
		$property = new Papi_Core_Property();

		$this->assertEmpty( $property->get_options() );

		$property = Papi_Core_Property::create( [
			'title' => 'Name'
		] );

		$options = $property->get_options();

		$this->assertEquals( 'Name', $options->title );
	}

	public function test_get_page() {
		$property = new Papi_Core_Property();
		$this->assertTrue( $property->get_page() instanceof Papi_Post_Page );
	}

	public function test_get_post_id() {
		$property = Papi_Core_Property::create();

		$this->assertEquals( $this->post_id, $property->get_post_id() );
	}

	public function test_get_setting() {
		$property = new Papi_Core_Property();
		$this->assertNull( $property->get_setting( null ) );
		$this->assertNull( $property->get_setting( 'length' ) );

		$property = Papi_Core_Property::create( [
			'type'     => 'string',
			'settings' => [
				'length' => 50
			]
		] );

		$this->assertEquals( 50, $property->get_setting( 'length' ) );
	}

	public function test_get_settings() {
		$property = Papi_Core_Property::create( [
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
		$property = new Papi_Core_Property();

		$this->assertEmpty( $property->get_slug() );

		$property = Papi_Core_Property::create( [
			'type'  => 'string',
			'slug'  => 'name',
			'value' => 'Fredrik'
		] );

		$this->assertEquals( 'papi_name', $property->get_slug() );

		$this->assertEquals( 'name', $property->get_slug( true ) );
	}

	public function test_get_value() {
		$property = new Papi_Core_Property();

		$this->assertNull( $property->get_value() );

		$property = Papi_Core_Property::create();

		$this->assertNull( $property->get_value() );

		$property->set_options( [
			'type'  => 'string',
			'slug'  => 'name',
			'value' => 'Fredrik'
		] );

		$this->assertEquals( 'Fredrik', $property->get_value() );
	}

	public function test_get_value_hardcoded() {
		$property = Papi_Core_Property::create( [
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

	public function test_get_value_option() {
		global $current_screen;

		$current_screen = WP_Screen::get( 'admin_init' );

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

		$_SERVER['REQUEST_URI'] = 'http://site.com/wp-admin/options-general.php?page=papi/options/header-option-type';

		update_option( 'name', 'Fredrik' );

		$this->assertEquals( 'Fredrik', $property->get_value() );

		$current_screen = null;
	}

	public function test_import_setting() {
		$property = new Papi_Core_Property();
		$this->assertNull( $property->import_setting( null ) );
	}

	public function test_import_settings() {
		$property = new Papi_Core_Property();
		$output   = $property->import_settings();
		$this->assertEquals( (object) [
			'property_array_slugs' => false
		], $output );
	}

	public function test_html_id() {
		$property = Papi_Core_Property::create( [
			'type'  => 'string',
			'slug'  => 'name'
		] );

		$this->assertEquals( '_papi_name', $property->html_id() );
		$this->assertEquals( '_papi_name_suffix', $property->html_id( 'suffix' ) );
		$this->assertEquals( '_papi_name_black', $property->html_id( 'Black' ) );
		$this->assertEquals( '_papi_name_lank', $property->html_id( 'Länk' ) );
	}

	public function test_html_id_array() {
		$property = Papi_Core_Property::create( [
			'type'  => 'string',
			'slug'  => 'name'
		] );

		$sub_property = Papi_Core_Property::create( [
			'type' => 'number',
			'slug' => 'age'
		] );

		$this->assertEquals( '_papi_name[age]', $property->html_id( $sub_property ) );
		$this->assertEquals( '_papi_name[0][age]', $property->html_id( $sub_property, 0 ) );

		$sub_property = (object) [
			'type' => 'number',
			'slug' => 'age'
		];

		$this->assertEquals( '_papi_name[age]', $property->html_id( $sub_property ) );
		$this->assertEquals( '_papi_name[0][age]', $property->html_id( $sub_property, 0 ) );

		$sub_property = 'non array or object';

		$this->assertEquals( '_papi_name_non_array_or_object', $property->html_id( $sub_property ) );
		$this->assertEquals( '_papi_name_non_array_or_object', $property->html_id( $sub_property, 0 ) );

		$property = Papi_Core_Property::create( [
			'type' => 'number',
			'slug' => 'sections[0][age]'
		] );

		$this->assertEquals( '_papi_sections[0][age_sort_order]', $property->html_id( 'sort_order' ) );
	}

	public function test_html_name() {
		$property = Papi_Core_Property::create( [
			'type'  => 'string',
			'slug'  => 'name'
		] );

		$this->assertEquals( 'papi_name', $property->html_name() );
	}

	public function test_html_name_array() {
		$property = Papi_Core_Property::create( [
			'type'  => 'string',
			'slug'  => 'name'
		] );

		$sub_property = Papi_Core_Property::create( [
			'type' => 'number',
			'slug' => 'age'
		] );

		$this->assertEquals( 'papi_name[age]', $property->html_name( $sub_property ) );
		$this->assertEquals( 'papi_name[0][age]', $property->html_name( $sub_property, 0 ) );

		$sub_property = (object) [
			'type' => 'number',
			'slug' => 'age'
		];

		$this->assertEquals( 'papi_name[age]', $property->html_name( $sub_property ) );
		$this->assertEquals( 'papi_name[0][age]', $property->html_name( $sub_property, 0 ) );

		$sub_property = 'non array or object';

		$this->assertEquals( 'papi_name', $property->html_name( $sub_property ) );
		$this->assertEquals( 'papi_name[0]', $property->html_name( $sub_property, 0 ) );
	}

	public function test_property_import_value() {
		$property = Papi_Core_Property::create( [
			'type' => 'number',
			'slug' => 'age'
		] );

		$this->assertEquals( 'test', $property->import_value( 'test', '', 0 ) );
	}

	public function test_load_value() {
		$property = Papi_Core_Property::create( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$actual = $property->load_value( 'Fredrik', '', 0 );

		$this->assertEquals( 'Fredrik', $actual );
	}

	public function test_match_slug() {
		$property = Papi_Core_Property::create( [
			'description' => 'Test',
			'type'        => 'string',
			'slug'        => 'name',
			'value'       => 'Fredrik'
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

	public function test_post_type_option() {
		$_GET['post_type'] = 'faq';
		$page_type2 = papi_get_page_type_by_id( 'faq-page-type' );
		$property2  = $page_type2->get_property( 'question' );
		$this->assertFalse( $property2->disabled() );

		$_GET['post_type'] = 'page';
		$page_type2 = papi_get_page_type_by_id( 'faq-page-type' );
		$property2  = $page_type2->get_property( 'question' );
		$this->assertTrue( $property2->disabled() );
		unset( $_GET['post_type'] );
	}

	public function test_set_option() {
		$property = Papi_Core_Property::create( [
			'type'  => 'string',
			'title' => 'Hello'
		] );

		$property->set_option( 'title', 'Name' );

		$value = $property->get_option( 'title' );

		$this->assertEquals( 'Name', $value );
	}

	public function test_set_options() {
		$property = Papi_Core_Property::create( [
			'type'     => 'string',
			'title'    => 'Hello'
		] );

		$property->set_options( [
			'title' => 'Name'
		] );

		$this->assertEquals( 'Name', $property->get_option( 'title' ) );

		$property = Papi_Core_Property::create( [
			'type' => 'string'
		] );

		$this->assertEquals( 'papi_string', $property->get_option( 'slug' ) );

		$property = Papi_Core_Property::create( [
			'description' => 'test'
		] );

		$this->assertEquals( 'test', $property->get_option( 'description' ) );
	}

	public function test_set_options_wrong_values() {
		$property1 = Papi_Core_Property::create( null );
		$property2 = Papi_Core_Property::create();
		$this->assertEquals( (array) $property1->get_options(), (array) $property2->get_options() );
	}

	public function test_set_post_id() {
		$property = Papi_Core_Property::create( [
			'type'  => 'string',
			'title' => 'Hello'
		] );

		$property->set_post_id( null );

		$this->assertNotEmpty( $property->get_post_id() );

		$post_id = $this->factory->post->create();
		$property->set_post_id( $post_id );

		$this->assertEquals( $post_id, $property->get_post_id() );
	}

	public function test_set_settings() {
		$property = Papi_Core_Property::create( [
			'type'     => 'string',
			'title'    => 'Hello',
			'settings' => []
		] );

		$this->assertFalse( $property->get_setting( 'allow_html' ) );

		$property->set_setting( 'allow_html', true );

		$this->assertTrue( $property->get_setting( 'allow_html' ) );
	}

	public function test_update_value() {
		$property = Papi_Core_Property::create( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$actual = $property->update_value( 'Fredrik', '', 0 );

		$this->assertEquals( 'Fredrik', $actual );
	}

	public function test_value_serialize() {
		$property = Papi_Core_Property::create( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$value = $property->update_value( [1, 2, 3], '', 0 );
		$this->assertTrue( is_string( $value ) );

		$value1 = $property->load_value( $value, '', 0 );
		$this->assertEquals( [1, 2, 3], $value1 );

		$value2 = $property->format_value( $value, '', 0 );
		$this->assertEquals( [1, 2, 3], $value2 );
	}
}
