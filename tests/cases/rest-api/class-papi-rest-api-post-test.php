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
		$post_id = $this->factory->post->create();
		$post = get_post( $post_id );
		$this->assertSame( $post, $this->class->get_post( $post ) );

		update_post_meta( $post_id, papi_get_page_type_key(), 'simple-page-type' );

		$this->assertSame( $post, $this->class->get_post( $post ) );
		$this->assertSame( 10, has_filter( 'rest_prepare_' . $post->post_type, [$this->class, 'prepare_response'] ) );

		$page_type = papi_get_entry_type_by_meta_id( $post->ID );

		global $wp_meta_keys;

		if ( ! is_array( $wp_meta_keys ) ) {
			$this->markTestSkipped( '`$wp_meta_keys` is not a array' );
		}

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
			'meta' => [
				'name' => get_post_meta( $post_id, 'name', true )
			]
		];

		$response = $this->class->prepare_response( $response );
		$this->assertSame( 'Fredrik', $response['meta']['name'] );
	}
}
