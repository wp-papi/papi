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
	protected function get_meta_type() {
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
	protected function overwrite_post_data( $post_id ) {
		global $wpdb;

		if ( empty( $post_id ) || empty( $this->overwrite ) ) {
			return;
		}

		$wpdb->update( $wpdb->posts, $this->overwrite, [
			'ID' => $post_id
		] );

		// Delete cache since it can be cached even if not saved in the database.
		foreach ( array_keys( $this->overwrite ) as $key ) {
			papi_cache_delete( $key, $post_id );
		}

		clean_post_cache( $post_id );
	}

	/**
	 * Pre save page template and page type.
	 *
	 * @param int $id
	 */
	protected function pre_save( $id ) {
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
	 * @param int     $id
	 * @param object  $post
	 */
	public function save_meta_boxes( $id, $post = null ) {
		// Check if there was a multisite switch before.
		if ( is_multisite() && ms_is_switched() ) {
			return;
		}

		// Can't proceed without a id.
		if ( empty( $id ) ) {
			return;
		}

		// Check if our nonce is vailed.
		if ( ! wp_verify_nonce( papi_get_sanitized_post( 'papi_meta_nonce' ), 'papi_save_data' ) ) {
			return;
		}

		$meta_type = $this->get_meta_type();
		$post      = is_array( $post ) ? (object) $post : $post;

		if ( $meta_type === 'post' && $post_type = get_post_type_object( $post->post_type ) ) {
			// Check so the id is a post id and not a autosave post.
			if ( ! $this->valid_post_id( $id ) ) {
				return;
			}

			// Check the `edit_posts` capability before we continue.
			if ( ! current_user_can( $post_type->cap->edit_posts ) ) {
				return;
			}

			// Save post revision data.
			if ( $parent_id = wp_is_post_revision( $id ) ) {
				$slugs = papi_get_slugs( $id, true );

				foreach ( $slugs as $slug ) {
					papi_update_field( $id, $slug, papi_get_field( $parent_id, $slug ) );
				}
			}

			// Delete all oEmbed caches.
			if ( class_exists( 'WP_Embed' ) ) {
				global $wp_embed;

				if ( $wp_embed instanceof WP_Embed ) {
					$wp_embed->delete_oembed_caches( $id );
				}
			}
		}

		if ( $meta_type === 'term' && $taxonomy = get_taxonomy( papi_get_taxonomy() ) ) {
			// Check the `edit_terms` capability before we continue.
			if ( $taxonomy && ! current_user_can( $taxonomy->cap->edit_terms ) ) {
				return;
			}
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

		// Get meta type.
		$meta_type = $this->get_meta_type();

		// Overwrite post data if any.
		if ( $meta_type === 'post' ) {
			$this->overwrite_post_data( $id );
		}

		// Save all properties value
		foreach ( $data as $key => $value ) {
			papi_data_update( $id, $key, $value, $this->get_meta_type() );
		}

		/**
		 * Fire `save_properties` action when all is done.
		 *
		 * @param int    $id
		 * @param string $meta_type
		 */
		do_action( 'papi/save_properties', $id, $meta_type );
	}

	/**
	 * Restore to post revision.
	 *
	 * @param int $post_id
	 * @param int $revision_id
	 */
	public function restore_post_revision( $post_id, $revision_id ) {
		$slugs = papi_get_slugs( $revision_id, true );

		foreach ( $slugs as $slug ) {
			$value = papi_get_field( $revision_id, $slug );

			if ( papi_is_empty( $value ) ) {
				papi_delete_field( $post_id, $slug );
			} else {
				papi_update_field( $post_id, $slug, $value );
			}
		}
	}

	/**
	 * Setup actions.
	 */
	protected function setup_actions() {
		add_action( 'save_post', [$this, 'save_meta_boxes'], 1, 2 );
		add_action( 'created_term', [$this, 'save_meta_boxes'], 1 );
		add_action( 'edit_term', [$this, 'save_meta_boxes'], 1 );
		add_action( 'wp_restore_post_revision', [$this, 'restore_post_revision'], 10, 2 );
	}

	/**
	 * Check if post id is valid or not.
	 *
	 * @param  int $post_id
	 *
	 * @return bool
	 */
	protected function valid_post_id( $post_id ) {
		$key = papi_get_sanitized_post( 'action' ) === 'save-attachment-compat' ? 'id' : 'post_ID';
		$val = papi_get_sanitized_post( $key );

		// When autosave is in place the post id is located deeper in the post data array, the ids should not match.
		if ( isset( $_POST['data'], $_POST['data']['wp_autosave'], $_POST['data']['wp_autosave']['post_id'] ) ) {
			return sanitize_text_field( $_POST['data']['wp_autosave']['post_id'] ) !== strval( $post_id );
		}

		// Should not be the same id when `wp-preview` equals `dopreview`.
		if ( isset( $_POST['wp-preview'] ) && strtolower( $_POST['wp-preview'] ) === 'dopreview' ) {
			return $val !== strval( $post_id );
		}

		return $val === strval( $post_id );
	}
}

if ( papi_is_admin() ) {
	new Papi_Admin_Meta_Handler;
}
