<?php

/**
 * Option page implementation of Papi page.
 */
class Papi_Option_Page extends Papi_Core_Page {

	/**
	 * The current option type.
	 *
	 * @var Papi_Option_Type
	 */
	protected $option_type;

	/**
	 * Type option.
	 *
	 * @var string
	 */
	const TYPE = 'option';

	/**
	 * The constructor.
	 *
	 * Create a new instance of the class.
	 *
	 * @param int $post_id
	 */
	public function __construct( $post_id = 0 ) {
		// On option page this should always be equal to zero.
		$this->id = 0;
	}

	/**
	 * Get the current option type.
	 *
	 * @return null|Papi_Option_Type
	 */
	public function get_option_type() {
		if ( empty( $this->option_type ) ) {
			if ( $entry_type = papi_get_entry_type_by_id( papi_get_qs( 'page' ) ) ) {
				$this->option_type = papi_is_option_type( $entry_type ) ? $entry_type : null;
			}
		}

		return $this->option_type;
	}

	/**
	 * Load property from page type.
	 *
	 * @param  string $slug
	 * @param  string $child_slug
	 *
	 * @return object
	 */
	public function get_property( $slug, $child_slug = '' ) {
		$entry_type_id = papi_get_qs( 'page' );

		if ( empty( $entry_type_id ) ) {
			$property   = null;
			$entry_types = papi_get_all_entry_types( [
				'types' => 'option'
			] );

			foreach ( $entry_types as $index => $entry_type ) {
				if ( $property = $entry_type->get_property( $slug, $child_slug ) ) {
					$this->option_type = $entry_type;
					break;
				}
			}

			if ( is_null( $property ) ) {
				return;
			}

			return $property;
		}

		$entry_type = papi_get_entry_type_by_id( $entry_type_id );

		if ( ! papi_is_option_type( $entry_type ) ) {
			return;
		}

		$this->option_type = $entry_type;

		return $this->prepare_property( $entry_type->get_property( $slug, $child_slug ) );
	}

	/**
	 * Check if it's a valid page.
	 *
	 * @return bool
	 */
	public function valid() {
		return $this->id === 0;
	}
}
