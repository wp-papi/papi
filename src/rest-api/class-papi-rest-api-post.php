<?php

class Papi_REST_API_Post {

	/**
	 * The post store class.
	 *
	 * @var Papi_Post_Store
	 */
	protected $store;

	/**
	 * REST API Post construct.
	 */
	public function __construct() {
		add_filter( 'the_post', [$this, 'get_post'] );
		add_action( 'rest_api_init', [$this, 'setup_fields'] );
	}

	/**
	 * Get page type.
	 *
	 * @param  array           $data
	 * @param  string          $field_name
	 * @param  WP_REST_Request $request
	 *
	 * @return array
	 */
	public function get_page_type( array $data, $field_name, $request ) {
		return papi_get_page_type_id( $data['ID'] ) ?: '';
	}

	/**
	 * Filter the post.
	 *
	 * Register all properties with `register_meta` and prepare response filter.
	 *
	 * @param  WP_Post $post
	 *
	 * @return WP_Post
	 */
	public function get_post( WP_Post $post ) {
		if ( ! ( $page_type = papi_get_entry_type_by_meta_id( $post->ID ) ) ) {
			return $post;
		}

		// Register all properties fields with register meta.
		foreach ( $page_type->get_properties() as $property ) {
			$property->register();
		}

		// Get the post store for a page type.
		$this->store = papi_get_meta_store( $post->ID );

		// Add filter to prepare the response for a post type.
		add_filter( 'rest_prepare_' . $post->post_type, [$this, 'prepare_response'] );

		return $post;
	}

	/**
	 * Prepare response.
	 *
	 * @param  WP_REST_Response $response
	 *
	 * @return WP_REST_Response
	 */
	public function prepare_response( $response ) {
		if ( ! isset( $response->data['meta'] ) ) {
			return $response;
		}

		foreach ( $response->data['meta'] as $key => $value ) {
			$response->data['meta'][$key] = $this->prepare_property_value( $key, $value );
		}

		return $response;
	}

	/**
	 * Prepare property value.
	 *
	 * @param  string $slug
	 * @param  mixed  $default
	 *
	 * @return mixed
	 */
	protected function prepare_property_value( $slug, $default = null ) {
		// Set property meta value instead of loading it since it's loaded.
		$this->store->set_property_meta_value( $slug, $default );

		// Get value from store.
		$value = $this->store->get_value( $id, $slug, 'post', $default );

		// Prepare the value for REST API if property is found.
		if ( $property = $this->store->get_property( $slug ) ) {
			return $property->rest_prepare_value( $value );
		}

		return $default;
	}

	/**
	 * Setup REST API fields.
	 */
	public function setup_fields() {
		if ( ! function_exists( 'register_rest_field' ) ) {
			return;
		}

		$post_types = papi_get_post_types();

		foreach ( $post_types as $post_type ) {
			register_rest_field( $post_type, 'page_type', [
				'get_callback' => [$this, 'get_page_type']
			] );
		}
	}
}

new Papi_REST_API_Post;
