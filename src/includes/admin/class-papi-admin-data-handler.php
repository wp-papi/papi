<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Data Handler.
 *
 * @package Papi
 * @since 1.3.0
 */

class Papi_Admin_Data_Handler {

	/**
	 * Decode property.
	 *
	 * @param string $key
	 * @param string $value
	 */

	protected function decode_property( $key, $value ) {
		if ( papi_is_property_type_key( $key ) && is_string( $value ) ) {
			$value = base64_decode( $value );
			$value = unserialize( $value );
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

	protected function get_post_data( $pattern = '/^papi\_.*/' ) {
		$data = [];
		$keys = preg_grep( $pattern, array_keys( $_POST ) );

		foreach ( $keys as $key ) {
			if ( ! isset( $_POST[ $key ] ) ) {
				continue;
			}

			// Fix for input fields that should be true or false.
			if ( $_POST[ $key ] === 'on' ) {
				$data[ $key ] = true;
			} else {
				$value = $this->decode_property( $key, $_POST[ $key ] );
				$data[ $key ] = $this->prepare_post_data( $value );
			}
		}

		// Don't wont to save meta nonce field.
		if ( isset( $data['papi_meta_nonce'] ) ) {
			unset( $data['papi_meta_nonce'] );
		}

		return $data;
	}

	/**
	 * Prepare post data.
	 * Will decode property options recursive.
	 *
	 * @param mixed $data
	 * @since 1.3.0
	 *
	 * @return mixed
	 */

	protected function prepare_post_data( $data ) {
		if ( ! is_array( $data ) ) {
			return $data;
		}

		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				$data[$key] = $this->prepare_post_data( $value );
			} else {
				$data[$key] = $this->decode_property( $key, $value );
			}
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

	protected function prepare_properties_data( array $data = [], $post_id = 0 ) {
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
			if ( isset( $data[$property_key] ) ) {
				$data[$property_key] = [
					'type'  => $value,
					'value' => $data[$property_key]
				];
			}

			unset( $data[$key] );
		}

		// Run `before_save` on a property class if it exists.
		foreach ( $data as $key => $item ) {
			if ( ! is_array( $item ) || ! isset( $item['type'] ) ) {
				continue;
			}

			// Get the property, will only make the instance once.
			$property = papi_get_property_type( $item['type']->type );

			// Can't handle null properties.
			// Remove it from the data array and continue.
			if ( is_null( $property ) ) {
				unset( $data[ $key ] );
				continue;
			}

			$property->set_options( $item['type'] );
			$property->set_option( 'value', $item['value'] );

			// Get right value and right type from the property.
			$data[$key]['value']  = $property->get_value( false );
			$data[$key]['type'] = $property->get_option( 'type' );

			// Run `update_value` if it exists on the property class.
			$data[$key]['value'] = $property->update_value( $data[$key]['value'], papi_remove_papi( $key ), $post_id );

			// Apply a filter so this can be changed from the theme for specified property type.
			$data[$key]['value'] = papi_filter_update_value( $item['type'], $data[$key]['value'], papi_remove_papi( $key ), $post_id );
		}

		// Check so all properties has a value and a type key and that the property is a array.
		$data = array_filter( $data, function ( $item ) {
			return is_array( $item ) && isset( $item['value'] ) && isset( $item['type'] );
		} );

		return $data;
	}

	/**
	 * Pre save page template and page type.
	 *
	 * @since 1.0.0
	 */

	protected function get_pre_data() {
		return $this->get_post_data( '/^\_papi\_.*/' );
	}
}
