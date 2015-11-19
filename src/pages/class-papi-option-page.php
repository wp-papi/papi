<?php

/**
 * Option page implementation of Papi page.
 */
class Papi_Option_Page extends Papi_Core_Page {

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
	 * Load property from page type.
	 *
	 * @param  string $slug
	 * @param  string $child_slug
	 *
	 * @return object
	 */
	public function get_property( $slug, $child_slug = '' ) {
		$content_type_id = str_replace( 'papi/', '', papi_get_qs( 'page' ) );

		if ( empty( $content_type_id ) ) {
			$property   = null;
			$content_types = papi_get_all_content_types( [
				'types' => 'option'
			] );

			foreach ( $content_types as $index => $content_types ) {
				if ( $property = $content_types->get_property( $slug, $child_slug ) ) {
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
