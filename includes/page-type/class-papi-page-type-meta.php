<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi Page Type Meta class.
 *
 * @package Papi
 * @version 1.0.0
 */

class Papi_Page_Type_Meta extends Papi_Page_Type_Base {

	/**
	 * Capabilities list.
	 *
	 * @var array
	 * @since 1.0.0
	 */

	public $capabilities = array();

	/**
	 * The description of the page type.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	public $description = '';

	/**
	 * The name of the page type.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	public $name = '';

	/**
	 * The page types that lives under this page type.
	 *
	 * @var array
	 * @since 1.0.0
	 */

	public $page_types = array();

	/**
	 * The post types to register the page type with.
	 *
	 * @var array
	 * @since 1.0.0
	 */

	public $post_type = array( 'page' );

	/**
	 * The sort order of the page type.
	 *
	 * @var int
	 * @since 1.0.0
	 */

	public $sort_order = 0;

	/**
	 * The template of the page type.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	public $template = '';

	/**
	 * The page type thumbnail.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	public $thumbnail = '';

	/**
	 * Constructor.
	 * Load a page type by the file.
	 *
	 * @param string $file_path
	 *
	 * @since 1.0.0
	 */

	public function __construct( $file_path ) {
		parent::__construct( $file_path );
		$this->setup_page_type();
		$this->setup_post_types();
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
			return '';
		}

		return $this->thumbnail;
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
	 * Check if the given post is allowed to use the page type.
	 *
	 * @param string $post_type
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */

	public function has_post_type( $post_type ) {
		return in_array( $post_type, $this->post_type );
	}

	/**
	 * Setup page type variables.
	 *
	 * @since 1.0.0
	 * @access private
	 */

	private function setup_page_type() {
		$this->sort_order = _papi_get_option( 'sort_order', 1000 );
	}

	/**
	 * Setup post types array.
	 *
	 * @since 1.0.0
	 * @access private
	 */

	private function setup_post_types() {
		if ( ! is_array( $this->post_type ) ) {
			$this->post_type = array( $this->post_type );
		}

		// Set a default value to post types array if we don't have a array or a empty array.
		if ( empty( $this->post_type ) ) {
			$this->post_type = array( 'page' );
		}
	}

}
