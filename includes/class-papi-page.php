<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi Page.
 *
 * @package Papi
 * @version 1.0.0
 */

class Papi_Page {

	/**
	 * The WordPress post id.
	 *
	 * @var int
	 * @since 1.0.0
	 */

	public $id;

	/**
	 * The WordPress post.
	 *
	 * @var object
	 * @since 1.0.0
	 */

	private $post;

	/**
	 * The Page type.
	 *
	 * @var object.
	 * @since 1.0.0
	 */

	private $page_type;

	/**
	 * Create a new instance of the class.
	 *
	 * @param int $post_id
	 *
	 * @since 1.0.0
	 */

	public function __construct( $post_id = 0 ) {
		$this->id   = $post_id;
		$this->post = get_post( $this->id );

		// Load page type object.
		$path            = _papi_get_file_path( _papi_get_page_type_meta_value( $this->id ) );
		$this->page_type = _papi_get_page_type( $path );
	}

	/**
	 * Get Papi Property value.
	 *
	 * @param string $slug
	 * @param bool $admin
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */

	public function get_value( $slug, $admin = false ) {
		$property_value      = get_post_meta( $this->id, $slug, true );
		$property_type_key   = _papi_f( _papi_get_property_type_key( $slug ) );
		$property_type_value = get_post_meta( $this->id, $property_type_key, true );

		if ( empty( $property_value ) || empty( $property_type_value ) ) {
			return null;
		}

		// The convert takes a array as argument so let's make one.
		$property_value = $this->convert( array(
			'admin' => $admin,
			'slug'  => $slug,
			'type'  => $property_type_value,
			'value' => $property_value
		) );

		if ( is_array( $property_value ) ) {
			$property_value = array_filter( $property_value );
		}

		return $property_value;
	}

	/**
	 * Convert property value with the property type converter.
	 *
	 * @param array $property
	 *
	 * @since 1.0.0
	 *
	 * @return mixed|null
	 */

	private function convert( $property ) {
		if ( !isset( $property['value'] ) || !isset( $property['type'] ) ) {
			return null;
		}

		$type          = strval( $property['type'] );
		$property_type = _papi_get_property_type( $type );

		// If no property type is found, just return the value.
		if ( empty( $property_type ) ) {
			return $property['value'];
		}

		// Run a `load_value` right after the value has been loaded from the database.
		$property['value'] = $property_type->load_value( $property['value'], $property['slug'], $this->id );

		// Apply a filter so this can be changed from the theme for specified property type.
		$property['value'] = _papi_load_value( $type, $property['value'], $property['slug'], $this->id );

		// Format the value from the property class.
		$property['value'] = $property_type->format_value( $property['value'], $property['slug'], $this->id, $property['admin'] );

		// Apply a filter so this can be changed from the theme for specified property type.
		$property['value'] = _papi_format_value(  $type, $property['value'], $property['slug'], $this->id );

		return $property['value'];
	}

	/**
	 * Check if the page has the post object and that it's not null
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */

	public function has_post() {
		return $this->post != null;
	}

	/**
	 * Get the WordPress post object.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */

	public function get_post() {
		return $this->post;
	}

	/**
	 * Get the permalink for the page.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */

	public function get_permalink() {
		return get_permalink( $this->id );
	}

	/**
	 * Get the post status of a page.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */

	public function get_status() {
		return get_post_status( $this->id );
	}

	/**
	 * Get Papi property value.
	 *
	 * @param string $slug
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */

	public function __get( $slug ) {
		return $this->get_value( $slug );
	}

}
