<?php

/**
 * Admin class that handle meta data, like post or term.
 */
final class Papi_Admin_Meta_Handler extends Papi_Core_Data_Handler {

	/**
	 * Get meta type so we know where the data should be saved.
	 *
	 * @return string
	 */
	private function get_meta_type() {
		if ( $current_filter = current_filter() ) {
			return papi_get_meta_type( explode( '_', $current_filter )[1] );
		}

		return papi_get_meta_type();
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
	private function pre_save( $id ) {
		if ( empty( $id ) ) {
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

			update_metadata( $this->get_meta_type(), $id, $key, $value );
		}
	}

	/**
	 * Save meta boxes.
	 *
	 * @param int    $post_id
	 * @param object $post
	 */
	public function save_meta_boxes( $id, $post = null ) {
		// Can't proceed without a id.
		if ( empty( $id ) ) {
			return;
		}

		// Don't save meta boxes for autosaves.
		// @codeCoverageIgnoreStart
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		// @codeCoverageIgnoreEnd

		if ( $this->get_meta_type() === 'post' ) {
			// Check so the id is a post id and not a revision post.
			if ( $this->valid_post_id( $id ) || is_int( wp_is_post_revision( $post ) ) || get_post_status( $id ) === 'auto-draft' ) {
				return;
			}

			// Check for any of the capabilities before we save the code.
			if ( ! current_user_can( 'edit_posts' ) || ! current_user_can( 'edit_pages' ) ) {
				return;
			}
		}

		// Check if our nonce is vailed.
		if ( ! wp_verify_nonce( papi_get_sanitized_post( 'papi_meta_nonce' ), 'papi_save_data' ) ) {
			return;
		}

		$this->save_properties( $id );
	}

	/**
	 * Save properties.
	 *
	 * @param int $id
	 */
	public function save_properties( $id ) {
		// Pre save page template, page type and some others dynamic values.
		$this->pre_save( $id );

		// Get properties data.
		$data = $this->get_post_data();

		// Prepare properties data.
		$data = $this->prepare_properties_data( $data, $id );

		// Overwrite post data if any.
		if ( $this->get_meta_type() === 'post' ) {
			$this->overwrite_post_data( $id );
		}

		// Save all properties value
		foreach ( $data as $key => $value ) {
			papi_update_property_meta_value( [
				'id'    => $id,
				'slug'  => $key,
				'type'  => $this->get_meta_type(),
				'value' => $value
			] );
		}
	}

	/**
	 * Setup actions.
	 */
	protected function setup_actions() {
		add_action( 'save_post', [$this, 'save_meta_boxes'], 1, 2 );
		add_action( 'created_term', [$this, 'save_meta_boxes'], 1 );
		add_action( 'edit_term', [$this, 'save_meta_boxes'], 1 );
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
	new Papi_Admin_Meta_Handler;
}
