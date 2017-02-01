<?php

/**
 * Post store implementation of Papi meta store.
 */
class Papi_Post_Store extends Papi_Core_Meta_Store {

	/**
	 * The WordPress post.
	 *
	 * @var object
	 */
	protected $post;

	/**
	 * The meta type.
	 *
	 * @var string
	 */
	protected $type = 'post';

	/**
	 * The constructor.
	 *
	 * @param int $id
	 */
	public function __construct( $id = 0 ) {
		$this->id         = papi_get_post_id( $id );
		$this->post       = get_post( $this->id );
		$id               = papi_get_page_type_id( $this->id );
		$this->type_class = papi_get_entry_type_by_id( $id );
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
	 * @return WP_Post
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
	 * @param  string $slug
	 * @param  string $child_slug
	 *
	 * @return null|Papi_Core_Property
	 */
	public function get_property( $slug, $child_slug = '' ) {
		$page_type_id = papi_get_page_type_id( $this->id );
		$page_type    = papi_get_entry_type_by_id( $page_type_id );

		if ( $page_type instanceof Papi_Page_Type === false ) {
			return;
		}

		if ( $property = $page_type->get_property( $slug, $child_slug ) ) {
			return $this->prepare_property( $property );
		}
	}

	/**
	 * Prepare load value.
	 *
	 * @param  Papi_Core_Property $property
	 * @param  mixed              $value
	 *
	 * @return mixed
	 */
	protected function prepare_load_value( Papi_Core_Property $property, $value ) {
		if ( $property->overwrite ) {
			// Clear post cache to solve issue with cached post objects
			// when selecting post field.
			clean_post_cache( $this->id );

			$slug    = $property->get_slug( true );
			$context = papi_is_admin() ? 'edit' : 'display';
			$value   = get_post_field( $slug, $this->id, $context );
		}

		return $value;
	}

	/**
	 * Check if the page has the post object and that it's not null.
	 *
	 * @return bool
	 */
	public function valid() {
		return ! is_null( $this->post );
	}
}
