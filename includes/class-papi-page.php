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
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */

	private function get_value( $slug ) {
		$property_value = get_post_meta( $this->id, $slug, true );

		if ( is_null( $property_value ) ) {
			return null;
		}

		$property_type_key   = _papi_f( _papi_get_property_type_key( $slug ) );
		$property_type_value = get_post_meta( $this->id, $property_type_key, true );

		if ( is_null( $property_type_value ) ) {
			return null;
		}

		// The convert takes a array as argument so let's make one.
		if ( ! is_array( $property_value ) ) {
			return $this->convert( array(
				'slug'  => $slug,
				'type'  => $property_type_value,
				'value' => $property_value
			) );
		}

		$convert = false;

		// Property List has array with properties.
		// Remove `papi_` key and property key.

		foreach ( $property_value as $ki => $vi ) {
			if ( is_array( $property_value[ $ki ] ) ) {
				foreach ( $property_value[ $ki ] as $k => $v ) {
					if ( _papi_is_property_type_key( $k ) ) {
						continue;
					} else {
						if ( empty( $vi ) ) {
							$item_slug = '';
						} else {
							foreach ( $vi as $vik => $viv ) {
								if ( _papi_is_property_type_key( $vik ) ) {
									continue;
								}

								$item_slug = $vik;
							}
						}

						$ptk                         = _papi_get_property_type_key( $k );
						$property_value[ $ki ] = $this->convert( array(
							'slug'  => $slug . '.' . $item_slug,
							'type'  => $property_value[ $ki ][ $ptk ],
							'value' => $v
						) );
					}
				}
			} else {
				$convert = true;
				break;
			}
		}

		// Convert non property list arrays.
		if ( $convert ) {
			$property_value = $this->convert( array(
				'slug'  => $slug,
				'type'  => $property_type_value,
				'value' => $property_value
			) );
		}

		return array_filter( $property_value );
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
		if ( ! is_array( $property ) ) {
			return $property;
		}

		// Try to convert the property value with a property type.
		if ( isset( $property['value'] ) && isset( $property['type'] ) ) {
			// Get the property type.
			$type          = strval( $property['type'] );
			$property_type = _papi_get_property_type( $type );

			// If no property type is found, just return the value.
			if ( is_null( $property_type ) ) {
				return $property['value'];
			}

			// Run a `load_value` right after the value has been loaded from the database.
			$property['value'] = $property_type->load_value( $property['value'], $property['slug'], $this->id );

			// Apply a filter so this can be changed from the theme for specified property type.
			// Example: "papi/load_value/string"
			$property['value'] = _papi_load_value( $type, $property['value'], $property['slug'], $this->id );

			// Format the value from the property class.
			$property['value'] = $property_type->format_value( $property['value'], $property['slug'], $this->id );

			// Apply a filter so this can be changed from the theme for specified property type.
			// Example: "papi/format_value/string"
			$property['value'] = _papi_format_value(  $type, $property['value'], $property['slug'], $this->id );
		}

		// If we only have the value, let's return that.
		if ( isset( $property['value'] ) ) {
			return $property['value'];
		}

		return $property;
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
