<?php

class Papi_REST_API_Admin {

	/**
	 * REST API Admin constructor.
	 */
	public function __construct() {
		$this->setup_actions();
	}

	/**
	 * REST API init callback.
	 */
	public function rest_api_init() {
		if ( $entry_type = papi_get_current_entry_type() ) {
			$entry_type->setup_blocks();
		}
	}

	/**
	 * Setup actions.
	 */
	protected function setup_actions() {
		add_action( 'rest_api_init', [$this, 'rest_api_init'] );
	}
}

new Papi_REST_API_Admin;
