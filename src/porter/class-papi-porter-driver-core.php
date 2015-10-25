<?php

/**
 * Papi core Porter driver.
 */
class Papi_Porter_Driver_Core extends Papi_Porter_Driver {

	/**
	 * The driver name.
	 *
	 * @var string
	 */
	protected $name = 'core';

	/**
	 * Alias for driver name.
	 *
	 * @var string
	 */
	protected $alias = 'papi';

	/**
	 * Get value that should be saved.
	 *
	 * @param  array $options
	 *
	 * @throws InvalidArgumentException when option values not matching the expected value.
	 *
	 * @return mixed
	 */
	public function get_value( array $options = [] ) {
		if ( ! isset( $options['post_id'] ) || ! is_int( $options['post_id'] ) ) {
			throw new InvalidArgumentException(
				'Missing `post_id` option. Should be int.'
			);
		}

		if ( ! isset( $options['property'] ) || $options['property'] instanceof Papi_Core_Property === false ) {
			throw new InvalidArgumentException(
				'Missing `property` option. Should be instance of `Papi_Core_Property`.'
			);
		}

		if ( ! isset( $options['slug'] ) || empty( $options['slug'] ) ) {
			$options['slug'] = $options['property']->get_slug( true );
		}

		if ( ! isset( $options['value'] ) ) {
			throw new InvalidArgumentException( 'Missing `value` option.' );
		}

		$value = $this->call_value( $options['value'] );
		$value = $this->update_value(
			$options['property'],
			$value,
			$options['slug'],
			$options['post_id']
		);

		return papi_maybe_json_decode( maybe_unserialize( $value ) );
	}

	/**
	 * Update value.
	 *
	 * @param  Papi_Core_Property $property
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return array
	 */
	protected function update_value( $property, $value, $slug, $post_id ) {
		if ( ! is_array( $value ) || ! $this->should_update_array( $slug ) ) {
			return $property->import_value( $value, $slug, $post_id );
		}

		$old   = papi_get_field( $post_id, $slug, [] );
		$value = array_merge( $old, $value );
		$value = $property->import_value( $value, $slug, $post_id );

		if ( $property->import_setting( 'property_array_slugs' ) ) {
			return papi_from_property_array_slugs( $value, $slug );
		}

		return $property->update_value( $value, $slug, $post_id );
	}
}
