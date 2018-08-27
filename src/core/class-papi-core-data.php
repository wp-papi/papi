<?php

class Papi_Core_Data {

	/**
	 * Does the meta use id?
	 *
	 * @var bool
	 */
	protected $id = false;

	/**
	 * Meta type.
	 *
	 * @var
	 */
	protected $type;

	/**
	 * Property data constructor.
	 *
	 * @param $type
	 */
	public function __construct( $type = 'post' ) {
		$this->type = is_string( $type ) ? papi_get_meta_type( $type ) : 'post';
		$this->id   = $this->type !== 'option' && $this->type !== 'site' && $this->type !== 'network';
	}

	/**
	 * Delete property value.
	 *
	 * @param  int    $id
	 * @param  string $slug
	 *
	 * @return bool
	 */
	public function delete( $id, $slug ) {
		$fn = $this->get_function( 'delete' );

		// Check so the function is callable before using it.
		if ( ! is_callable( $fn ) ) {
			return false;
		}

		// Delete cached value.
		papi_cache_delete( $slug, $id, $this->type );

		if ( $this->id ) {
			return call_user_func_array( $fn, [$this->type, $id, unpapify( $slug )] );
		}

		return call_user_func_array( $fn, [unpapify( $slug )] );
	}

	/**
	 * Get right meta function for right type and context.
	 *
	 * @param  string $context
	 *
	 * @return string
	 */
	public function get_function( $context = 'get' ) {
		switch ( $this->type ) {
			case 'option':
				return sprintf( '%s_option', $context );
			case 'site':
			case 'network':
				return sprintf( '%s_site_option', $context );
			case 'post':
			case 'term':
				return sprintf( '%s_metadata', $context );
			default:
				break;
		}
	}

	/**
	 * Geta property value for right meta type.
	 *
	 * @param  int    $id
	 * @param  string $slug
	 *
	 * @return mixed
	 */
	public function get( $id, $slug ) {
		$fn = $this->get_function( 'get' );

		if ( ! is_callable( $fn ) ) {
			return;
		}

		if ( $this->id ) {
			$value = call_user_func_array( $fn, [$this->type, $id, unpapify( $slug ), true] );
		} else {
			$value = call_user_func_array( $fn, [unpapify( $slug ), null] );
		}

		if ( papi_is_empty( $value ) ) {
			return;
		}

		return $value;
	}

	/**
	 * Update property meta.
	 *
	 * @param  int    $id
	 * @param  string $slug
	 * @param  mixed  $value
	 *
	 * @return bool
	 */
	public function update( $id, $slug, $value ) {
		$save_value = true;

		// Get right update function to use.
		$fn = $this->get_function( 'update' );

		if ( ! is_callable( $fn ) ) {
			return false;
		}

		// Check for string keys in the array if any.
		foreach ( array_keys( papi_to_array( $value ) ) as $key ) {
			if ( is_string( $key ) ) {
				$save_value = false;
				break;
			}
		}

		// If main value shouldn't be saved it should be array.
		if ( ! $save_value && is_array( $value ) ) {
			$value = [$value];
		}

		// Delete saved value if empty.
		if ( papi_is_empty( $value ) ) {
			return $this->delete( $id, $slug );
		}

		$result = true;

		foreach ( papi_to_array( $value ) as $key => $val ) {
			// Delete saved value if value is empty.
			if ( papi_is_empty( $val ) || $val === '[]' || $val === '{}' ) {
				return $this->delete( $id, $slug );
			}

			// Delete main value cache.
			papi_cache_delete( $slug, $id, $this->type );

			// If not a array we can save the value.
			if ( ! is_array( $val ) ) {
				if ( $save_value ) {
					$val = $value;
				}

				if ( $this->id ) {
					$out    = call_user_func_array( $fn, [$this->type, $id, unpapify( $slug ), $val] );
					$result = $out ? $result : $out;
				} else {
					$out    = call_user_func_array( $fn, [unpapify( $slug ), $val] );
					$result = $out ? $result : $out;
				}

				continue;
			}

			// Clear cache for all child values.
			$this->update_clear_cache( $id, $value );

			// Update metadata or option value for all child values.
			foreach ( $val as $child_key => $child_value ) {
				if ( papi_is_empty( $child_value ) ) {
					$this->delete( $id, $child_key );
				} else {
					if ( $this->id ) {
						call_user_func_array( $fn, [$this->type, $id, unpapify( $child_key ), $child_value] );
					} else {
						call_user_func_array( $fn, [unpapify( $child_key ), $child_value] );
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Clear cache values on update property meta.
	 *
	 * @param  int   $id
	 * @param  mixed $value
	 */
	public function update_clear_cache( $id, $value ) {
		$value = is_array( $value ) ? $value : [];

		foreach ( $value as $child_key => $child_value ) {
			papi_cache_delete( $child_key, $id, $this->type );

			if ( is_array( $child_value ) ) {
				$this->update_clear_cache( $id, $child_value );
			}
		}
	}
}
