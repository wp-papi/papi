<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi Page Type Base class.
 *
 * @package Papi
 * @version 1.0.0
 */

class Papi_Page_Type_Base {

	/**
	 * The page type class name.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	public $_class_name = '';

	/**
	 * The file name of the page type file.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	public $_file_name = '';

	/**
	 * The file path of the page type file.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	public $_file_path = '';

	/**
	 * The instance of the page type.
	 *
	 * @var object
	 * @since 1.0.0
	 */

	protected static $instance;

	/**
	 * Get instance of the page type.
	 *
	 * @param mixed $instance
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */

	public static function instance ( $instance = null ) {
		if ( empty( $instance ) ) {
			return self::$instance;
		}

		self::$instance = $instance;
	}

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
			$file_path = _papi_get_file_path( $page_type );
		}

		if ( ! is_file( $file_path ) ) {
			return null;
		}

		$this->setup_file( $file_path );
		$this->setup_meta_data();
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 2.1
	 */

	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'papi' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 2.1
	 */

	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'papi' ), '1.0.0' );
	}

	/**
	 * Get the page type file name.
	 *
	 * @since 1.0.0.
	 *
	 * @return string
	 */

	public function get_filename () {
		return $this->_file_name;
	}

	/**
	 * Create a new instance of the page type file.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */

	public function new_class() {
		if ( ! class_exists( $this->_class_name ) ) {
			require_once $this->file_path;
		}

		return new $this->_class_name;
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
		$this->_file_path = $file_path;
		$this->_file_name = _papi_get_page_type_base_path( $this->_file_path );

		// Get the class name of the file.
		$this->_class_name = _papi_get_class_name( $this->_file_path );

		// Try to load the page type class.
		if ( ! class_exists( $this->_class_name ) ) {
			require_once $this->_file_path;
		}
	}

	/**
	 * Setup page type meta data.
	 *
	 * @since 1.0.0
	 * @access private
	 */

	private function setup_meta_data() {
		// Check so we have the page type meta array function.
		if ( ! method_exists( $this->_class_name, 'page_type' ) ) {
			return null;
		}

		foreach ( $this->page_type() as $key => $value ) {
			if ( substr( $key, 0, 1 ) === '_' ) {
				continue;
			}

			$this->$key = $value;
		}
	}
}
