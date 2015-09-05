<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Page Type Base class.
 *
 * @package Papi
 */
class Papi_Page_Type_Base {

	/**
	 * The meta method to call.
	 *
	 * @var string
	 */
	public $_meta_method = 'page_type';

	/**
	 * The page type class name.
	 *
	 * @var string
	 */
	private $_class_name = '';

	/**
	 * The file name of the page type file.
	 *
	 * @var string
	 */
	private $_file_name = '';

	/**
	 * The file path of the page type file.
	 *
	 * @var string
	 */
	private $_file_path = '';

	/**
	 * The page type identifier.
	 *
	 * @var string
	 */
	public $id = '';

	/**
	 * The constructor.
	 *
	 * Load a page type by the file.
	 *
	 * @param string $file_path
	 */
	public function __construct( $file_path ) {
		// Try to load the file if the file path is empty.
		if ( empty( $file_path ) ) {
			$page_type = papi_get_page_type_id();
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
	 * @return string
	 */
	public function get_class_name() {
		return $this->_class_name;
	}

	/**
	 * Get the page type file pat.h
	 *
	 * @return string
	 */
	public function get_file_path() {
		return $this->_file_path;
	}

	/**
	 * Get the page type identifier.
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
	 * @param  string $id
	 *
	 * @return bool
	 */
	public function match_id( $id ) {
		if ( strpos( $id, 'papi/' ) === 0 ) {
			$id = preg_replace( '/^papi\//', '', $id );
		}

		return $this->get_id() === $id;
	}

	/**
	 * Create a new instance of the page type file.
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
	 */
	private function setup_file( $file_path ) {
		$this->_file_path  = $file_path;
		$this->_file_name  = papi_get_page_type_base_path( $this->_file_path );
		$this->_class_name = papi_get_class_name( $this->_file_path );
	}

	/**
	 * Setup page type meta data.
	 */
	private function setup_meta_data() {
		if ( ! method_exists( $this->_class_name, $this->_meta_method ) ) {
			return;
		}

		foreach ( call_user_func( [$this, $this->_meta_method] ) as $key => $value ) {
			if ( substr( $key, 0, 1 ) === '_' ) {
				continue;
			}

			$this->$key = papi_esc_html( $value );
		}
	}
}
