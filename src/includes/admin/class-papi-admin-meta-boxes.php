<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Admin Meta Boxes.
 *
 * @package Papi
 * @since 1.0.0
 */

class Papi_Admin_Meta_Boxes {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */

	public function __construct() {
		$this->setup_actions();
	}

	/**
	 * Setup actions.
	 *
	 * @todo Try to get this working
	 * @since 1.0.0
	 */

	private function setup_actions() {
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );
	}

	/**
	 * Sanitize data before saving it.
	 *
	 * @param mixed $value
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */

	private function santize_data( $value ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $k => $v ) {
				if ( is_string( $v ) ) {
					$value[ $k ] = $this->santize_data( $v );
				}
			}
		} else if ( is_string( $value ) ) {
			$value = papi_remove_trailing_quotes( $value );
		}

		return $value;
	}

	/**
	 * Get post data.
	 *
	 * @param string $pattern
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	private function get_post_data( $pattern = '/^papi\_.*/' ) {
		$data    = array();
		$keys    = preg_grep( $pattern, array_keys( $_POST ) );

		foreach ( $keys as $key ) {
			if ( ! isset( $_POST[ $key ] ) ) {
				continue;
			}

			// Fix for input fields that should be true or false.
			if ( $_POST[ $key ] === 'on' ) {
				$data[ $key ] = true;
			} else {
				$data[ $key ] = $this->santize_data( $_POST[ $key ] );
			}
		}

		// Don't wont to save meta nonce field.
		if ( isset( $data['papi_meta_nonce'] ) ) {
			unset( $data['papi_meta_nonce'] );
		}

		return $data;
	}

	/**
	 * Prepare properties data for saving.
	 *
	 * @param array $data
	 * @param int $post_id
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	private function prepare_properties_data( array $data = array(), $post_id ) {
		// Since we are storing witch property it is in the $data array
		// we need to remove that and set the property type to the property
		// and make a array of the property type and the value.
		foreach ( $data as $key => $value ) {
			$property_type_key = papi_get_property_type_key();

			if ( strpos( $key, $property_type_key ) === false ) {
				continue;
			}

			$property_key = str_replace( $property_type_key, '', $key );

			// Check if value exists.
			if ( isset( $data[ $property_key ] ) ) {
				$data[ $property_key ] = array(
					'type'  => $value,
					'value' => $data[ $property_key ]
				);
			}

			unset( $data[ $key ] );
		}

		// Run `before_save` on a property class if it exists.
		foreach ( $data as $key => $value ) {
			if ( ! is_array( $value ) || ! isset( $value['type'] ) ) {
				continue;
			}

			// Get the property, will only make the instance once.
			$property = papi_get_property_type( $value['type'] );

			// Can't handle null properties.
			// Remove it from the data array and continue.
			if ( is_null( $property ) ) {
				unset( $data[ $key ] );
				continue;
			}

			// Run `update_value` if it exists on the property class.
			$data[ $key ]['value'] = $property->update_value( $data[ $key ]['value'], papi_remove_papi( $key ), $post_id );

			// Apply a filter so this can be changed from the theme for specified property type.
			$data[ $key ]['value'] = papi_filter_update_value( $value['type'], $data[ $key ]['value'], papi_remove_papi( $key ), $post_id );
		}

		// Check so all properties has a value and a type key and that the property is a array.
		$data = array_filter( $data, function ( $property ) {
			return is_array( $property ) && isset( $property['value'] ) && isset( $property['type'] );
		} );

		return $data;
	}

	/**
	 * Pre save page template and page type.
	 *
	 * @param int $post_id
	 *
	 * @since 1.0.0
	 */

	private function pre_save( $post_id ) {
		// Can't proceed without a post id.
		if ( is_null( $post_id ) ) {
			return;
		}

		$data = $this->get_post_data( '/^\_papi\_.*/' );

		foreach ( $data as $key => $value ) {
			if ( empty( $value ) ) {
				continue;
			}

			update_post_meta( $post_id, $key, $value );
		}
	}

	/**
	 * Save meta boxes.
	 *
	 * @since 1.0.0
	 */

	public function save_meta_boxes() {
		// Fetch the post id.
		if ( isset( $_POST['post_ID'] ) ) {
			$post_id = papi_get_sanitized_post( 'post_ID' );
			$post_id = intval( $post_id );
		}

		// Can't proceed without a post id.
		if ( empty( $post_id ) ) {
			return;
		}

		// Check the post being saved has the same id as the post id. This will prevent other save post events.
		if ( papi_get_sanitized_post( 'post_ID' ) != strval( $post_id ) ) {
			return;
		}

		$post = get_post( $post_id );

		// Can't proceed without a post id or a post.
		if ( empty( $post ) ) {
			return;
		}

		// Don't save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check if our nonce is vailed.
		if ( ! isset( $_POST['papi_meta_nonce'] ) || ! wp_verify_nonce( $_POST['papi_meta_nonce'], 'papi_save_data' ) ) {
			return;
		}

		// Check for any of the capabilities before we save the code.
		if ( ! current_user_can( 'edit_posts' ) || ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		// Convert post id to int if is a string.
		if ( is_string( $post_id ) ) {
			$post_id = intval( $post_id );
		}

		$this->save_property( $post_id );
	}

	/**
	 * Save property and property type.
	 *
	 * @param int $post_id
	 *
	 * @since 1.0.0
	 */

	public function save_property( $post_id ) {
		// Pre save page template, page type and some others dynamic values.
		$this->pre_save( $post_id );

		// Get properties data.
		$data = $this->get_post_data();

		// Prepare property data.
		$data = $this->prepare_properties_data( $data, $post_id );

		foreach ( $data as $key => $property ) {
			papi_property_update_meta( array(
				'post_id'       => $post_id,
				'slug'          => $key,
				'type'          => $property['type'],
				'value'         => $property['value']
			) );
		}
	}
}
