<?php

/**
 * @group admin
 */
class Papi_Admin_Page_Type_Switcher_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		add_filter( 'papi/settings/directories', function () {
			return PAPI_FIXTURE_DIR . '/page-types';
		} );
	}

	public function test_admin_init() {
		$switcher = new Papi_Admin_Page_Type_Switcher;
		$switcher->admin_init();
		$this->assertSame( 10, has_action( 'post_submitbox_misc_actions', [$switcher, 'metabox'] ) );
		$this->assertSame( 10, has_action( 'save_post', [$switcher, 'save_post'] ) );
	}

	public function test_metabox_1() {
		$switcher = new Papi_Admin_Page_Type_Switcher;
		$switcher->metabox();
		$this->expectOutputRegex( '//' );
	}

	public function test_metabox_2() {
		$switcher = new Papi_Admin_Page_Type_Switcher;

		$_GET = [
			'post_type' => 'page',
			'page_type' => 'properties-page-type'
		];

		$switcher->metabox();
		$this->expectOutputRegex( '/\<div/' );
	}

	public function test_save_post() {
		$switcher = new Papi_Admin_Page_Type_Switcher;
		$post_id  = $this->factory->post->create( ['post_type' => 'page'] );
		$post     = get_post( $post_id );
		$_POST    = [];

		// Bad values.
		$this->assertFalse( $switcher->save_post( 0, null ) );
		$this->assertFalse( $switcher->save_post( 0, null ) );

		// Nonce check.
		$this->assertFalse( $switcher->save_post( $post_id, $post ) );

		$_POST['papi_meta_nonce'] = wp_create_nonce( 'papi_save_data' );

		// Empty post values.
		$this->assertFalse( $switcher->save_post( $post_id, $post ) );

		$_POST[papi_get_page_type_key()] = 'properties-page-type';
		$_POST[papi_get_page_type_key( 'switch' )] = 'properties-page-type';

		// Same post type ids.
		$this->assertFalse( $switcher->save_post( $post_id, $post ) );

		$_POST[papi_get_page_type_key()] = 'properties-page-type';
		$_POST[papi_get_page_type_key( 'switch' )] = 'no-existing-page-type';

		// Bad page type and post type objects.
		$this->assertFalse( $switcher->save_post( $post_id, $post ) );

		$_POST[papi_get_page_type_key( 'switch' )] = 'post-page-type';
		$_POST['post_type'] = 'page';

		// Bad post type.
		$this->assertFalse( $switcher->save_post( $post_id, $post ) );

		$_POST[papi_get_page_type_key( 'switch' )] = 'display-not-page-type';

		// Bad capabilities.
		$this->assertFalse( $switcher->save_post( $post_id, $post ) );

		$_POST[papi_get_page_type_key( 'switch' )] = 'simple-page-type';

		// Bad capabilities.
		$this->assertFalse( $switcher->save_post( $post_id, $post ) );

		update_post_meta( $post_id, papi_get_page_type_key(), 'properties-page-type' );
		update_post_meta( $post_id, 'string_test', 'Fredrik' );
		update_post_meta( $post_id, 'hidden_test', 'Fredrik' );

		$this->assertSame( 'Fredrik', papi_get_field( $post_id, 'string_test' ) );
		$this->assertSame( 'Fredrik', papi_get_field( $post_id, 'hidden_test' ) );

		$user_id = $this->factory->user->create( [ 'role' => 'administrator' ] );
		wp_set_current_user( $user_id );

		// Create new nonce because of new user.
		$_POST['papi_meta_nonce'] = wp_create_nonce( 'papi_save_data' );
		$_POST[papi_get_page_type_key( 'switch' )] = 'simple-page-type';

		// Success!
		$this->assertTrue( $switcher->save_post( $post_id, $post ) );

		$this->assertSame( 'Fredrik', papi_get_field( $post_id, 'string_test' ) );
		$this->assertNull( papi_get_field( $post_id, 'hidden_test' ) );

		wp_set_current_user( 0 );
	}

	public function test_save_post_revision() {
		$switcher = new Papi_Admin_Page_Type_Switcher;
		$post_id  = $this->factory->post->create( ['post_type' => 'revision'] );
		$post     = get_post( $post_id );
		$_POST    = [];
		$user_id  = $this->factory->user->create( [ 'role' => 'administrator' ] );
		wp_set_current_user( $user_id );

		$_POST['post_type'] = 'page';
		$_POST[papi_get_page_type_key( 'switch' )] = 'simple-page-type';
		$_POST[papi_get_page_type_key()] = 'properties-page-type';
		$_POST['papi_meta_nonce'] = wp_create_nonce( 'papi_save_data' );

		$this->assertFalse( $switcher->save_post( $post_id, $post ) );

		$post_id  = $this->factory->post->create( ['post_type' => 'revision', 'post_parent' => $post_id] );
		$post     = get_post( $post_id );

		$this->assertFalse( $switcher->save_post( $post_id, $post ) );
	}

	public function test_save_post_autosave() {
		$switcher = new Papi_Admin_Page_Type_Switcher;
		$post_id  = $this->factory->post->create( ['post_type' => 'page'] );
		$post     = get_post( $post_id );
		$_POST    = [];
		$user_id  = $this->factory->user->create( [ 'role' => 'administrator' ] );
		wp_set_current_user( $user_id );

		$_POST['post_type'] = 'page';
		$_POST[papi_get_page_type_key( 'switch' )] = 'simple-page-type';
		$_POST[papi_get_page_type_key()] = 'properties-page-type';
		$_POST['papi_meta_nonce'] = wp_create_nonce( 'papi_save_data' );

		$post_id  = $this->factory->post->create( ['post_type' => 'revision', 'post_parent' => $post_id, 'post_name' => $post_id . '-autosave'] );
		$post     = get_post( $post_id );

		$this->assertFalse( $switcher->save_post( $post_id, $post ) );
	}
}
