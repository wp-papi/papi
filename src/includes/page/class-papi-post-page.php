<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Post Page.
 *
 * @package Papi
 */

class Papi_Post_Page extends Papi_Data_Page {

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
	 * Load property from page type.
	 *
	 * @param string $slug
	 *
	 * @return object
	 */

	protected function get_property_from_page_type( $slug ) {
		$page_type_id = papi_get_page_type_meta_value( $this->id );
		$page_type    = papi_get_page_type_by_id( $page_type_id );

		if ( ! is_object( $page_type ) || ! ( $page_type instanceof Papi_Page_Type ) ) {
			return;
		}

		return $page_type->get_property( $slug );
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
