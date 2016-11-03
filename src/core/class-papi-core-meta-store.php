<?php

/**
 * Core class that implements a Papi meta store.
 */
abstract class Papi_Core_Meta_Store {

	/**
	 * The WordPress meta id if it exists.
	 *
	 * @var int
	 */
	public $id;

	/**
	 * Current properties.
	 *
	 * @var array
	 */
	protected $properties = [];

	/**
	 * The meta type.
	 *
	 * @var string
	 */
	protected $type = 'meta';

	/**
	 * The type class.
	 *
	 * @var Papi_Core_Type
	 */
	protected $type_class;

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
	 * Get meta type.
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Get type class.
	 *
	 * @return Papi_Core_Type
	 */
	public function get_type_class() {
		return $this->type_class;
	}

	/**
	 * Get value, uncached.
	 *
	 * @param  string $slug
	 *
	 * @return mixed
	 */
	public function get_value( $slug ) {
		$value = $this->load_value( $slug );

		return $this->format_value( $slug, $value );
	}

	/**
	 * Format property value from the property.
	 *
	 * @param  string $slug
	 * @param  mixed  $value
	 *
	 * @return mixed
	 */
	public function format_value( $slug, $value ) {
		$slug     = unpapify( $slug );
		$property = $this->property( $slug );

		// If no property type is found, just return null.
		if ( ! papi_is_property( $property ) ) {
			return;
		}

		// Format the value from the property class.
		$value = $property->format_value( $value, $slug, $this->id );

		// Only fired when not in admin.
		if ( ! is_admin() ) {
			$value = papi_filter_format_value(
				$property->type,
				$value,
				$slug,
				$this->id,
				papi_get_meta_type()
			);
		}

		// Remove empty values from arrays.
		if ( is_array( $value ) ) {
			foreach ( $value as $index => $val ) {
				if ( papi_is_empty( $val ) ) {
					unset( $value[$index] );
				}
			}
		}

		return $value;
	}

	/**
	 * Get current property.
	 *
	 * @param  string $slug
	 *
	 * @return Papi_Core_Property
	 */
	protected function property( $slug = '' ) {
		if ( isset( $this->properties[$slug] ) && papi_is_property( $this->properties[$slug] ) ) {
			return $this->properties[$slug];
		}

		$this->properties[$slug] = $this->get_property( $slug );

		return $this->properties[$slug];
	}

	/**
	 * Load property value from the property.
	 *
	 * @param  string $slug
	 *
	 * @return mixed
	 */
	public function load_value( $slug ) {
		$slug     = unpapify( $slug );
		$property = $this->property( $slug );

		// If no property type is found, just return null.
		if ( ! papi_is_property( $property ) ) {
			return;
		}

		// Get raw property meta value.
		$value = papi_get_property_meta_value( $this->id, $slug, $this->get_type() );

		// Prepare load value, when you have `overwrite => true`
		// this value will not exist in the database and that's
		// why we need to prepare (change) the value.
		$value = $this->prepare_load_value( $property, $value );

		// Bail if value is empty and option value is empty.
		if ( papi_is_empty( $value ) ) {
			if ( ! papi_is_empty( $property->get_option( 'value' ) ) ) {
				return $property->get_option( 'value' );
			}

			return;
		}

		// A property need to know about the store.
		$this->property( $slug )->set_store( $this );

		// Run load value method right after the value has been loaded from the database.
		$value = $property->load_value( $value, $slug, $this->id );
		$value = papi_filter_load_value(
			$property->type,
			$value,
			$slug,
			$this->id,
			papi_get_meta_type()
		);

		// Remove empty values from arrays.
		if ( is_array( $value ) ) {
			foreach ( $value as $index => $val ) {
				if ( papi_is_empty( $val ) ) {
					unset( $value[$index] );
				}
			}
		}

		return $value;
	}

	/**
	 * Get store from factory.
	 *
	 * @param  int    $post_id
	 * @param  string $type
	 *
	 * @return mixed
	 */
	public static function factory( $post_id, $type = 'post' ) {
		$type         = papi_get_meta_type( $type );
		$class_suffix = '_' . ucfirst( $type ) . '_Store';
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
	 * Get property from entry type.
	 *
	 * @param  string $slug
	 * @param  string $child_slug
	 *
	 * @return Papi_Core_Property
	 */
	abstract public function get_property( $slug, $child_slug = '' );

	/**
	 * Get property option or default value.
	 *
	 * @param  string $slug
	 * @param  string $option
	 * @param  mixed  $default
	 *
	 * @return bool
	 */
	public function get_property_option( $slug, $option, $default = null ) {
		$slug     = unpapify( $slug );
		$property = $this->property( $slug );

		// If no property type is found, return default
		// value since we don't have a property.
		if ( ! papi_is_property( $property ) ) {
			return $default;
		}

		$value = $property->get_option( $option );

		if ( papi_is_empty( $value ) ) {
			return $default;
		}

		return $value;
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
		return $value;
	}

	/**
	 * Prepare property before returning it.
	 *
	 * @param  Papi_Core_Property $property
	 *
	 * @return Papi_Core_Property
	 */
	protected function prepare_property( Papi_Core_Property $property ) {
		$property->set_store( $this );

		return $property;
	}

	/**
	 * Check if it's a valid store.
	 *
	 * @return bool
	 */
	abstract public function valid();
}
