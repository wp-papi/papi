<?php

/**
 * Core class that implements a Papi page.
 */
abstract class Papi_Core_Page extends Papi_Container {

	/**
	 * The page type.
	 *
	 * @var string
	 */
	const TYPE = 'core';

	/**
	 * The WordPress post id if it exists.
	 *
	 * @var int
	 */
	public $id;

	/**
	 * The type of page.
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Get Papi property value.
	 *
	 * @param  string $slug
	 *
	 * @return mixed
	 */
	public function __get( $slug ) {
		return $this->get_value( $slug );
	}

	/**
	 * Get page type.
	 *
	 * @return string
	 */
	public function get_type() {
		return static::TYPE;
	}

	/**
	 * Get value from property.
	 *
	 * @param  string $slug
	 *
	 * @return mixed
	 */
	public function get_value( $slug ) {
		$slug  = unpapify( $slug );
		$value = papi_get_property_meta_value( $this->id, $slug, static::TYPE );
		return $this->convert( $slug, $value );
	}

	/**
	 * Convert property value with the property type converter.
	 *
	 * @param  string $slug
	 * @param  mixed  $value
	 *
	 * @return mixed
	 */
	protected function convert( $slug, $value ) {
		$property = $this->get_property( $slug );

		// If no property type is found, just return null.
		if ( ! papi_is_property( $property ) ) {
			return;
		}

		if ( papi_is_empty( $value ) ) {
			if ( ! papi_is_empty( $property->get_option( 'value' ) ) ) {
				return $property->get_option( 'value' );
			}

			return;
		}

		// A property need to know about the page.
		$property->set_page( $this );

		// Run load value method right after the value has been loaded from the database.
		$value = $property->load_value( $value, $slug, $this->id );

		$value = papi_filter_load_value(
			$property->type,
			$value,
			$slug,
			$this->id
		);

		// Format the value from the property class.
		$value = $property->format_value( $value, $slug, $this->id );

		// Only fired when not in admin.
		if ( ! is_admin() ) {
			$value = papi_filter_format_value(
				$property->type,
				$value,
				$slug,
				$this->id
			);
		}

		if ( is_array( $value ) ) {
			$value = array_filter( $value );
		}

		return $value;
	}

	/**
	 * Get page from factory.
	 *
	 * @param  int    $post_id
	 * @param  string $type
	 *
	 * @return mixed
	 */
	public static function factory( $post_id, $type = 'page' ) {
		$type         = $type === 'page' ? 'post' : $type;
		$class_suffix = '_' . ucfirst( $type ) . '_Page';
		$class_name   = 'Papi' . $class_suffix;

		if ( ! class_exists( $class_name ) ) {
			return;
		}

		$post_id = papi_get_post_id( $post_id );
		$page    = new $class_name( $post_id );

		if ( ! $page->valid() ) {
			return;
		}

		return $page;
	}

	/**
	 * Get property from page type.
	 *
	 * @param  string $slug
	 * @param  string $child_slug
	 *
	 * @return Papi_Property
	 */
	abstract public function get_property( $slug, $child_slug = '' );

	/**
	 * Check if it's a valid page.
	 *
	 * @return bool
	 */
	abstract public function valid();
}
