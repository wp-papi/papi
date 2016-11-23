<?php

final class Papi_REST_API {

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->load_files();
		$this->setup_actions();
	}

	/**
	 * Load admin files that are not loaded by the autoload.
	 */
	protected function load_files() {
		require_once __DIR__ . '/class-papi-rest-api-post.php';
		require_once __DIR__ . '/class-papi-rest-api-settings.php';
	}

	/**
	 * Register REST API routes.
	 */
	public function register_routes() {
	}

	/**
	 * REST API init callback.
	 */
	public function rest_api_init() {
		papi_get_all_entry_types();
	}

	/**
	 * Setup actions.
	 */
	protected function setup_actions() {
		add_action( 'rest_api_init', [$this, 'rest_api_init'] );
		add_action( 'rest_api_init', [$this, 'register_routes'] );
	}
}

new Papi_REST_API;
