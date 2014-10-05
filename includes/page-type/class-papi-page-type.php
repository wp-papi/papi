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
 * @version 1.0.0
 */
class Papi_Page_Type extends Papi_Page_Type_Base {

	/**
	 * The meta box instance.
	 *
	 * @var Papi_Admin_Meta_Box
	 * @since 1.0.0
	 */

	private $box;

	/**
	 * Contains all register properties on this page.
	 * Will only contain root level properties.
	 *
	 * @var array
	 * @since 1.0.0
	 */

	private $properties = array();

	/**
	 * Remove post type support array.
	 *
	 * @var array
	 * @since 1.0.0
	 */

	private $remove_post_type_support = array();

	/**
	 * Load a page type by the file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_path
	 */

	public function __construct( $file_path = '' ) {
		parent::__construct( $file_path );
		if ( method_exists( $this, 'register' ) ) {
			$this->register();
		}
	}

	/**
	 * Remove post type support action.
	 *
	 * @since 1.0.0
	 */

	public function remove_post_type_support() {
		// Get post type.
		$post_type = _papi_get_wp_post_type();

		// Can't proceed without a post type.
		if ( empty( $post_type ) || is_null( $post_type ) ) {
			return;
		}

		// Loop through all post type support to remove.
		foreach ( $this->remove_post_type_support as $post_type_support ) {
			remove_post_type_support( $post_type, $post_type_support );
		}
	}

	/**
	 * Create a new instance of the page type file.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */

	/*public function new_class () {
		if (!class_exists($this->page_type)) {
		  require_once($this->file_path);
		}
		return new $this->page_type;
	  }*/

	/**
	 * Add new meta box with properties.
	 *
	 * @param mixed $title .
	 * @param array $options
	 * @param array $properties
	 *
	 * @since 1.0.0
	 */

	protected function box( $title = '', $options = array(), $properties = null ) {
		// Options is optional value.
		if ( empty( $properties ) ) {
			if (empty($options) && is_array($title)) {
				$properties = $title;
				$title = $properties['title'];
			} else {
				$properties = $options;
			}
			$options    = array();
		}

		// Can current user view this box?
		if ( isset( $options['capabilities'] ) && ! _papi_current_user_is_allowed( $options['capabilities'] ) ) {
			return;
		}

		// Move title into options.
		if ( ! isset( $options['title'] ) ) {
			$options['title'] = $title;
		}

		$post_type = _papi_get_wp_post_type();

		if ( ! $this->has_post_type( $post_type ) ) {
			return;
		}

		// Add post type to the options array.
		$options['post_type'] = $post_type;

		// Create a new box.
		$this->box = new Papi_Admin_Meta_Box( $options, $properties );
	}

	/**
	 * Add new property to the page.
	 *
	 * @param array $options
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	protected function property( $options = array() ) {
		$options = _papi_get_property_options( $options );

		if ( is_array( $options ) ) {
			$this->properties = array_merge( $this->properties, $options );
		} else if ( ! $options->disabled ) {
			$this->properties[] = $options;
		}

		return $options;
	}

	/**
	 * Add a new tab.
	 *
	 * @param string $title
	 * @param array $options
	 * @param array $properties
	 *
	 * @since 1.0
	 *
	 * @return object
	 */

	protected function tab( $title, $options = array(), $properties = null ) {
		if ( ! isset( $properties ) ) {
			$properties = $options;
			$options    = array();
		}

		if ( ! is_array( $options ) ) {
			$options = array();
		}

		// Default options values.
		$defaults = array(
			'sort_order'   => null,
			'capabilities' => array()
		);

		$options = array_merge( $defaults, $options );
		$options = (object) $options;

		// Return a tab object.
		// Sort order key has to be on the root level since the sorter don't go to deep.
		return (object) array(
			'title'      => $title,
			'tab'        => true,
			'options'    => $options,
			'sort_order' => $options->sort_order,
			'properties' => $properties
		);
	}

	/**
	 * Remove post type support. Runs once, on page load.
	 *
	 * @param array $remove_post_type_support
	 *
	 * @since 1.0.0
	 */

	protected function remove( $remove_post_type_support = array() ) {
		$remove_post_type_support       = _papi_string_array( $remove_post_type_support );
		$this->remove_post_type_support = array_merge( $this->remove_post_type_support, $remove_post_type_support );
		add_action( 'init', array( $this, 'remove_post_type_support' ) );
	}

}
