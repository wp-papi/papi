<?php

/**
 * Admin class that handle option post data.
 */
final class Papi_Admin_Option_Handler extends Papi_Core_Data_Handler {

	/**
	 * Save properties with a post id of zero.
	 */
	public function save_properties() {
		if ( $_SERVER ['REQUEST_METHOD'] !== 'POST' || papi_get_meta_type() !== 'option' ) {
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
			papi_data_update( 0, $key, $value, 'option' );
		}

		/**
		 * Fire `save_properties` action when all is done.
		 *
		 * @param int    $id
		 * @param string $meta_type
		 */
		do_action( 'papi/save_properties', 0, 'option' );
	}

	/**
	 * Setup actions.
	 */
	protected function setup_actions() {
		add_action( 'admin_init', [$this, 'save_properties'] );
	}
}

if ( papi_is_admin() ) {
	new Papi_Admin_Option_Handler;
}
