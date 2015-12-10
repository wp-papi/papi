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
			if ( $content_type = papi_get_content_type_by_id( papi_get_qs( 'page' ) ) ) {
				$this->option_type = papi_is_option_type( $content_type ) ? $content_type : null;
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
		$content_type_id = papi_get_qs( 'page' );

		if ( empty( $this->option_type ) ) {
			$property   = null;
			$content_types = papi_get_all_content_types( [
				'types' => 'option'
			] );

			foreach ( $content_types as $index => $content_type ) {
				if ( $property = $content_type->get_property( $slug, $child_slug ) ) {
					$this->option_type = $content_type;
					break;
				}
			}

			if ( is_null( $property ) ) {
				return;
			}

			return $property;
		}

		$content_type = papi_get_content_type_by_id( $content_type_id );

		if ( ! papi_is_option_type( $content_type ) ) {
			return;
		}

		$this->option_type = $content_type;

		return $content_type->get_property( $slug, $child_slug );
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
