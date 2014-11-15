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
class Papi_Page_Type extends Papi_Page_Type_Meta {

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
	 * @param bool $register
	 */

	public function __construct( $file_path = '', $register = true ) {
		parent::__construct( $file_path );

		if ( $register && method_exists( $this, 'register' ) ) {
			$this->register();
		}
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
		list( $options, $properties ) = _papi_get_options_and_properties( $file_or_options, $properties );

		$post_type = _papi_get_wp_post_type();

		// Check so we have a post the to add the box to.
		if ( ! $this->has_post_type( $post_type ) ) {
			return null;
		}

		// Add post type to the options array.
		$options['post_type'] = $post_type;

		// Create a new box.
		new Papi_Admin_Meta_Box( $options, $properties );
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
	 * @param array $remove_post_type_support
	 *
	 * @since 1.0.0
	 */

	protected function remove( $remove_post_type_support = array() ) {
		$remove_post_type_support       = _papi_to_array( $remove_post_type_support );
		$this->remove_post_type_support = array_merge( $this->remove_post_type_support, $remove_post_type_support );
		add_action( 'init', array( $this, 'remove_post_type_support' ) );
	}

	/**
	 * Remove post type support action.
	 *
	 * @since 1.0.0
	 */

	public function remove_post_type_support() {
		$post_type = _papi_get_wp_post_type();

		if ( empty( $post_type ) ) {
			return;
		}

		foreach ( $this->remove_post_type_support as $post_type_support ) {
			remove_post_type_support( $post_type, $post_type_support );
		}
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
	 * @return array
	 */

	protected function tab( $file_or_options = array(), $properties = array() ) {
		return papi_tab( $file_or_options, $properties );
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
