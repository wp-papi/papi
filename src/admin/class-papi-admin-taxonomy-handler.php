<?php

/**
 * Admin class that handle post data.
 */
final class Papi_Admin_Taxonomy_Handler extends Papi_Core_Data_Handler {

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->setup_actions();
	}

	/**
	 * Save meta boxes.
	 *
	 * @param int $term_id
	 */
	public function save_meta_boxes( $term_id ) {

		// Can't proceed without a post id or post.
		if ( empty( $term_id ) ) {
			return;
		}

		// Don't save meta boxes for autosaves
		if ( defined( 'DOING_AUTOSAVE' ) ) {
			return;
		}

		// Check if our nonce is vailed.
		if ( ! wp_verify_nonce( papi_get_sanitized_post( 'papi_meta_nonce' ), 'papi_save_data' ) ) {
			return;
		}

		$this->save_properties( $term_id );
	}

	/**
	 * Pre save page template and page type.
	 *
	 * @param int $term_id
	 */
	private function pre_save( $term_id ) {
		if ( empty( $term_id ) ) {
			return;
		}

		$data = $this->get_pre_data();

		foreach ( $data as $key => $value ) {
			if ( empty( $value ) ) {
				continue;
			}

			if ( is_array( $value ) ) {
				list( $keys, $value ) = $this->get_pre_deep_keys_value( $value );
				$key = sprintf( '%s_%s', $key, implode( '_', $keys ) );
			}

			update_term_meta( $term_id, $key, $value );
		}
	}

	/**
	 * Save properties.
	 *
	 * @param int $term_id
	 */
	public function save_properties( $term_id ) {

		$this->pre_save( $term_id );

		// Get properties data.
		$data = $this->get_post_data();

		// Prepare properties data.
		$data = $this->prepare_properties_data( $data, 0 );

		// Save all properties value
		foreach ( $data as $key => $value ) {
			papi_update_property_meta_value( [
				'post_id'       => $term_id,
				'slug'          => $key,
				'type'          => Papi_Taxonomy_Page::TYPE,
				'value'         => $value
			] );
		}

	}

	/**
	 * Setup actions.
	 */
	private function setup_actions() {
		add_action( 'created_term', [$this, 'save_meta_boxes'], 1 );
		add_action( 'edit_term', [$this, 'save_meta_boxes'], 1 );
	}
}

if ( is_admin() ) {
	new Papi_Admin_Taxonomy_Handler;
}
