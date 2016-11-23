<?php

class Papi_REST_API_Settings {

	/**
	 * The post store class.
	 *
	 * @var Papi_Post_Store
	 */
	protected $entries;

	/**
	 * REST API Settings construct.
	 */
	public function __construct() {
		// Hook into the REST API.
		add_action( 'rest_api_init', [$this, 'register'] );
		add_filter( 'rest_pre_get_setting', [$this, 'pre_get_setting'] );
	}

	/**
	 * Register options properties.
	 */
	public function register() {
		// Fetch all options entries.
		$this->entries = papi_get_all_entry_types( [
			'types' => 'option'
		] );

		foreach ( $this->entries as $entry ) {
			foreach ( $entry->get_properties() as $property ) {
				$property->register( 'option' );
			}
		}
	}

	/**
	 * Setup request after callbacks filter so it's only runs on settings endpoint.
	 *
	 * When the filter is added the `rest_pre_get_setting` will be removed since it's
	 * not used to anything good.
	 *
	 * @param  mixed $value
	 *
	 * @return mixed
	 */
	public function pre_get_setting( $value ) {
		if ( ! has_filter( 'rest_request_after_callbacks', [$this, 'prepare_response'] ) ) {
			add_filter( 'rest_request_after_callbacks', [$this, 'prepare_response'] );
			remove_filter( 'rest_pre_get_setting', [$this, 'pre_get_setting'] );
		}

		return $value;
	}

	/**
	 * Get setting value for a property.
	 *
	 * @param  string $key
	 * @param  string $value
	 *
	 * @return mixed
	 */
	public function get_setting( $key, $value ) {
		$property = null;

		foreach ( (array) $this->entries as $entry ) {
			if ( $property = $entry->get_property( $key ) ) {
				break;
			}
		}

		if ( is_null( $property ) ) {
			return $value;
		}

		$value = papi_get_option( $key );

		return $property->rest_prepare_value( $value );
	}

	/**
	 * Prepare settings response.
	 *
	 * @param  WP_HTTP_Response $response
	 *
	 * @return array
	 */
	public function prepare_response( $response ) {
		$response = (array) $response;

		foreach ( $response as $key => $value ) {
			$setting = $this->get_setting( $key, $value );

			if ( $setting !== $value ) {
				$response[$key] = $setting;
			}
		}

		return $response;
	}
}

new Papi_REST_API_Settings;
