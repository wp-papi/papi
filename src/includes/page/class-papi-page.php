<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Page.
 *
 * @package Papi
 */

class Papi_Page extends Papi_Page_Manager {

	/**
	 * Data type to describe which
	 * type of page data is it.
	 *
	 * @var string
	 */

	protected $data_type = 'post';

	/**
	 * The WordPress post.
	 *
	 * @var object
	 */

	private $post;

	/**
	 * The Page type.
	 *
	 * @var Papi_Page_Type
	 */

	private $page_type;

	/**
	 * Create a new instance of the class.
	 *
	 * @param int $post_id
	 */

	public function __construct( $post_id = 0 ) {
		$this->id        = intval( $post_id );
		$this->post      = get_post( $post_id );
		$id              = papi_get_page_type_meta_value( $post_id );
		$this->page_type = papi_get_page_type_by_id( $id );
	}

	/**
	 * Get the page type object of the page.
	 *
	 * @return Papi_Page_Type
	 */

	public function get_page_type() {
		return $this->page_type;
	}

	/**
	 * Get the permalink for the page.
	 *
	 * @return string
	 */

	public function get_permalink() {
		return get_permalink( $this->id );
	}

	/**
	 * Get the WordPress post object.
	 *
	 * @return object
	 */

	public function get_post() {
		return $this->post;
	}

	/**
	 * Get the post status of a page.
	 *
	 * @return string
	 */

	public function get_status() {
		return get_post_status( $this->id );
	}

	/**
	 * Get property value.
	 *
	 * @param string $slug
	 *
	 * @return mixed
	 */

	public function get_value( $slug ) {
		$slug                = papi_remove_papi( $slug );
		$property_value      = get_post_meta( $this->id, $slug, true );
		$property_type_key   = papi_get_property_type_key_f( $slug );
		$property_type_value = get_post_meta( $this->id, $property_type_key, true );

		if ( papi_is_empty( $property_value ) || empty( $property_type_value ) ) {
			return;
		}

		// The convert takes a array as argument so let's make one.
		$property_value = $this->convert(
			$slug,
			$property_type_value,
			$property_value
		);

		if ( is_array( $property_value ) ) {
			$property_value = array_filter( $property_value );
		}

		return $property_value;
	}

	/**
	 * Check if the page has the post object and that it's not null.
	 *
	 * @return bool
	 */

	public function valid() {
		return $this->post != null;
	}

}
