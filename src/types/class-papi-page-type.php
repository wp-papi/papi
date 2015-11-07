<?php

/**
 * Papi type that handle all psot types except attachment,
 * option data and rendering. All page types should extend
 * this class.
 */
class Papi_Page_Type extends Papi_Page_Type_Meta {

	/**
	 * Array of post type supports to remove.
	 * By default remove `postcustom` which is the Custom fields metabox.
	 *
	 * @var array
	 */
	private $post_type_supports = ['custom-fields'];

	/**
	 * Remove meta boxes.
	 *
	 * @var array
	 */
	private $remove_meta_boxes = [];

	/**
	 * Should the Page Type be displayed in WordPress admin or not?
	 *
	 * @param  string $post_type
	 *
	 * @return bool
	 */
	public function display( $post_type ) {
		return true;
	}

	/**
	 * Get post type.
	 *
	 * @return string
	 */
	public function get_post_type() {
		return papi_get_post_type();
	}

	/**
	 * This function will setup all meta boxes.
	 */
	public function setup() {
		if ( ! method_exists( $this, 'register' ) ) {
			return;
		}

		// 1. Run the register method.
		$this->register();

		// 2. Remove post type support
		$this->remove_post_type_support();

		// 3. Load all boxes.
		$this->boxes = $this->get_boxes();

		foreach ( $this->boxes as $box ) {
			new Papi_Admin_Meta_Box( $box );
		}
	}

	/**
	 * Remove post type support. Runs once, on page load.
	 *
	 * @param array $post_type_supports
	 */
	protected function remove( $post_type_supports = [] ) {
		$this->post_type_supports = array_merge( $this->post_type_supports, papi_to_array( $post_type_supports ) );
	}

	/**
	 * Remove post type support action.
	 */
	public function remove_post_type_support() {
		global $_wp_post_type_features;

		$post_type = $this->get_post_type();

		if ( empty( $post_type ) ) {
			return;
		}

		foreach ( $this->post_type_supports as $key => $value ) {
			if ( is_numeric( $key ) ) {
				$key = $value;
				$value = '';
			}

			if ( isset( $_wp_post_type_features[$post_type] ) && isset( $_wp_post_type_features[$post_type][$key] ) ) {
				unset( $_wp_post_type_features[$post_type][$key] );
				continue;
			}

			// Add non post type support to remove meta boxes array.
			if ( empty( $value ) ) {
				$value = 'normal';
			}

			$this->remove_meta_boxes[] = [$key, $value];
		}

		add_action( 'add_meta_boxes', [$this, 'remove_meta_boxes'], 999 );
	}

	/**
	 * Remove meta boxes.
	 */
	public function remove_meta_boxes() {
		$post_type = $this->get_post_type();

		if ( empty( $post_type ) ) {
			return;
		}

		foreach ( $this->remove_meta_boxes as $item ) {
			remove_meta_box( $item[0], $post_type, $item[1] );
		}
	}
}
