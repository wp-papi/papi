<?php

class Papi_Lib_Core_Property_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		global $post;

		$this->post_id = $this->factory->post->create();

		$post = get_post( $this->post_id );
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->post_id );
	}

	public function test_papi_delete_property_meta_value() {
		$this->assertFalse( papi_delete_property_meta_value( $this->post_id, 'random223-page-type' ) );
		$this->assertFalse( papi_delete_property_meta_value( $this->post_id, 'random223-page-type', Papi_Option_Page::TYPE ) );

		update_post_meta( $this->post_id, 'random223-page-type', 'post' );
		update_option( 'random223-page-type', 'option' );
		$this->assertTrue( papi_delete_property_meta_value( $this->post_id, 'random223-page-type' ) );
		$this->assertTrue( papi_delete_property_meta_value( $this->post_id, 'random223-page-type', Papi_Option_Page::TYPE ) );

		update_post_meta( $this->post_id, 'random223-page-type', 'post' );
		update_option( 'random223-page-type', 'option' );
		$this->assertTrue( papi_delete_property_meta_value( $this->post_id, 'papi_random223-page-type' ) );
		$this->assertTrue( papi_delete_property_meta_value( $this->post_id, 'papi-random223-page-type', Papi_Option_Page::TYPE ) );
	}

	public function test_papi_from_property_array_slugs() {
		$actual = papi_from_property_array_slugs( [
			'repeater' => 1,
			'repeater_0_image' => 1,
			'_repeater_0_image_property' => 'image'
		], 'repeater' );

		$expected = [
			[
				'image' => 1,
				'_image_property' => 'image'
			]
		];

		$this->assertSame( $expected, $actual );

		$this->assertEmpty( papi_from_property_array_slugs( [], 'custom_slug' ) );
	}

	public function test_papi_is_property() {
		$this->assertTrue( papi_is_property( new Papi_Property() ) );
		$this->assertTrue( papi_is_property( new Papi_Property_String() ) );
		$this->assertFalse( papi_is_property( (object) [] ) );
		$this->assertFalse( papi_is_property( [] ) );
		$this->assertFalse( papi_is_property( null ) );
		$this->assertFalse( papi_is_property( 1 ) );
		$this->assertFalse( papi_is_property( true ) );
		$this->assertFalse( papi_is_property( false ) );
		$this->assertFalse( papi_is_property( '' ) );
	}

	public function test_papi_get_property_meta_value() {
		$this->assertNull( papi_get_property_meta_value( $this->post_id, 'random322-page-type' ) );
		$this->assertNull( papi_get_property_meta_value( $this->post_id, 'random322-page-type', Papi_Option_Page::TYPE ) );
		update_post_meta( $this->post_id, 'random322-page-type', 'post' );
		update_option( 'random322-page-type', 'option' );
		$this->assertSame( 'post', papi_get_property_meta_value( $this->post_id, 'random322-page-type' ) );
		$this->assertSame( 'option', papi_get_property_meta_value( $this->post_id, 'random322-page-type', Papi_Option_Page::TYPE ) );
	}

	public function test_papi_get_options_and_properties() {
		$simple_box = PAPI_FIXTURE_DIR . '/page-types/boxes/simple.php';

		$options = papi_get_options_and_properties( $simple_box, [
			'test' => 'test'
		] );

		$this->assertSame( 'Simple', $options[0]['title'] );
		$this->assertSame( 'test', $options[0]['test'] );
		$this->assertSame( 'Name', $options[1][0]->title );
		$this->assertSame( 'string', $options[1][0]->type );

		$options = papi_get_options_and_properties( 'hello' );
		$this->assertSame( 'hello', $options[0]['title'] );

		$options = papi_get_options_and_properties( [
			'title' => 'Simple',
			papi_property( [
				'type'  => 'string',
				'title' => 'Name'
			] )
		], [], false );

		$this->assertSame( 'Simple', $options[0]['title'] );
		$this->assertSame( 'Name', $options[1][0]->title );
		$this->assertSame( 'string', $options[1][0]->type );

		$options = papi_get_options_and_properties( [
			'title' => 'Simple'
		] );

		$this->assertSame( 'Simple', $options[0]['title'] );

		$options = papi_get_options_and_properties( [] );

		$this->assertEmpty( $options[0]['title'] );

		$options = papi_get_options_and_properties( [ papi_property( [
			'type'  => 'string',
			'title' => 'Name'
		] ) ] );

		$this->assertSame( 'Name', $options[0]['title'] );
		$this->assertSame( 'Name', $options[1][0]->title );
		$this->assertSame( 'string', $options[1][0]->type );

		$options = papi_get_options_and_properties( [ papi_property( [
			'type'     => 'string',
			'title'    => 'Name',
			'sidebar'  => false,
			'required' => true
		] ) ] );

		$this->assertSame( 'Name', $options[0]['title'] );
		$this->assertSame( 'Name', $options[1][0]->title );
		$this->assertSame( 'string', $options[1][0]->type );
		$this->assertTrue( $options[0]['_required'] );

		$options = papi_get_options_and_properties( [ (object) [
			'options' => [
				'title' => 'Name'
			]
		] ] );

		$this->assertSame( 'Name', $options[0]['title'] );
	}

	public function test_papi_get_property_class_name() {
		$this->assertSame( 'Papi_Property_String', papi_get_property_class_name( 'PropertyString' ) );
		$this->assertSame( 'Papi_Property_String', papi_get_property_class_name( 'string' ) );
		$this->assertSame( 'Papi_Property_Fake', papi_get_property_class_name( 'fake' ) );
		$this->assertSame( 'Papi_Property_Test_Form_1', papi_get_property_class_name( 'test-form-1' ) );
		$this->assertNull( papi_get_property_class_name( null ) );
		$this->assertNull( papi_get_property_class_name( false ) );
		$this->assertNull( papi_get_property_class_name( true ) );
		$this->assertNull( papi_get_property_class_name( [] ) );
		$this->assertNull( papi_get_property_class_name( new stdClass() ) );
		$this->assertNull( papi_get_property_class_name( 1 ) );
	}

	public function test_papi_get_property_type() {
		$this->assertTrue( papi_get_property_type( 'string' ) instanceof Papi_Property_String );
		$this->assertFalse( papi_get_property_type( null ) instanceof Papi_Property_String );
		$this->assertFalse( papi_get_property_type( 'fake' ) instanceof Papi_Property_String );
		$this->assertFalse( papi_get_property_type( 1 ) instanceof Papi_Property_String );
		$this->assertFalse( papi_get_property_type( true ) instanceof Papi_Property_String );
		$this->assertFalse( papi_get_property_type( false ) instanceof Papi_Property_String );
		$this->assertFalse( papi_get_property_type( [] ) instanceof Papi_Property_String );
		$this->assertFalse( papi_get_property_type( new stdClass() ) instanceof Papi_Property_String );
		$options = Papi_Core_Property::create( [
			'type' => 'string'
		] )->get_options();
		$this->assertTrue( papi_get_property_type( $options ) instanceof Papi_Property_String );
	}

	public function test_papi_get_property_type_custom() {
		add_action( 'papi_include_properties', function() {
			require_once PAPI_FIXTURE_DIR . '/properties/class-papi-property-kvack.php';
		} );

		do_action( 'papi_include_properties' );

		$this->assertTrue( papi_get_property_type( 'kvack' ) instanceof Papi_Property_Kvack );
	}

	public function test_papi_get_property_type_key() {
		$this->assertSame( 'image_property', papi_get_property_type_key( 'image' ) );
		$this->assertSame( '_property', papi_get_property_type_key( null ) );
		$this->assertSame( '_property', papi_get_property_type_key( false ) );
		$this->assertSame( '_property', papi_get_property_type_key( true ) );
		$this->assertSame( '_property', papi_get_property_type_key( [] ) );
		$this->assertSame( '_property', papi_get_property_type_key( 1 ) );
		$this->assertSame( '_property', papi_get_property_type_key( new stdClass() ) );
		$this->assertSame( 'my_slug[0][key_property]', papi_get_property_type_key( 'my_slug[0][key]' ) );
	}

	public function test_papi_get_property_type_f() {
		$this->assertSame( '_image_property', papi_get_property_type_key_f( 'image' ) );
		$this->assertSame( '_property', papi_get_property_type_key_f( null ) );
		$this->assertSame( '_property', papi_get_property_type_key_f( true ) );
		$this->assertSame( '_property', papi_get_property_type_key_f( false ) );
		$this->assertSame( '_property', papi_get_property_type_key_f( 1 ) );
		$this->assertSame( '_property', papi_get_property_type_key_f( [] ) );
		$this->assertSame( '_property', papi_get_property_type_key_f( new stdClass() ) );
	}

	public function test_papi_is_property_type_key() {
		$this->assertTrue( papi_is_property_type_key( 'image_property' ) );
		$this->assertTrue( papi_is_property_type_key( '_image_property' ) );
		$this->assertFalse( papi_is_property_type_key( 'kvack' ) );
		$this->assertFalse( papi_is_property_type_key( 1 ) );
		$this->assertFalse( papi_is_property_type_key( false ) );
		$this->assertFalse( papi_is_property_type_key( true ) );
	}

	public function test_papi_render_property() {
		$property = papi_property( [
			'title' => 'Name'
		] );
		$this->assertEmpty( papi_render_property( $property ) );

		$property = papi_property( [
			'type'  => 'fake',
			'title' => 'Name'
		] );
		$this->assertEmpty( papi_render_property( $property ) );

		$user_id = $this->factory->user->create( [ 'role' => 'administrator' ] );
		wp_set_current_user( $user_id );

		$_GET = [ 'lang' => 'se' ];
		$property = papi_property( [
			'type'  => 'string',
			'title' => 'Name',
			'lang'  => 'dk',
			'capabilities' => 'administrator'
		] );
		$this->assertEmpty( papi_render_property( $property ) );
		$this->expectOutputRegex( '//' );
		unset( $_GET );

		$user_id = $this->factory->user->create( [ 'role' => 'read' ] );
		wp_set_current_user( $user_id );
		$property = papi_property( [
			'type'  => 'string',
			'title' => 'Name',
			'capabilities' => 'administrator'
		] );
		$this->assertEmpty( papi_render_property( $property ) );
		$this->expectOutputRegex( '//' );
		wp_set_current_user( 0 );
	}

	public function test_papi_render_required_property() {
		$property = papi_property( [
			'slug'     => 'required_name',
			'type'     => 'string',
			'title'    => 'Name',
			'required' => true
		] );
		papi_render_property( $property );
		$this->expectOutputRegex( '/class\=\"papi\-rq\"/' );
	}

	public function test_papi_render_properties() {
		$tab = papi_tab( 'Content', [] );
		papi_render_properties( [ $tab ] );
		$this->expectOutputRegex( '/class\=\"papi\-tabs\-wrapper\"/' );

		$property = papi_property( [
			'type'  => 'string',
			'title' => 'Name'
		] );
		papi_render_properties( [ $property ] );
		$this->expectOutputRegex( '/table\s*class\=\"papi\-table\"/' );
	}

	public function test_papi_require_text() {
		$this->assertEmpty( papi_require_text( null ) );
		$property = papi_property( [
			'type'     => 'string',
			'title'    => 'Name',
			'required' => true
		] );
		$this->assertSame( '(required field)', papi_require_text( $property ) );
	}

	public function test_papi_required_html() {
		$this->assertEmpty( papi_required_html( null ) );
		$property = papi_property( [
			'type'     => 'string',
			'title'    => 'Name',
			'required' => true
		] );
		$this->assertRegExp( '/class\=\"papi\-rq\"/', papi_required_html( $property ) );
	}

	public function test_papi_populate_properties() {
		$this->assertEmpty( papi_populate_properties( [] ) );
		$this->assertEmpty( papi_populate_properties( null ) );
		$this->assertEmpty( papi_populate_properties( true ) );
		$this->assertEmpty( papi_populate_properties( false ) );
		$this->assertEmpty( papi_populate_properties( 1 ) );

		$actual = papi_populate_properties( new stdClass() );
		$this->assertTrue( is_object( $actual[0] ) );

		$actual = papi_populate_properties( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$this->assertSame( 'Name', $actual[0]->title );
		$this->assertSame( 'string', $actual[0]->type );

		$this->assertSame( [ $actual[0] ], papi_populate_properties( $actual[0] ) );

		$actual = papi_populate_properties( [
			[
				'type'  => 'string',
				'title' => 'Name 1'
			],
			[
				'type'  => 'string',
				'title' => 'Name 2'
			]
		] );

		$this->assertSame( 'Name 1', $actual[0]->title );
		$this->assertSame( 'string', $actual[0]->type );

		$this->assertSame( 'Name 2', $actual[1]->title );
		$this->assertSame( 'string', $actual[1]->type );

		$tab = papi_tab( 'Content', [] );
		$actual = papi_populate_properties( [$tab] );

		$this->assertSame( 'Content', $actual[0]->title );
		$this->assertEmpty( $actual[0]->properties );

		$actual = papi_populate_properties( [
			[
				'type'  	 => 'string',
				'title' 	 => 'Name 1',
				'sort_order' => 1
			],
			[
				'type'  	 => 'string',
				'title' 	 => 'Name 3'
			],
			[
				'type'  	 => 'string',
				'title' 	 => 'Name 2',
				'sort_order' => 0
			]
		] );

		$this->assertSame( 'Name 2', $actual[0]->title );
		$this->assertSame( 'string', $actual[0]->type );

		$this->assertSame( 'Name 1', $actual[1]->title );
		$this->assertSame( 'string', $actual[1]->type );

		$this->assertSame( 'Name 3', $actual[2]->title );
		$this->assertSame( 'string', $actual[2]->type );
	}

	public function test_papi_property() {
		$actual = papi_property( [
			'type'  => 'string',
			'title' => 'Name'
		] );

		$this->assertSame( 'Name', $actual->title );
		$this->assertSame( 'string', $actual->type );

		$this->assertNull( papi_property( [] ) );
		$this->assertNull( papi_property( new stdClass() ) );
		$this->assertNull( papi_property( null ) );
		$this->assertNull( papi_property( true ) );
		$this->assertNull( papi_property( false ) );
		$this->assertNull( papi_property( 1 ) );
	}

	public function test_papi_property_template() {
		$actual = papi_property( PAPI_FIXTURE_DIR . '/properties/simple.php' );

		$this->assertSame( 'Name', $actual->title );
		$this->assertSame( 'string', $actual->type );
	}

	public function test_papi_property_template_in_page_type() {
		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$page_type  = papi_get_page_type_by_id( 'name-page-type' );
		$boxes      = $page_type->get_boxes();
		$properties = $boxes[0]->properties;
		$this->assertSame( 'papi_my_name_is', $properties[0]->get_slug() );
	}

	public function test_papi_update_property_meta_value() {
		$this->assertTrue( papi_update_property_meta_value( [
			'post_id'       => $this->post_id,
			'slug'          => 'name',
			'value'         => 'Fredrik'
		] ) );

		$this->assertFalse( papi_update_property_meta_value( [
			'post_id'       => $this->post_id,
			'slug'          => 'name',
			'value'         => 'Fredrik'
		] ) );

		$this->assertSame( 'Fredrik', get_post_meta( $this->post_id, 'name', true ) );

		$this->assertTrue( papi_update_property_meta_value( [
			'post_id'       => $this->post_id,
			'slug'          => 'name',
			'value'         => ''
		] ) );

		$this->assertEmpty( get_post_meta( $this->post_id, 'name', true ) );

		$this->assertTrue( papi_update_property_meta_value( [
			'post_id'       => $this->post_id,
			'slug'          => 'what',
			'value'         => [
				'firstname' => 'Fredrik'
			]
		] ) );

		$this->assertSame( 'Fredrik', get_post_meta( $this->post_id, 'firstname', true ) );

		$this->assertTrue( papi_update_property_meta_value( [
			'post_id'       => $this->post_id,
			'slug'          => 'what',
			'value'         => [
				'Fredrik'
			]
		] ) );

		$this->assertSame( ['Fredrik'], get_post_meta( $this->post_id, 'what', true ) );
	}

	public function test_papi_update_property_meta_value_option() {
		global $current_screen;

		$current_screen = WP_Screen::get( 'admin_init' );

		$old_request_uri = $_SERVER['REQUEST_URI'];

		$_SERVER['REQUEST_URI'] = 'http://site.com/wp-admin/options-general.php?page=papi%2Foptions%2Fheader-option-type';

		papi_update_property_meta_value( [
			'slug'  => 'name',
			'value' => 'Fredrik'
		] );

		$this->assertSame( 'Fredrik', get_option( 'name' ) );

		papi_update_property_meta_value( [
			'slug'  => 'name',
			'value' => ''
		] );

		$this->assertNull( get_option( 'name', null ) );

		papi_update_property_meta_value( [
			'slug'          => 'name',
			'value'         => [
				'firstname' => 'Fredrik'
			]
		] );

		$this->assertSame( 'Fredrik', get_option( 'firstname' ) );

		papi_update_property_meta_value( [
			'slug'          => 'name',
			'value'         => [
				'Fredrik'
			]
		] );

		$this->assertSame( ['Fredrik'], get_option( 'name' ) );

		$_SERVER['REQUEST_URI'] = $old_request_uri;

		$current_screen = null;
	}

	public function test_papi_to_property_array_slugs() {
		$actual = papi_to_property_array_slugs( [
			[
				'image' => 1,
				'image_property' => 'image',
				0 => false
			]
		], 'repeater' );

		$expected = [
			'repeater' => 1,
			'repeater_0_image' => 1,
			'_repeater_0_image_property' => 'image'
		];

		$this->assertEquals( $expected, $actual );

		$actual = papi_to_property_array_slugs( [ 1, '', true, false ], 'repeater' );

		$expected = [
			'repeater' => 0
		];

		$this->assertEquals( $expected, $actual );
	}
}
