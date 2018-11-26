<?php

/**
 * @group core
 */
class Papi_Core_Property_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		add_filter( 'papi/settings/directories', function () {
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
		update_post_meta( $this->post_id, papi_get_page_type_key(), 'properties-page-type' );

		$page_type = papi_get_entry_type_by_id( 'properties-page-type' );
		$flexible  = $page_type->get_property( 'flexible_test_other' );

		$this->assertSame( 'flexible', $flexible->type );
		$this->assertSame( 'flexible', $flexible->get_option( 'type' ) );
		$this->assertSame( 'Flexible test', $flexible->title );
		$this->assertSame( 'Flexible test', $flexible->get_option( 'title' ) );
		$this->assertSame( 'papi_flexible_test_other', $flexible->slug );
		$this->assertSame( 'papi_flexible_test_other', $flexible->get_option( 'slug' ) );
		$this->assertSame( 'papi_flexible_test_other', $flexible->get_slug() );
		$this->assertSame( 'flexible_test_other', $flexible->get_slug( true ) );

		$flexible_children = $flexible->get_child_properties();

		$this->assertSame( 'Twitter', $flexible_children['twitter']['title'] );
		$this->assertSame( 'string', $flexible_children['twitter']['items'][0]->type );
		$this->assertSame( 'string', $flexible_children['twitter']['items'][0]->get_option( 'type' ) );
		$this->assertSame( 'papi_twitter_name', $flexible_children['twitter']['items'][0]->slug );
		$this->assertSame( 'papi_twitter_name', $flexible_children['twitter']['items'][0]->get_option( 'slug' ) );
		$this->assertSame( 'papi_twitter_name', $flexible_children['twitter']['items'][0]->get_slug() );
		$this->assertSame( 'twitter_name', $flexible_children['twitter']['items'][0]->get_slug( true ) );
		$this->assertSame( 'Twitter name', $flexible_children['twitter']['items'][0]->title );
		$this->assertSame( 'Twitter name', $flexible_children['twitter']['items'][0]->get_option( 'title' ) );
		$this->assertSame( 'Posts', $flexible_children['posts']['title'] );

		$this->assertSame( 'Posts', $flexible_children['posts']['title'] );
		$this->assertSame( 'post', $flexible_children['posts']['items'][0]->type );
		$this->assertSame( 'post', $flexible_children['posts']['items'][0]->get_option( 'type' ) );
		$this->assertSame( 'papi_post_one', $flexible_children['posts']['items'][0]->slug );
		$this->assertSame( 'papi_post_one', $flexible_children['posts']['items'][0]->get_option( 'slug' ) );
		$this->assertSame( 'papi_post_one', $flexible_children['posts']['items'][0]->get_slug() );
		$this->assertSame( 'post_one', $flexible_children['posts']['items'][0]->get_slug( true ) );
		$this->assertSame( 'Post one', $flexible_children['posts']['items'][0]->title );
		$this->assertSame( 'Post one', $flexible_children['posts']['items'][0]->get_option( 'title' ) );
		$this->assertSame( 'post', $flexible_children['posts']['items'][1]->type );
		$this->assertSame( 'post', $flexible_children['posts']['items'][1]->get_option( 'type' ) );
		$this->assertSame( 'papi_post_two', $flexible_children['posts']['items'][1]->slug );
		$this->assertSame( 'papi_post_two', $flexible_children['posts']['items'][1]->get_option( 'slug' ) );
		$this->assertSame( 'papi_post_two', $flexible_children['posts']['items'][1]->get_slug() );
		$this->assertSame( 'post_two', $flexible_children['posts']['items'][1]->get_slug( true ) );
		$this->assertSame( 'Post two', $flexible_children['posts']['items'][1]->title );
		$this->assertSame( 'Post two', $flexible_children['posts']['items'][1]->get_option( 'title' ) );

		$this->assertSame( 'List', $flexible_children['list']['title'] );
		$repeater = $flexible_children['list']['items'][0];

		$this->assertSame( 'repeater', $repeater->type );
		$this->assertSame( 'repeater', $repeater->get_option( 'type' ) );
		$this->assertSame( 'Repeater test', $repeater->title );
		$this->assertSame( 'Repeater test', $repeater->get_option( 'title' ) );
		$this->assertSame( 'papi_repeater_test_other', $repeater->slug );
		$this->assertSame( 'papi_repeater_test_other', $repeater->get_option( 'slug' ) );
		$this->assertSame( 'papi_repeater_test_other', $repeater->get_slug() );
		$this->assertSame( 'repeater_test_other', $repeater->get_slug( true ) );

		$repeater_children = $repeater->get_child_properties();

		$this->assertSame( 'string', $repeater_children[0]->type );
		$this->assertSame( 'string', $repeater_children[0]->get_option( 'type' ) );
		$this->assertSame( 'papi_book_name', $repeater_children[0]->slug );
		$this->assertSame( 'papi_book_name', $repeater_children[0]->get_option( 'slug' ) );
		$this->assertSame( 'papi_book_name', $repeater_children[0]->get_slug() );
		$this->assertSame( 'book_name', $repeater_children[0]->get_slug( true ) );
		$this->assertSame( 'Book name', $repeater_children[0]->title );
		$this->assertSame( 'Book name', $repeater_children[0]->get_option( 'title' ) );

		$this->assertSame( 'bool', $repeater_children[1]->type );
		$this->assertSame( 'bool', $repeater_children[1]->get_option( 'type' ) );
		$this->assertSame( 'papi_is_open', $repeater_children[1]->slug );
		$this->assertSame( 'papi_is_open', $repeater_children[1]->get_option( 'slug' ) );
		$this->assertSame( 'papi_is_open', $repeater_children[1]->get_slug() );
		$this->assertSame( 'is_open', $repeater_children[1]->get_slug( true ) );
		$this->assertSame( 'Is open?', $repeater_children[1]->title );
		$this->assertSame( 'Is open?', $repeater_children[1]->get_option( 'title' ) );

		$this->assertSame( 'List 2', $flexible_children['list2']['title'] );
		$repeater2 = $flexible_children['list2']['items'][0];

		$this->assertSame( 'repeater', $repeater2->type );
		$this->assertSame( 'repeater', $repeater2->get_option( 'type' ) );
		$this->assertSame( 'Repeater test 2', $repeater2->title );
		$this->assertSame( 'Repeater test 2', $repeater2->get_option( 'title' ) );
		$this->assertSame( 'papi_repeater_test_other_2', $repeater2->slug );
		$this->assertSame( 'papi_repeater_test_other_2', $repeater2->get_option( 'slug' ) );
		$this->assertSame( 'papi_repeater_test_other_2', $repeater2->get_slug() );
		$this->assertSame( 'repeater_test_other_2', $repeater2->get_slug( true ) );

		$repeater_children2 = $repeater->get_child_properties();

		$this->assertSame( 'string', $repeater_children2[0]->type );
		$this->assertSame( 'string', $repeater_children2[0]->get_option( 'type' ) );
		$this->assertSame( 'papi_book_name', $repeater_children2[0]->slug );
		$this->assertSame( 'papi_book_name', $repeater_children2[0]->get_option( 'slug' ) );
		$this->assertSame( 'papi_book_name', $repeater_children2[0]->get_slug() );
		$this->assertSame( 'book_name', $repeater_children2[0]->get_slug( true ) );
		$this->assertSame( 'Book name', $repeater_children2[0]->title );
		$this->assertSame( 'Book name', $repeater_children2[0]->get_option( 'title' ) );

		$this->assertSame( 'bool', $repeater_children2[1]->type );
		$this->assertSame( 'bool', $repeater_children2[1]->get_option( 'type' ) );
		$this->assertSame( 'papi_is_open', $repeater_children2[1]->slug );
		$this->assertSame( 'papi_is_open', $repeater_children2[1]->get_option( 'slug' ) );
		$this->assertSame( 'papi_is_open', $repeater_children2[1]->get_slug() );
		$this->assertSame( 'is_open', $repeater_children2[1]->get_slug( true ) );
		$this->assertSame( 'Is open?', $repeater_children2[1]->title );
		$this->assertSame( 'Is open?', $repeater_children2[1]->get_option( 'title' ) );
	}

	public function test_current_user_can() {
		$property = Papi_Core_Property::factory( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$this->assertTrue( $property->current_user_can() );

		$property = Papi_Core_Property::factory( [
			'type'         => 'string',
			'title'        => 'Name',
			'capabilities' => ['fake']
		] );

		$this->assertFalse( $property->current_user_can() );
	}

	public function test_default_options() {
		$_GET['post'] = 0;
		$_GET['post_type'] = '';
		$property = Papi_Core_Property::factory();
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
		$this->assertSame( 1000, $options->sort_order );
		$this->assertEmpty( $options->title );
		$this->assertEmpty( $options->type );
		$this->assertEmpty( $options->value );
		unset( $_GET['post'] );
		unset( $_GET['post_type'] );
	}

	public function test_disabled() {
		$_GET['post_type'] = 'page';

		$property = Papi_Core_Property::factory( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$this->assertFalse( $property->disabled() );

		$property = Papi_Core_Property::factory( [
			'type'    => 'string',
			'title'   => 'Name',
			'site_id' => 2
		] );

		$this->assertTrue( $property->disabled() );

		unset( $_GET['post_type'] );
	}

	public function test_display() {
		$page_type = papi_get_entry_type_by_id( 'properties-page-type' );
		$property  = $page_type->get_property( 'string_test' );
		$this->assertTrue( $property->display() );

		$page_type2 = papi_get_entry_type_by_id( 'faq-page-type' );
		$property2  = $page_type2->get_property( 'type' );
		$this->assertFalse( $property2->display() );
	}

	public function test_factory() {
		$property = Papi_Core_Property::factory( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$this->assertSame( $property->get_option( 'type' ), 'string' );
		$this->assertSame( $property->get_option( 'title' ), 'Name' );
		$this->assertSame( $property->get_option( 'slug' ), 'papi_name' );
	}

	public function test_factory_fake() {
		require_once PAPI_FIXTURE_DIR . '/properties/class-papi-property-fake.php';

		$this->assertNull( Papi_Core_Property::factory( '' ) );
		$this->assertNull( Papi_Core_Property::factory( 'fake' ) );
	}

	public function test_factory_bad_value() {
		papi()->bind( 'Papi_Core_Property_String', '' );
		$this->assertTrue( Papi_Core_Property::factory( 'string' ) instanceof Papi_Core_Property );
	}

	public function test_factory_property() {
		$property = Papi_Core_Property::factory( [
			'type'  => 'string',
			'title' => 'Hello'
		] );
		$this->assertTrue( Papi_Core_Property::factory( $property ) instanceof Papi_Core_Property );
	}

	public function test_factory_slug() {
		$property = Papi_Core_Property::factory( [
			'type'  => 'string',
			'title' => 'Name',
			'slug'  => 'name'
		] );

		$this->assertSame( $property->get_option( 'slug' ), 'papi_name' );
	}

	public function test_factory_slug_empty() {
		$property = Papi_Core_Property::factory( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$this->assertSame( $property->get_option( 'slug' ), 'papi_name' );
	}

	public function test_factory_slug_false() {
		$property = Papi_Core_Property::factory( [
			'type'  => 'string',
			'title' => 'Name',
			'slug'  => false
		] );

		$slug = unpapify( $property->get_option( 'slug' ) );
		$slug = str_replace( '_', '', $slug );

		$this->assertRegExp( '/^[a-f0-9]{32}$/', $slug );
	}

	public function test_format_value() {
		$property = Papi_Core_Property::factory( [
			'type'  => 'string',
			'title' => 'Hello'
		] );

		$actual = $property->format_value( 'Fredrik', '', 0 );

		$this->assertSame( 'Fredrik', $actual );
	}

	public function test_get_child_property_fail() {
		$property = Papi_Core_Property::factory( [
			'type'  => 'string',
			'title' => 'Hello'
		] );

		$this->assertNull( $property->get_child_property( 'hello' ) );
	}

	public function test_get_child_property_success() {
		$property = Papi_Core_Property::factory( [
			'type'     => 'string',
			'title'    => 'Hello',
			'settings' => [
				'items' => [
					Papi_Core_Property::factory( [
						'type'  => 'string',
						'title' => 'Name'
					] )
				]
			]
		] );

		$child_property = $property->get_child_property( 'name' );

		$this->assertInstanceOf( 'Papi_Core_Property', $child_property );
		$this->assertSame( 'string', $child_property->type );
		$this->assertSame( 'Name', $child_property->title );
	}

	public function test_get_convert_type() {
		$property = Papi_Core_Property::factory();

		$this->assertSame( 'string', $property->get_convert_type() );
	}

	public function test_get_default_settings() {
		$property = Papi_Core_Property::factory();

		$this->assertSame( [], $property->get_default_settings() );
	}

	public function test_get_default_value_fail() {
		$property = Papi_Core_Property::factory( [
			'type'  => 'string',
			'title' => 'Hello'
		] );

		$this->assertEmpty( $property->get_value() );

		$property = Papi_Core_Property::factory( [
			'default' => 'Hello',
			'title'   => 'Hello',
		] );

		$this->assertSame( 'Hello', $property->get_value() );
	}

	public function test_get_default_value_success() {
		$_GET['post'] = 0;

		$property = Papi_Core_Property::factory( [
			'default' => 'Hello',
			'type'    => 'string',
			'title'   => 'Hello'
		] );

		$this->assertSame( 'Hello', $property->get_value() );

		unset( $_GET['post'] );
	}

	public function test_get_meta_type() {
		$property = new Papi_Core_Property();
		$this->assertSame( 'post', $property->get_meta_type() );
	}

	public function test_get_option() {
		$property = new Papi_Core_Property();

		$property->set_option( 'title', 'Name' );

		$this->assertSame( 'Name', $property->title );
		$this->assertSame( 'Name', $property->get_option( 'title' ) );

		$property = Papi_Core_Property::factory( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$this->assertNull( $property->fake );
		$this->assertNull( $property->get_option( 'fake' ) );
		$this->assertSame( 'Name', $property->title );
		$this->assertSame( 'Name', $property->get_option( 'title' ) );
		$this->assertSame( 1000, $property->sort_order );
		$this->assertSame( 1000, $property->get_option( 'sort_order' ) );

		$property->title = 'Link';

		$this->assertSame( 'Link', $property->title );

		$settings = $property->get_option( 'settings' );
		$this->assertTrue( is_object( $settings ) );
	}

	public function test_get_options() {
		$property = Papi_Core_Property::factory( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$options = $property->get_options();

		$this->assertSame( 'Name', $options->title );
	}

	public function test_get_store() {
		$property = new Papi_Core_Property();
		$this->assertTrue( $property->get_store() instanceof Papi_Post_Store );
	}

	public function test_get_post_id() {
		$property = Papi_Core_Property::factory();

		$this->assertSame( $this->post_id, $property->get_post_id() );
	}

	public function test_get_setting() {
		$property = new Papi_Core_Property();
		$this->assertNull( $property->get_setting( null ) );
		$this->assertNull( $property->get_setting( 'length' ) );

		$property = Papi_Core_Property::factory( [
			'type'     => 'string',
			'settings' => [
				'length' => 50
			]
		] );

		$this->assertSame( 50, $property->get_setting( 'length' ) );
	}

	public function test_get_settings() {
		$property = Papi_Core_Property::factory( [
			'type'     => 'string',
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
		$this->assertNotEmpty( $settings->items );
	}

	public function test_get_slug() {
		$property = new Papi_Core_Property();

		$this->assertEmpty( $property->get_slug() );

		$property = Papi_Core_Property::factory( [
			'type'  => 'string',
			'slug'  => 'name',
			'value' => 'Fredrik'
		] );

		$this->assertSame( 'papi_name', $property->get_slug() );

		$this->assertSame( 'name', $property->get_slug( true ) );
	}

	public function test_get_value() {
		$this->assertNull( ( new Papi_Core_Property )->get_value() );
	}

	public function test_html_id() {
		$property = Papi_Core_Property::factory( [
			'type'  => 'string',
			'slug'  => 'name'
		] );

		$this->assertSame( '_papi_name', $property->html_id() );
		$this->assertSame( '_papi_name_suffix', $property->html_id( 'suffix' ) );
		$this->assertSame( '_papi_name_black', $property->html_id( 'Black' ) );
		$this->assertSame( '_papi_name_lank', $property->html_id( 'Länk' ) );
	}

	public function test_html_id_array() {
		$property = Papi_Core_Property::factory( [
			'type'  => 'string',
			'slug'  => 'name'
		] );

		$sub_property = Papi_Core_Property::factory( [
			'type' => 'number',
			'slug' => 'age'
		] );

		$this->assertSame( '_papi_name[age]', $property->html_id( $sub_property ) );
		$this->assertSame( '_papi_name[0][age]', $property->html_id( $sub_property, 0 ) );

		$sub_property = (object) [
			'type' => 'number',
			'slug' => 'age'
		];

		$this->assertSame( '_papi_name[age]', $property->html_id( $sub_property ) );
		$this->assertSame( '_papi_name[0][age]', $property->html_id( $sub_property, 0 ) );

		$sub_property = 'non array or object';

		$this->assertSame( '_papi_name_non_array_or_object', $property->html_id( $sub_property ) );
		$this->assertSame( '_papi_name_non_array_or_object', $property->html_id( $sub_property, 0 ) );

		$property = Papi_Core_Property::factory( [
			'type' => 'number',
			'slug' => 'sections[0][age]'
		] );

		$this->assertSame( '_papi_sections[0][age_sort_order]', $property->html_id( 'sort_order' ) );
	}

	public function test_html_name() {
		$property = Papi_Core_Property::factory( [
			'type'  => 'string',
			'slug'  => 'name'
		] );

		$this->assertSame( 'papi_name', $property->html_name() );
	}

	public function test_html_name_array() {
		$property = Papi_Core_Property::factory( [
			'type'  => 'string',
			'slug'  => 'name'
		] );

		$sub_property = Papi_Core_Property::factory( [
			'type' => 'number',
			'slug' => 'age'
		] );

		$this->assertSame( 'papi_name[age]', $property->html_name( $sub_property ) );
		$this->assertSame( 'papi_name[0][age]', $property->html_name( $sub_property, 0 ) );

		$sub_property = (object) [
			'type' => 'number',
			'slug' => 'age'
		];

		$this->assertSame( 'papi_name[age]', $property->html_name( $sub_property ) );
		$this->assertSame( 'papi_name[0][age]', $property->html_name( $sub_property, 0 ) );

		$sub_property = 'non array or object';

		$this->assertSame( 'papi_name', $property->html_name( $sub_property ) );
		$this->assertSame( 'papi_name[0]', $property->html_name( $sub_property, 0 ) );
	}

	public function test_load_value() {
		$property = Papi_Core_Property::factory( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$actual = $property->load_value( 'Fredrik', '', 0 );

		$this->assertSame( 'Fredrik', $actual );
	}

	public function test_layout() {
		$property = Papi_Core_Property::factory( [
			'type' => 'string',
			'slug' => 'name'
		] );

		$this->assertSame( 'horizontal', $property->get_option( 'layout' ) );
		$this->assertTrue( $property->get_option( 'sidebar' ) );

		$property = Papi_Core_Property::factory( [
			'layout' => 'vertical',
			'type'   => 'string',
			'slug'   => 'name'
		] );

		$this->assertSame( 'vertical', $property->get_option( 'layout' ) );
		$this->assertTrue( $property->get_option( 'sidebar' ) );
	}

	public function test_match_slug() {
		$property = Papi_Core_Property::factory( [
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
		$page_type2 = papi_get_entry_type_by_id( 'faq-page-type' );
		$property2  = $page_type2->get_property( 'question' );
		$this->assertFalse( $property2->disabled() );

		$_GET['post_type'] = 'page';
		$page_type2 = papi_get_entry_type_by_id( 'faq-page-type' );
		$property2  = $page_type2->get_property( 'question' );
		$this->assertTrue( $property2->disabled() );
		unset( $_GET['post_type'] );
	}

	public function test_set_option() {
		$property = Papi_Core_Property::factory( [
			'type'  => 'string',
			'title' => 'Hello'
		] );

		$property->set_option( 'title', 'Name' );

		$value = $property->get_option( 'title' );

		$this->assertSame( 'Name', $value );
	}

	public function test_set_options() {
		$property = Papi_Core_Property::factory( [
			'type'  => 'string',
			'title' => 'Hello'
		] );

		$property->set_options( [
			'title' => 'Name'
		] );

		$this->assertSame( 'Name', $property->get_option( 'title' ) );

		$property = Papi_Core_Property::factory( [
			'type' => 'string'
		] );

		$this->assertSame( 'papi_string', $property->get_option( 'slug' ) );

		$property = Papi_Core_Property::factory( [
			'type'        => 'string',
			'description' => 'test'
		] );

		$this->assertSame( 'test', $property->get_option( 'description' ) );

		$property = Papi_Core_Property::factory( [
			'type' => 'string',
			'desc' => 'test'
		] );

		$this->assertSame( 'test', $property->get_option( 'description' ) );
	}

	public function test_set_post_id() {
		$property = Papi_Core_Property::factory( [
			'type'  => 'string',
			'title' => 'Hello'
		] );

		$property->set_post_id( null );

		$this->assertNotEmpty( $property->get_post_id() );

		$post_id = $this->factory->post->create();
		$property->set_post_id( $post_id );

		$this->assertSame( $post_id, $property->get_post_id() );
	}

	public function test_set_settings() {
		$property = Papi_Core_Property::factory( [
			'type'     => 'string',
			'title'    => 'Hello',
			'settings' => []
		] );

		$this->assertFalse( $property->get_setting( 'allow_html' ) );

		$property->set_setting( 'allow_html', true );

		$this->assertTrue( $property->get_setting( 'allow_html' ) );
	}

	public function test_update_value() {
		$property = Papi_Core_Property::factory( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$actual = $property->update_value( 'Fredrik', '', 0 );

		$this->assertSame( 'Fredrik', $actual );
	}

	public function test_update_value_empty() {
		$property = Papi_Core_Property::factory( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$value = $property->update_value( [null], '', 0 );
		$this->assertNull( $value );

		$value1 = $property->load_value( $value, '', 0 );
		$this->assertNull( $value1 );
	}

	public function test_update_value_with_empty_values() {
		$property = Papi_Core_Property::factory( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$value = $property->update_value( ['', 75, 87], '', 0 );
		$this->assertTrue( is_string( $value ) );

		$value1 = $property->load_value( $value, '', 0 );
		$this->assertSame( [75, 87], $value1 );
	}

	public function test_update_value_with_key_value() {
		$property = Papi_Core_Property::factory( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$value = $property->update_value( ['name' => 'Fredrik'], '', 0 );
		$this->assertTrue( is_string( $value ) );

		$value1 = $property->load_value( $value, '', 0 );
		$this->assertSame( 'Fredrik', $value1->name );
	}

	public function test_value_serialize() {
		$property = Papi_Core_Property::factory( [
			'title' => 'Name'
		] );

		$value = $property->update_value( [1, 2, 3], '', 0 );
		$this->assertTrue( is_string( $value ) );

		$value1 = $property->load_value( $value, '', 0 );
		$this->assertSame( [1, 2, 3], $value1 );

		$value2 = $property->format_value( $value, '', 0 );
		$this->assertSame( [1, 2, 3], $value2 );
	}
}
