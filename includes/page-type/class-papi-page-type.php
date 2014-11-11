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
	 * Add new meta box with properties.
	 *
	 * @param mixed $file_or_options .
	 * @param array $properties
	 *
	 * @since 1.0.0
	 */

	protected function box( $file_or_options = array(), $properties = array() ) {
		$options = array();

		if ( is_array( $file_or_options ) ) {
			if ( empty( $properties ) ) {
				// The first parameter is the options array.
				$options['title'] = isset( $file_or_options['title'] ) ? $file_or_options['title'] : '';
				$properties 	  = $file_or_options;
			} else {
				$options = array_merge( $options, $file_or_options );
			}
		} else if ( is_string( $file_or_options ) ) {
			// If it's a template we need to load it the right way
			// and add all properties the right way.
			if ( _papi_is_ext( $file_or_options, 'php' ) ) {
				$values = $properties;
				$template = papi_template( $file_or_options, $values );

				// Create the property array from existing property array or a new.
				if ( isset ( $template['properties'] ) ) {
					if ( is_array( $template['properties'] ) ) {
						$properties = $template['properties'];
					} else {
						$properties = array();
					}
					unset( $template['properties'] );
				} else {
					$properties = array();
				}

				$options = $template;

				// Add all non string keys to the properties array
				foreach ( $options as $key => $value ) {
					if ( ! is_string( $key ) ) {
						$properties[] = $value;
						unset( $options[$key] );
					}
				}
			} else {
				// The first parameter is used as the title.
				$options['title'] = $file_or_options;
			}
		}

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

	protected function tab( $file_or_options = array(), $properties = array() ) {
		$options = array();

		if ( is_array( $file_or_options ) ) {
			$options = array_merge( $options, $file_or_options );
		} else if ( is_string( $file_or_options ) ) {
			// If it's a template we need to load it the right way
			// and add all properties the right way.
			if ( _papi_is_ext( $file_or_options, 'php' ) ) {
				$values = $properties;
				$template = papi_template( $file_or_options, $values );

				// Create the property array from existing property array or a new.
				if ( isset ( $template['properties'] ) ) {
					if ( is_array( $template['properties'] ) ) {
						$properties = $template['properties'];
					} else {
						$properties = array();
					}
					unset( $template['properties'] );
				} else {
					$properties = array();
				}

				$options = $template;

				// Add all non string keys to the properties array
				foreach ( $options as $key => $value ) {
					if ( ! is_string( $key ) ) {
						$properties[] = $value;
						unset( $options[$key] );
					}
				}
			} else {
				// The first parameter is used as the title.
				$options['title'] = $file_or_options;
			}
		}

		// The tab key is important, it's says that we should render a tab meta box.
		return (object) array(
			'options'    => $options,
			'properties' => $properties,
			'tab'        => true
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
