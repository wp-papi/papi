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
	 * Properties meta values.
	 *
	 * @var array
	 */
	protected $meta_values = [];

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
	 * Get property meta value.
	 *
	 * @param  string $slug
	 *
	 * @return mixed
	 */
	public function get_property_meta_value( $slug ) {
		$slug = strtolower( $slug );

		if ( isset( $this->meta_values[$slug] ) ) {
			return $this->meta_values[$slug];
		}

		return papi_data_get( $this->id, $slug, $this->get_type() );
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
	 * Get value.
	 *
	 * @param  int    $id
	 * @param  string $slug
	 * @param  mixed  $default
	 * @param  string $type
	 *
	 * @return mixed
	 */
	public function get_value( $id = null, $slug = null, $default = null, $type = 'post' ) {
		if ( ! is_numeric( $id ) && is_string( $id ) ) {
			$type    = empty( $default ) ? $type : $default;
			$default = $slug;
			$slug    = $id;
			$id      = null;
		}

		$slug = strtolower( $slug );

		// Determine if we should use the cache or not.
		$cache = $this->get_property_option( $slug, 'cache', true );

		// Get the raw value from the cache.
		$raw_value = $cache ? papi_cache_get( $slug, $id, $type ) : false;

		// Load raw value if not cached.
		if ( $raw_value === null || $raw_value === false ) {
			$raw_value = $this->load_value( $slug );

			if ( papi_is_empty( $raw_value ) ) {
				return $default;
			}

			if ( $cache ) {
				papi_cache_set( $slug, $id, $raw_value, $type );
			} else {
				papi_cache_delete( $slug, $id, $type );
			}
		}

		if ( papi_is_empty( $raw_value ) ) {
			return $default;
		}

		// Format raw value.
		$value = $this->format_value( $slug, $raw_value );

		return papi_is_empty( $value ) ? $default : $value;
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
		$slug     = strtolower( unpapify( $slug ) );
		$property = $this->property( $slug );

		// If no property type is found, just return null.
		if ( ! papi_is_property( $property ) ) {
			return;
		}

		// Format the value from the property class.
		$value = $property->format_value( $value, $slug, $this->id );

		// Only fired when not in admin.
		if ( ! papi_is_admin() ) {
			$value = papi_filter_format_value(
				$property->type,
				$value,
				$slug,
				$this->id,
				papi_get_meta_type()
			);
		}

		// Modify value before it's return to the theme via a defined format callback on the property.
		if ( ! papi_is_admin() && is_callable( $property->format_cb ) ) {
			$value = call_user_func( $property->format_cb, $value );
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
			$property = $this->properties[$slug];
		} else {
			$property = $this->properties[$slug] = $this->get_property( $slug );
		}

		/**
		 * Modify property.
		 *
		 * @param  Papi_Core_Property $property
		 */
		return apply_filters( 'papi/get_property', $property );
	}

	/**
	 * Load property value from the property.
	 *
	 * @param  string $slug
	 *
	 * @return mixed
	 */
	public function load_value( $slug ) {
		$slug     = strtolower( unpapify( $slug ) );
		$property = $this->property( $slug );

		// If no property type is found, just return null.
		if ( ! papi_is_property( $property ) ) {
			return;
		}

		// Get raw property meta value.
		$value = $this->get_property_meta_value( $slug );

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
	 * Set property meta value.
	 *
	 * @param string $slug
	 * @param mixed  $value
	 */
	public function set_property_meta_value( $slug, $value ) {
		$this->meta_values[strtolower( $slug )] = $value;
	}

	/**
	 * Check if it's a valid store.
	 *
	 * @return bool
	 */
	abstract public function valid();
}
