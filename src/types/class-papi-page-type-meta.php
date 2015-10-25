<?php

/**
 * Base Papi type implementation of meta data
 * for a page type.
 */
class Papi_Page_Type_Meta extends Papi_Core_Type {

	/**
	 * The meta method to call.
	 *
	 * @var string
	 */
	public $_meta_method = 'page_type';

	/**
	 * Capabilities list.
	 *
	 * @var array
	 */
	public $capabilities = [];

	/**
	 * The page types that lives under this page type.
	 *
	 * @var array
	 */
	public $child_types = [];

	/**
	 * The description of the page type.
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * Fill labels.
	 *
	 * @var bool
	 */
	public $fill_labels = false;

	/**
	 * Labels, the same labels that post type object uses.
	 *
	 * @var array
	 */
	public $labels = [];

	/**
	 * The name of the page type.
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * The post types to register the page type with.
	 *
	 * @var array
	 */
	public $post_type = ['page'];

	/**
	 * The sort order of the page type.
	 *
	 * @var int
	 */
	public $sort_order = null;

	/**
	 * Show standard page type or not.
	 *
	 * @var bool
	 */
	public $standard_type = false;

	/**
	 * The template of the page type.
	 *
	 * @var string
	 */
	public $template = '';

	/**
	 * The page type thumbnail.
	 *
	 * @var string
	 */
	public $thumbnail = '';

	/**
	 * The constructor.
	 *
	 * Load a page type by the file.
	 *
	 * @param string $file_path
	 */
	public function __construct( $file_path = '' ) {
		parent::__construct( $file_path );
		$this->setup_page_type();
		$this->setup_post_types();
	}

	/**
	 * Is the user allowed to view this page type?
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
	 * Get child page types that lives under the current page type.
	 *
	 * @return array
	 */
	public function get_child_types() {
		$child_types = [];

		foreach ( papi_to_array( $this->child_types ) as $id ) {
			$child_type = papi_get_page_type_by_id( $id );

			if ( papi_is_page_type( $child_type ) ) {
				$child_types[] = $child_type;
			}
		}

		return $child_types;
	}

	/**
	 * Get labels that should be changed
	 * when using `fill_labels` option.
	 *
	 * @return array
	 */
	public function get_labels() {
		if ( ! $this->fill_labels ) {
			return $this->labels;
		}

		return array_merge( $this->labels, [
			'add_new_item' => sprintf(
				'%s %s',
				__( 'Add New', 'papi' ),
				$this->name
			),
			'edit_item' => sprintf(
				'%s %s',
				__( 'Edit', 'papi' ),
				$this->name
			),
			'view_item' => sprintf(
				'%s %s',
				__( 'View', 'papi' ),
				$this->name
			)
		] );
	}

	/**
	 * Get page type image thumbnail.
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
	 * @return bool
	 */
	public function has_post_type( $post_type ) {
		return in_array( $post_type, $this->post_type );
	}

	/**
	 * Setup page type variables.
	 */
	private function setup_page_type() {
		if ( is_null( $this->sort_order ) ) {
			$this->sort_order = papi_filter_settings_sort_order();
		}
	}

	/**
	 * Setup post types array.
	 */
	private function setup_post_types() {
		$this->post_type = papi_to_array( $this->post_type );

		// Set a default value to post types array
		// if we don't have a array or a empty array.
		if ( empty( $this->post_type ) ) {
			$this->post_type = ['page'];
		}
	}
}
