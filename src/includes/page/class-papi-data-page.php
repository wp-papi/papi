<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Data Page.
 *
 * @package Papi
 */

abstract class Papi_Data_Page extends Papi_Container {

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

	public function __construct( $post_id = 0 ) {
		$this->id = intval( $post_id );
	}

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
	 * Get value from property.
	 *
	 * @param string $slug
	 *
	 * @return mixed
	 */

	public function get_value( $slug ) {
		$slug  = papi_remove_papi( $slug );
		$value = papi_property_get_meta( $this->id, $slug, $this->data_type );

		if ( papi_is_empty( $value ) ) {
			return;
		}

		return $this->convert(
			$slug,
			$value
		);
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

	protected function convert( $slug, $value ) {
		$property = $this->get_property( $slug );

		// If no property type is found, just return null.
		if ( $property instanceof Papi_Property === false ) {
			return;
		}

		// A property need to know about the data type.
		$property->set_page_options( [
			'data_type' => $this->data_type,
			'post_id'   => $this->id
		] );

		// Set property options so we can access them in load value or format value functions.
		//$property->set_options( $this->get_property_options( $slug ) );

		// Run load value method right after the value has been loaded from the database.
		$value = $property->load_value( $value, $slug, $this->id );

		if ( $this->data_type !== 'option' ) {
			$value = papi_filter_load_value( $property->get_type(), $value, $slug, $this->id );
		}

		// Format the value from the property class.
		$value = $property->format_value( $value, $slug, $this->id );

		if ( ! is_admin() || $this->data_type !== 'option' ) {
			$value = papi_filter_format_value( $property->get_type(), $value, $slug, $this->id );
		}

		if ( is_array( $value ) ) {
			$value = array_filter( $value );
		}

		return $value;
	}

	/**
	 * Get page from factory.
	 *
	 * @param int $post_id
	 * @param string $data_type
	 *
	 * @return mixed
	 */

	public static function factory( $post_id, $data_type = 'post' ) {
		if ( papi_is_option_page() ) {
			$data_type = 'option';
		}

		$class_suffix = '_' . ucfirst( $data_type ) . '_Page';
		$class_name   = 'Papi' . $class_suffix;

		if ( ! class_exists( $class_name ) ) {
			return;
		}

		$post_id = papi_get_post_id( $post_id );
		$page = new $class_name( $post_id );

		if ( ! $page->valid() ) {
			return;
		}

		return $page;
	}

	/**
	 * Get property options from page type.
	 *
	 * @param string $slug
	 *
	 * @return Papi_Property
	 */

	protected function get_property( $slug ) {
		$property = $this->get_property_from_page_type( $slug );

		if ( ! is_object( $property ) ) {
			return;
		}

		return $property;
	}

	/**
	 * Load property options from page type.
	 *
	 * @param string $slug
	 *
	 * @return object
	 */

	abstract protected function get_property_from_page_type( $slug );

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
