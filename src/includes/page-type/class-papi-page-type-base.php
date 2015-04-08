<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Page Type Base class.
 *
 * @package Papi
 * @since 1.0.0
 */

class Papi_Page_Type_Base {

	/**
	 * The meta method to call.
	 *
	 * @var string
	 * @since 1.2.0
	 */

	public $_meta_method = 'page_type';

	/**
	 * The page type class name.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	private $_class_name = '';

	/**
	 * The file name of the page type file.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	private $_file_name = '';

	/**
	 * The file path of the page type file.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	private $_file_path = '';

	/**
	 * The page type identifier.
	 *
	 * @var string
	 * @since 1.3.0
	 */

	private $id = '';

	/**
	 * Constructor.
	 * Load a page type by the file.
	 *
	 * @param string $file_path
	 * @since 1.0.0
	 */

	public function __construct( $file_path ) {
		// Try to load the file if the file path is empty.
		if ( empty( $file_path ) ) {
			$page_type = papi_get_page_type_meta_value();
			$file_path = papi_get_file_path( $page_type );
		}

		if ( is_file( $file_path ) ) {
			$this->setup_file( $file_path );
			$this->setup_meta_data();
		}
	}

	/**
	 * Get the page type class name with namespace if exists.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */

	public function get_class_name() {
		return $this->_class_name;
	}

	/**
	 * Get the page type file pat.h
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */

	public function get_file_path() {
		return $this->_file_path;
	}

	/**
	 * Get the page type identifier.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */

	public function get_id() {
		if ( ! empty( $this->id ) ) {
			return $this->id;
		}

		return $this->_file_name;
	}

	/**
	 * Check if the the given identifier match the page type identifier.
	 *
	 * @param string $id
	 * @since 1.3.0
	 *
	 * @return bool
	 */

	public function match_id( $id ) {
		return $this->get_id() === $id;
	}

	/**
	 * Create a new instance of the page type file.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */

	public function new_class() {
		if ( empty( $this->_file_path ) ) {
			return;
		}

		return new $this->_class_name;
	}

	/**
	 * Load the file and setup page type meta data.
	 *
	 * @param string $file_path
	 * @since 1.0.0
	 */

	private function setup_file( $file_path ) {
		$this->_file_path = $file_path;
		$this->_file_name = papi_get_page_type_base_path( $this->_file_path );

		// Get the class name of the file.
		$this->_class_name = papi_get_class_name( $this->_file_path );
	}

	/**
	 * Setup page type meta data.
	 *
	 * @since 1.0.0
	 */

	private function setup_meta_data() {
		// Check so we have the page type meta method.
		if ( ! method_exists( $this->_class_name, $this->_meta_method ) ) {
			return;
		}

		foreach ( call_user_func( array( $this, $this->_meta_method ) ) as $key => $value ) {
			if ( substr( $key, 0, 1 ) === '_' ) {
				continue;
			}

			$this->$key = papi_esc_html( $value );
		}
	}
}
