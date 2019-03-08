<?php

/**
 * @group rest-api
 */
class Papi_REST_API_Post_Test extends WP_UnitTestCase {

	/**
	 * @var Papi_REST_API_Post
	 */
	protected $class;

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
		$this->assertSame( 10, has_action( 'the_post', [$this->class, 'get_post'] ) );
		$this->assertSame( 10, has_action( 'rest_api_init', [$this->class, 'setup_fields'] ) );
	}

	public function test_get_page_type() {
		$this->assertEmpty( $this->class->get_page_type( [], 'page_type', null ) );

		$post_id = $this->factory->post->create( [
			'post_type' => 'page'
		] );

		$page_type = $this->class->get_page_type( ['ID' => $post_id], 'page_type', null );

		$this->assertSame( '', $page_type );
		update_post_meta( $post_id, papi_get_page_type_key(), 'simple-content-page-type' );

		$page_type = $this->class->get_page_type( ['ID' => $post_id], 'page_type', null );
		$this->assertSame( $page_type, get_post_meta( $post_id, papi_get_page_type_key(), true ) );
	}

	public function test_get_post() {
		$post_id = $this->factory->post->create( ['post_type' => 'page'] );
		$post = get_post( $post_id );
		$this->assertSame( $post, $this->class->get_post( $post ) );

		$post_id = $this->factory->post->create();
		$post = get_post( $post_id );
		update_post_meta( $post_id, papi_get_page_type_key(), 'simple-page-type' );

		$this->assertSame( $post, $this->class->get_post( $post ) );
		$this->assertSame( 10, has_filter( 'rest_prepare_' . $post->post_type, [$this->class, 'prepare_response'] ) );

		$page_type = papi_get_entry_type_by_meta_id( $post->ID );

		global $wp_meta_keys;

		if ( ! is_array( $wp_meta_keys ) ) {
			$this->markTestSkipped( '`$wp_meta_keys` is not a array' );
		}

		$meta_keys = $wp_meta_keys[$post->post_type];
		$meta_keys = count( $meta_keys ) === 1 ? array_shift( $meta_keys ) : $meta_keys;

		foreach ( $page_type->get_properties() as $property ) {
			$this->assertArrayHasKey( $property->get_slug( true ), $meta_keys );
		}
	}

	public function test_prepare_response() {
		$this->assertEmpty( $this->class->prepare_response( [] ) );

		$post_id = $this->factory->post->create();
		global $post;
		$post = get_post( $post_id );

		update_post_meta( $post_id, papi_get_page_type_key(), 'properties-page-type' );
		$this->assertSame( $post, $this->class->get_post( $post ) );

		update_post_meta( $post_id, 'post_test', $post_id );
		$response = new \stdClass;
		$response->data = [
			'meta' => [
				'post_test' => $post_id
			]
		];

		$response = $this->class->prepare_response( $response );
		$this->assertSame( $post->ID, $response->data['meta']['post_test']->ID );
	}

	public function test_setup_fields() {
		global $wp_rest_additional_fields;

		$this->class->setup_fields();

		if ( ! is_array( $wp_rest_additional_fields ) ) {
			$this->markTestSkipped( '`register_rest_field` is only supported in WordPress 4.7 and later' );
		}

		$this->assertArrayHasKey( 'page_type', $wp_rest_additional_fields['page'] );
	}
}
