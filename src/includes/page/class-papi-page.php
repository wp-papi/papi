<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Page.
 *
 * @package Papi
 * @since 1.0.0
 */

class Papi_Page {

	/**
	 * Admin data array.
	 * Used to store current property in WordPress admin.
	 *
	 * @var array
	 * @since 1.3.0
	 */

	private $admin_data;

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
	 * @var Papi_Page_Type
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
		$id              = papi_get_page_type_meta_value( $this->id );
		$this->page_type = papi_get_page_type_by_id( $id );
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

	/**
	 * Convert property value with the property type converter.
	 *
	 * @param string $slug
	 * @param string $type
	 * @param mixed $value
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */

	private function convert( $slug, $type, $value ) {
		$property = papi_get_property_type( strval( $type ) );

		// If no property type is found, just return the value.
		if ( empty( $type ) || ( $property instanceof Papi_Property ) === false ) {
			return $value;
		}

		// Set property options so we can access them in load value or format value functions.
		$property->set_options( $this->get_property_options( $slug, $type ) );

		// Run a `load_value` right after the value has been loaded from the database.
		$value = $property->load_value( $value, $slug, $this->id );

		// Apply a filter so this can be changed from the theme for specified property type.
		$value = papi_filter_load_value( $type, $value, $slug, $this->id );

		// Format the value from the property class.
		$value = $property->format_value( $value, $slug, $this->id );

		// Apply a filter so this can be changed from the theme for specified property type.
		return papi_filter_format_value( $type, $value, $slug, $this->id );
	}

	/**
	 * Get the page type object of the page.
	 *
	 * @since 1.0.0
	 *
	 * @return Papi_Page_Type
	 */

	public function get_page_type() {
		return $this->page_type;
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
	 * Get property options from admin data.
	 *
	 * @param string $slug
	 * @param string $db_property_type
	 * @since 1.3.0
	 *
	 * @return Papi_Property
	 */

	private function get_property_options( $slug, $db_property_type ) {
		if ( ! isset( $this->admin_data['property'] ) ) {
			$property = $this->load_property_options_from_page_type( $slug );

			if ( empty( $property ) ) {
				return;
			}

			$this->set_admin_data( array(
				'property' => Papi_Property::create( $property )
			) );
		}

		if ( ! method_exists( $this->admin_data['property'], 'get_options' ) ) {
			return;
		}

		$options = $this->admin_data['property']->get_options();


		if ( $options->type !== $db_property_type ) {
			$options->type = $db_property_type;
		}

		return $options;
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
	 * Get Papi Property value.
	 *
	 * @param string $slug
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */

	public function get_value( $slug ) {
		$property_value      = get_post_meta( $this->id, $slug, true );
		$property_type_key   = papi_f( papi_get_property_type_key( $slug ) );
		$property_type_value = get_post_meta( $this->id, $property_type_key, true );

		if ( papi_is_empty( $property_value ) || empty( $property_type_value ) ) {
			return;
		}

		// The convert takes a array as argument so let's make one.
		$property_value = $this->convert(
			$slug,
			$property_type_value,
			$property_value
		);

		if ( is_array( $property_value ) ) {
			$property_value = array_filter( $property_value );
		}

		return $property_value;
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
	 * Load property options from page type.
	 *
	 * @param string $slug
	 * @since 1.3.0
	 *
	 * @return object
	 */

	private function load_property_options_from_page_type( $slug ) {
		$page_type_id = papi_get_page_type_meta_value();
		$page_type    = papi_get_page_type_by_id( $page_type_id );

		if ( ! is_object( $page_type ) || ( $page_type instanceof Papi_Page_Type ) === false ) {
			return;
		}

		$boxes = $page_type->get_boxes();
		$property_options = array();

		if ( empty( $boxes ) ) {
			return;
		}

		foreach ( $boxes as $box ) {
			if ( ! isset( $box[1] ) ) {
				continue;
			}

			foreach ( $box[1] as $property ) {
				if ( papi_remove_papi( $property->slug ) === $slug ) {
					$property_options = $property;
					break;
				}
			}
		}

		return $property_options;
	}

	/**
	 * Set admin data.
	 *
	 * @param array $admin_data
	 * @since 1.3.0
	 */

	public function set_admin_data( $admin_data = array() ) {
		if ( ! is_array( $admin_data ) || empty( $admin_data ) ) {
			return;
		}

		$this->admin_data = $admin_data;
	}

}
