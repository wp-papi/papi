<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Data Handler class.
 *
 * @package Papi
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
	 * @param  string $pattern
	 *
	 * @return array
	 */
	protected function get_post_data( $pattern = '/^papi\_.*/' ) {
		$data = [];
		$keys = preg_grep( $pattern, array_keys( $_POST ) );

		foreach ( $keys as $key ) {
			// Fix for input fields that should be true on `on` value.
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
	 * @param  mixed $data
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
	 * @param  array $data
	 * @param  int   $post_id
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

		foreach ( $data as $key => $item ) {
			$property = papi_get_property_type( $item['type'] );

			unset( $data[ $key ] );

			if ( papi_is_property( $property ) ) {
				// Run `update_value` method on the property class.
				$data[$key] = $property->update_value(
					$item['value'],
					papi_remove_papi( $key ),
					$post_id
				);

				// Apply `update_value` filter so this can be changed from the theme for specified property type.
				$data[$key] = papi_filter_update_value(
					$item['type']->type,
					$data[$key],
					papi_remove_papi( $key ),
					$post_id
				);
			}
		}

		return $data;
	}

	/**
	 * Get pre data that should be saved before all properties data.
	 */
	protected function get_pre_data() {
		return $this->get_post_data( '/^\_papi\_.*/' );
	}
}
