<?php

/**
 * Option page implementation of Papi page.
 */
class Papi_Option_Page extends Papi_Core_Page {

	/**
	 * content type to describe which
	 * type of page data is it.
	 *
	 * @var string
	 */
	protected $type = self::TYPE_OPTION;

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
		$page_type_id = str_replace( 'papi/', '', papi_get_qs( 'page' ) );

		if ( empty( $page_type_id ) ) {
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


		$page_type = papi_get_content_type_by_id( $page_type_id );

		if ( $page_type instanceof Papi_Option_Type === false ) {
			return;
		}

		return $page_type->get_property( $slug, $child_slug );
	}

	/**
	 * Check if it's a valid page.
	 *
	 * @return bool
	 */
	public function valid() {
		return $this->id === 0 && $this->valid_type();
	}
}
