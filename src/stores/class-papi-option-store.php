<?php

/**
 * Option store implementation of Papi meta store.
 */
class Papi_Option_Store extends Papi_Core_Meta_Store {

	/**
	 * The meta type.
	 *
	 * @var string
	 */
	protected $type = 'option';

	/**
	 * The constructor.
	 *
	 * @param int $id
	 */
	public function __construct( $id = 0 ) {
		// Options don't have a id so set it to zero.
		$this->id = 0;
	}

	/**
	 * Load property from page type.
	 *
	 * @param  string $slug
	 * @param  string $child_slug
	 *
	 * @return null|object
	 */
	public function get_property( $slug, $child_slug = '' ) {
		$entry_type_id = papi_get_qs( 'page' );

		if ( empty( $entry_type_id ) ) {
			$property   = null;
			$entry_types = papi_get_all_entry_types( [
				'types' => 'option'
			] );

			foreach ( $entry_types as $entry_type ) {
				if ( $property = $entry_type->get_property( $slug, $child_slug ) ) {
					break;
				}
			}

			if ( is_null( $property ) ) {
				return;
			}

			return $property;
		}

		$entry_type = papi_get_entry_type_by_id( $entry_type_id );

		if ( $entry_type instanceof Papi_Option_Type === false ) {
			return;
		}

		if ( $property = $entry_type->get_property( $slug, $child_slug ) ) {
			return $this->prepare_property( $property );
		}
	}

	/**
	 * Check if it's a valid store.
	 *
	 * @return bool
	 */
	public function valid() {
		return $this->id === 0;
	}
}
