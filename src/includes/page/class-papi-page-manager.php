<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Page Manager.
 *
 * @package Papi
 */

abstract class Papi_Page_Manager {

	/**
	 * The WordPress post id if it exists.
	 *
	 * @var int
	 */

	public $id;

	/**
	 * Data type to describe which
	 * type of page data is it.
	 *
	 * @var string
	 */

	protected $data_type;

	/**
	 * Page data types.
	 *
	 * @var array
	 */

	private $data_types = ['option', 'post'];

	/**
	 * Empty constructor.
	 */

	public function __construct() {}

	/**
	 * Get Papi property value.
	 *
	 * @param string $slug
	 *
	 * @return mixed
	 */

	public function __get( $slug ) {
		return $this->get_value( $slug );
	}

	/**
	 * Convert property value with the property type converter.
	 *
	 * @param string $slug
	 * @param string $type
	 * @param mixed $value
	 *
	 * @return mixed
	 */

	protected function convert( $slug, $type, $value ) {
		$property = papi_get_property_type( strval( $type ) );

		// If no property type is found, just return the value.
		if ( empty( $type ) || ( $property instanceof Papi_Property ) === false ) {
			return $value;
		}

		// Set property options so we can access them in load value or format value functions.
		$property->set_options( $this->get_property_options( $slug ) );

		// Run a `load_value` right after the value has been loaded from the database.
		$value = $property->load_value( $value, $slug, $this->id );

		// Apply a filter so this can be changed from the theme for specified property type.
		$value = papi_filter_load_value( $type, $value, $slug, $this->id );

		// Format the value from the property class.
		$value = $property->format_value( $value, $slug, $this->id );

		if ( is_admin() ) {
			return $value;
		}

		// Only apply `format_value` filter so this can be changed from the theme for specified property type.
		return papi_filter_format_value( $type, $value, $slug, $this->id );
	}

	/**
	 * Get property options from admin data.
	 *
	 * @param string $slug
	 *
	 * @return Papi_Property
	 */

	protected function get_property_options( $slug ) {
		if ( ! isset( $this->admin_data['property'] ) ) {
			$property = $this->load_property_options_from_page_type( $slug );

			if ( empty( $property ) ) {
				return;
			}

			$this->set_admin_data( [
				'property' => Papi_Property::create( $property )
			] );
		}

		return $this->admin_data['property']->get_options();
	}

	/**
	 * Get Papi Property value.
	 *
	 * @param string $slug
	 *
	 * @return mixed
	 */

	abstract protected function get_value( $slug );

	/**
	 * Load property options from page type.
	 *
	 * @param string $slug
	 *
	 * @return object
	 */

	protected function load_property_options_from_page_type( $slug ) {
		$page_type_id = papi_get_page_type_meta_value( $this->id );
		$page_type    = papi_get_page_type_by_id( $page_type_id );

		if ( ! is_object( $page_type ) || ! ( $page_type instanceof Papi_Page_Type ) ) {
			return;
		}

		return $page_type->get_property( $slug );
	}

	/**
	 * Set admin data.
	 *
	 * @param array $admin_data
	 */

	public function set_admin_data( $admin_data = [] ) {
		if ( ! is_array( $admin_data ) || empty( $admin_data ) ) {
			return;
		}

		$this->admin_data = $admin_data;
	}

	/**
	 * Check if it's a valid page.
	 *
	 * @return bool
	 */

	abstract public function valid();

	/**
	 * Check if the page has a valid data type.
	 *
	 * @return bool
	 */

	protected function valid_data_type() {
		return in_array( $this->data_type, $this->data_types );
	}

}
