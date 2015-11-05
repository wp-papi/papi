<?php

/**
 * Admin class that handle option post data.
 */
final class Papi_Admin_Option_Handler extends Papi_Core_Data_Handler {

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->setup_actions();
	}

	/**
	 * Save options with a post id of zero.
	 */
	public function save_options() {
		if ( ! papi_is_method( 'post' ) || ! papi_is_option_page() ) {
			return;
		}

		// Check if our nonce is vailed.
		if ( ! wp_verify_nonce( papi_get_sanitized_post( 'papi_meta_nonce' ), 'papi_save_data' ) ) {
			return;
		}

		// Get properties data.
		$data = $this->get_post_data();

		// Prepare properties data.
		$data = $this->prepare_properties_data( $data, 0 );

		foreach ( $data as $key => $value ) {
			papi_update_property_meta_value( [
				'post_id'       => 0,
				'slug'          => $key,
				'value'         => $value
			] );
		}
	}

	/**
	 * Setup actions.
	 */
	private function setup_actions() {
		add_action( 'admin_init', [$this, 'save_options'], 10 );
	}
}

if ( is_admin() ) {
	new Papi_Admin_Option_Handler;
}
