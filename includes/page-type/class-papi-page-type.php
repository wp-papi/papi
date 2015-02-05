<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi Page Type class.
 * All page types in the WordPress theme will extend this class.
 *
 * @package Papi
 * @since 1.0.0
 */

class Papi_Page_Type extends Papi_Page_Type_Meta {

	/**
	 * The array of meta boxes to register.
	 *
	 * @var array
	 * @since 1.0.0
	 */

	private $boxes = array();

	/**
	 * Load all boxes even if we aren't on a post type.
	 *
	 * @var bool
	 * @since 1.0.0
	 */

	private $load_boxes = false;

	/**
	 * Contains all register properties on this page.
	 * Will only contain root level properties.
	 *
	 * @var array
	 * @since 1.0.0
	 */

	private $properties = array();

	/**
	 * Array of post type supports to remove.
	 * By default remove `postcustom` which is the Custom fields metabox.
	 *
	 * @var array
	 * @since 1.0.0
	 */

	private $post_type_supports = array( 'custom-fields' );

	/**
	 * Load a page type by the file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_path
	 */

	public function __construct( $file_path = '' ) {
		parent::__construct( $file_path );
		add_action( 'init', array( $this, 'remove_post_type_support' ) );
	}

	/**
	 * Add new meta box with properties.
	 *
	 * @param mixed $file_or_options .
	 * @param array $properties
	 *
	 * @since 1.0.0
	 */

	protected function box( $file_or_options = array(), $properties = array() ) {
		if ( is_object( $file_or_options ) ) {
			$file_or_options = (array)$file_or_options;
		}

		if ( ! is_string( $file_or_options) && ! is_array( $file_or_options ) ) {
			return null;
		}

		list( $options, $properties ) = papi_get_options_and_properties( $file_or_options, $properties, true );

		$post_type = papi_get_wp_post_type();

		// Check so we have a post the to add the box to.
		if ( ! $this->load_boxes && ( empty( $post_type ) || ! $this->has_post_type( $post_type ) ) ) {
			return null;
		}

		// Add post type to the options array.
		$options['post_type'] = $post_type;

		if ( isset( $options['sort_order'] ) ) {
			$sort_order = intval( $options['sort_order'] );
		} else {
			$sort_order = papi_filter_settings_sort_order();
		}

		$title = isset( $options['title'] ) ? $options['title'] : '';

		array_push( $this->boxes, array( $options, $properties, 'sort_order' => $sort_order, 'title' => $title ) );
	}

	/**
	 * Get boxes from the page type.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	public function get_boxes() {
		if ( empty( $this->boxes ) ) {
			if ( ! method_exists( $this, 'register' ) ) {
				return null;
			}

			$this->load_boxes = true;

			$this->register();
		}

		return $this->boxes;
	}

	/**
	 * This function will setup all meta boxes.
	 *
	 * @since 1.0.0
	 */

	public function setup() {
		if ( ! method_exists( $this, 'register' ) ) {
			return null;
		}

		$this->register();

		$this->boxes = papi_sort_order( array_reverse( $this->boxes ) );

		foreach ( $this->boxes as $box ) {
			new Papi_Admin_Meta_Box( $box[0], $box[1] );
		}
	}

	/**
	 * Add new property to the page using array or rendering property template file.
	 *
	 * @param string|array $file_or_options
	 * @param array $values
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	protected function property( $file_or_options = array(), $values = array() ) {
		return papi_property( $file_or_options, $values );
	}

	/**
	 * Remove post type support. Runs once, on page load.
	 *
	 * @param array $post_type_supports
	 *
	 * @since 1.0.0
	 */

	protected function remove( $post_type_supports = array() ) {
		$this->post_type_supports = array_merge( $this->post_type_supports, papi_to_array( $post_type_supports ) );
	}

	/**
	 * Remove post type support action.
	 *
	 * @since 1.0.0
	 */

	public function remove_post_type_support() {
		$post_type = papi_get_wp_post_type();

		if ( empty( $post_type ) ) {
			return;
		}

		foreach ( $this->post_type_supports as $post_type_support ) {
			remove_post_type_support( $post_type, $post_type_support );
		}
	}

	/**
	 * Add a new tab.
	 *
	 * @param array $properties
	 *
	 * @since 1.0
	 *
	 * @return array
	 */

	protected function tab( $file_or_options = array(), $properties = array() ) {
		if ( ! is_string( $file_or_options) && ! is_array( $file_or_options ) ) {
			return null;
		}

		$tab = papi_tab( $file_or_options, $properties );

		// Tabs sometimes will be in $tab->options['options'] when you use a tab template in this method
		// and using the return value of papi_tab function is used.
		//
		// This should be fixed later, not a priority for now since this works.
		if ( is_object( $tab ) && isset( $tab->options ) && isset( $tab->options['options'] ) ) {
			$tab = (object)$tab->options;
		}

		return $tab;
	}

	/**
	 * Load template file.
	 *
	 * @param string $file
	 * @param array $values
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	protected function template( $file, $values = array() ) {
		return papi_template( $file, $values );
	}
}
