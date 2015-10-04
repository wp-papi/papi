<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Unit tests covering `Papi_Attachment_Type` class.
 *
 * @package Papi
 */
class Papi_Attachment_Type_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		$_GET = [];

		tests_add_filter( 'papi/settings/directories', function () {
			return [1,  PAPI_FIXTURE_DIR . '/page-types'];
		} );

		$this->attachment_type = papi_get_page_type_by_id( 'others/attachment-type' );
	}

	public function tearDown() {
		parent::tearDown();
		unset(
			$_GET,
			$this->attachment_type
		);
	}

	public function test_filters() {
		$attachment_type = new Papi_Attachment_Type();
		$attachment_type->boot();
		$this->assertGreaterThan( 0, has_filter( 'attachment_fields_to_edit', [$attachment_type, 'edit_attachment'] ) );
		$this->assertGreaterThan( 0, has_filter( 'attachment_fields_to_save', [$attachment_type, 'save_attachment'] ) );
	}

    public function test_post_type() {
        $this->assertSame( 'attachment', $this->attachment_type->post_type[0] );
    }

    public function test_get_post_type() {
        $this->assertSame( 'attachment', $this->attachment_type->get_post_type() );
    }

    public function test_meta() {
        $this->assertSame( 'Attachment', $this->attachment_type->name );
    }

    public function test_edit_attachment() {
        $post_id = $this->factory->post->create();
        $post = get_post( $post_id );
        $form_fields = $this->attachment_type->edit_attachment( [], $post );

        $this->assertTrue( isset( $form_fields['papi_name'] ) );
        $this->assertTrue( isset( $form_fields['papi_post'] ) );
        $this->assertTrue( isset( $form_fields['papi_text'] ) );
        $this->assertTrue( isset( $form_fields['papi_meta_nonce'] ) );
    }

    public function test_save_attachment() {
        $post_id = $this->factory->post->create();
        $post = (array) get_post( $post_id );
        $property = $this->attachment_type->get_property( 'name' );

		$_POST = papi_test_create_property_post_data( [
			'slug'  => $property->get_option( 'slug' ),
			'type'  => $property,
			'value' => 'Fredrik'
		], $_POST );
        $_POST['action'] = 'save-attachment-compat';

        $user_id = $this->factory->user->create( [ 'role' => 'administrator' ] );
        wp_set_current_user( $user_id );

        $_POST['papi_meta_nonce'] = wp_create_nonce( 'papi_save_data' );
        $_POST['id'] = $post_id;

        $this->assertSame( $post, $this->attachment_type->save_attachment( $post, null ) );
        $this->assertSame( 'Fredrik', papi_get_field( $post_id, 'name' ) );

    }
}
