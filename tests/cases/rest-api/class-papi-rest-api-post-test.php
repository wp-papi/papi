<?php

/**
 * @group rest-api
 */
class Papi_REST_API_Post_Test extends WP_UnitTestCase {

    public function setUp() {
        parent::setUp();

		add_filter( 'papi/settings/directories', function () {
			return PAPI_FIXTURE_DIR . '/page-types';
		} );
        
        $this->class = new Papi_REST_API_Post;
    }

    public function tearDown() {
        parent::tearDown();

        unset( $this->class );
    }

	public function test_actions() {
		$this->assertSame( 10, has_action( 'rest_the_post', [$this->class, 'get_post'] ) );
	}

    public function test_get_post() {
        $post_id = $this->factory->post->create();
        $post = get_post( $post_id );
        $this->assertSame( $post, $this->class->get_post( $post ) );

        update_post_meta( $post_id, papi_get_page_type_key(), 'simple-page-type' );
        
        $this->assertSame( $post, $this->class->get_post( $post ) );
        $this->assertSame( 10, has_filter( 'rest_prepare_' . $post->post_type, [$this->class, 'prepare_response'] ) );

        $page_type = papi_get_entry_type_by_meta_id( $post->ID );

        global $wp_meta_keys;

        foreach ( $page_type->get_properties() as $property ) {
            $this->assertArrayHasKey( $property->get_slug( true ), $wp_meta_keys[$post->post_type] );
        }
    }

    public function test_prepare_response() {
        $this->assertEmpty( $this->class->prepare_response( [] ) );
        
        $post_id = $this->factory->post->create();
        $post = get_post( $post_id );
        
        update_post_meta( $post_id, papi_get_page_type_key(), 'simple-page-type' );
        $this->assertSame( $post, $this->class->get_post( $post ) );

        update_post_meta( $post_id, 'name', 'Fredrik' );
        $response = [
            'meta' => (object) [
                'name' => get_post_meta( $post_id, 'name', true )
            ]
        ];
        
        $response = $this->class->prepare_response( $response );
        $this->assertSame( 'Fredrik', $response['meta']->name );
    }
}