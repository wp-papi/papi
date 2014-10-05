<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi Page Meta class.
 *
 * @package Papi
 * @version 1.0.0
 */
class Papi_Page_Type_Base {

	/**
	 * The name of the page type.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	public $name = '';

	/**
	 * The description of the page type.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	public $description = '';

	/**
	 * The page type thumbnail.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	public $thumbnail = '';

	/**
	 * The template of the page type.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	public $template = '';

	/**
	 * The post types to register the page type with.
	 *
	 * @var array
	 * @since 1.0.0
	 */

	public $post_types = array( 'page' );

	/**
	 * The page type. It's the name of the class.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	public $page_type = '';

	/**
	 * The file name of the page type file.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	public $file_name = '';

	/**
	 * The file path of the page type file.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	public $file_path = '';

	/**
	 * Capabilities list.
	 *
	 * @var array
	 * @since 1.0.0
	 */

	public $capabilities = array();

	/**
	 * Constructor.
	 * Load a page type by the file.
	 *
	 * @param string $file_path
	 *
	 * @since 1.0.0
	 */

	public function __construct( $file_path ) {
		// Try to load the file if the file path is empty.
		if ( empty( $file_path ) ) {
			$page_type = _papi_get_page_type_meta_value();
			$file_path = _papi_get_page_type_file( $page_type );
		}

		// Check so we have a file that exists.
		if ( ! is_string( $file_path ) || ! file_exists( $file_path ) || ! is_file( $file_path ) ) {
			return null;
		}

		// Load the file.
		$this->setup_file( $file_path );

		// Check so we have the page type meta array function.
		if ( ! method_exists( $this->page_type, 'page_type' ) ) {
			return;
		}

		// Setup page type meta data.
		$this->setup_meta_data();
	}

	/**
	 * Check so we have a name on the page type.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */

	public function has_name() {
		return ! empty( $this->name );
	}

	/**
	 * Is the user allowed to view this page type?
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */

	public function current_user_is_allowed() {
		foreach ( $this->capabilities as $capability ) {
			if ( ! current_user_can( $capability ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get page type image thumbnail.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */

	public function get_thumbnail() {
		if ( empty( $this->thumbnail ) ) {
			return _papi_page_type_default_thumbnail();
		}

		return $this->thumbnail;
	}

	/**
	 * Check if the given post is allowed to use the page type.
	 *
	 * @param string $post_type
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */

	protected function has_post_type( $post_type ) {
		return in_array( $post_type, $this->post_types );
	}

	/**
	 * Create a new instance of the page type file.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */

	public function new_class() {
		if ( ! class_exists( $this->page_type ) ) {
			require_once( $this->file_path );
		}

		return new $this->page_type;
	}

	/**
	 * Load the file and setup page type meta data.
	 *
	 * @param string $file_path
	 *
	 * @since 1.0.0
	 * @access private
	 */

	private function setup_file( $file_path ) {
		// Setup file and page type variables.
		$this->file_path = $file_path;
		$this->page_type = _papi_get_class_name( $this->file_path );
		$this->file_name = _papi_get_page_type_base_path( $this->file_path );

		// Try to load the page type class.
		if ( ! class_exists( $this->page_type ) ) {
			require_once( $this->file_path );
		}
	}

	/**
	 * Setup page type meta data.
	 *
	 * @since 1.0.0
	 * @access private
	 */

	private function setup_meta_data() {
		// Get page type meta data.
		$page_type_meta = call_user_func( $this->page_type . '::page_type' );

		// Filter all fields.
		$page_type_meta = $this->filter_page_type_fields( $page_type_meta );

		// Add each field as a variable.
		foreach ( $page_type_meta as $key => $value ) {
			$this->$key = $value;
		}

		if ( ! is_array( $this->post_types ) ) {
			$this->post_types = array( $this->post_types );
		}

		// Set a default value to post types array if we don't have a array or a empty array.
		if ( empty( $this->post_types ) ) {
			$this->post_types = array( 'page' );
		}
	}

	/**
	 * Filter page type fields. Some keys aren't allowed to use.
	 *
	 * @param array $arr
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	private function filter_page_type_fields( $arr = array() ) {
		$not_allowed = array( 'file_name', 'page_type' );

		return array_intersect_key( $arr, array_flip( array_diff( array_keys( $arr ), $not_allowed ) ) );
	}

}
