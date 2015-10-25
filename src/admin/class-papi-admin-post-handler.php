<?php

/**
 * Admin class that handle post data.
 */
final class Papi_Admin_Post_Handler extends Papi_Core_Data_Handler {

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->setup_actions();
	}

	/**
	 * Overwrite post data in `posts` table.
	 *
	 * @param int $post_id
	 */
	private function overwrite_post_data( $post_id ) {
		global $wpdb;

		if ( empty( $post_id ) || empty( $this->overwrite ) ) {
			return;
		}

		$wpdb->update( $wpdb->posts, $this->overwrite, ['ID' => $post_id] );
	}

	/**
	 * Pre save page template and page type.
	 *
	 * @param int $post_id
	 */
	private function pre_save( $post_id ) {
		if ( empty( $post_id ) ) {
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

			update_post_meta( $post_id, $key, $value );
		}
	}

	/**
	 * Save meta boxes.
	 *
	 * @param int    $post_id
	 * @param object $post
	 */
	public function save_meta_boxes( $post_id, $post ) {
		// Can't proceed without a post id or post.
		if ( empty( $post_id ) || empty( $post ) ) {
			return;
		}

		// Don't save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check the post being saved has the same id as the post id.
		// This will prevent other save post events.
		if ( $this->valid_post_id( $post_id ) ) {
			return;
		}

		// Check if our nonce is vailed.
		if ( ! wp_verify_nonce( papi_get_sanitized_post( 'papi_meta_nonce' ), 'papi_save_data' ) ) {
			return;
		}

		// Check for any of the capabilities before we save the code.
		if ( ! current_user_can( 'edit_posts' ) || ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		$this->save_properties( $post_id );
	}

	/**
	 * Save properties.
	 *
	 * @param int $post_id
	 */
	public function save_properties( $post_id ) {
		// Pre save page template, page type and some others dynamic values.
		$this->pre_save( $post_id );

		// Get properties data.
		$data = $this->get_post_data();

		// Prepare properties data.
		$data = $this->prepare_properties_data( $data, $post_id );

		// Overwrite post data if any.
		$this->overwrite_post_data( $post_id );

		// Save all properties value
		foreach ( $data as $key => $value ) {
			papi_update_property_meta_value( [
				'post_id'       => $post_id,
				'slug'          => $key,
				'value'         => $value
			] );
		}
	}

	/**
	 * Setup actions.
	 */
	private function setup_actions() {
		add_action( 'save_post', [$this, 'save_meta_boxes'], 1, 2 );
	}

	/**
	 * Check if post id is valid or not.
	 *
	 * @param  int $post_id
	 *
	 * @return bool
	 */
	private function valid_post_id( $post_id ) {
		$key = papi_get_sanitized_post( 'action' ) === 'save-attachment-compat'
			? 'id'
			: 'post_ID';

		return papi_get_sanitized_post( $key ) !== strval( $post_id );
	}
}

if ( is_admin() ) {
	new Papi_Admin_Post_Handler;
}
