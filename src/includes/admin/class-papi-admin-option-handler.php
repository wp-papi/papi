<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Admin Option Handler.
 *
 * @package Papi
 */

class Papi_Admin_Option_Handler extends Papi_Admin_Data_Handler {

	/**
	 * Constructor.
	 */

	public function __construct() {
		if ( papi_is_metod( 'post' ) && papi_is_option_page() ) {
			$this->save_options();
		}
	}

	/**
	 * Save options with a post id of zero.
	 */

	private function save_options() {
		$data = $this->get_post_data();
		$data = $this->prepare_properties_data( $data, 0 );

		foreach ( $data as $key => $property ) {
			papi_property_update_meta( [
				'post_id'       => 0,
				'slug'          => $key,
				'type'          => $property['type'],
				'value'         => $property['value']
			] );
		}
	}

}

new Papi_Admin_Option_Handler;
