<?php

/**
 * Papi Admin Option Handler class.
 */
class Papi_Admin_Option_Handler extends Papi_Admin_Data_Handler {

	/**
	 * The constructor.
	 */
	public function __construct() {
		if ( papi_is_method( 'post' ) && papi_is_option_page() ) {
			$this->save_options();
		}
	}

	/**
	 * Save options with a post id of zero.
	 */
	private function save_options() {
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
}

if ( is_admin() ) {
	new Papi_Admin_Option_Handler;
}
