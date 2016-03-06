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
	 * Get value from property.
	 *
	 * @param  string $slug
	 *
	 * @return mixed
	 */
	public function get_value( $slug ) {
		$slug  = unpapify( $slug );
		$value = papi_get_property_meta_value( $this->id, $slug, $this->get_type() );

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

		// Prepare convert value, when you have `overwrite => true`
		// this value will not exist in the database and that's
		// why we need to prepare (change) the value.
		$value = $this->prepare_convert_value( $property, $value );

		if ( papi_is_empty( $value ) ) {
			if ( ! papi_is_empty( $property->get_option( 'value' ) ) ) {
				return $property->get_option( 'value' );
			}

			return;
		}

		// A property need to know about the store.
		$property->set_store( $this );

		// Run load value method right after the value has been loaded from the database.
		$value = $property->load_value( $value, $slug, $this->id );
		$value = papi_filter_load_value(
			$property->type,
			$value,
			$slug,
			$this->id,
			papi_get_meta_type()
		);

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

		if ( is_array( $value ) ) {
			$value = array_filter( $value );
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
	 * Prepare convert value.
	 *
	 * @param  Papi_Core_Property $property
	 * @param  mixed              $value
	 *
	 * @retrun mixed
	 */
	protected function prepare_convert_value( Papi_Core_Property $property, $value ) {
		return $value;
	}

	/**
	 * Prepare property before returning it.
	 *
	 * @param  Papi_Core_Property $property
	 *
	 * @return Papi_Core_Property|null
	 */
	protected function prepare_property( $property ) {
		if ( papi_is_property( $property ) ) {
			$property->set_store( $this );
		}

		return $property;
	}

	/**
	 * Check if it's a valid store.
	 *
	 * @return bool
	 */
	abstract public function valid();
}
