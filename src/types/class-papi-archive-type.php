<?php

/**
 * Papi type that handle fields for archive templates and rendering
 */
class Papi_Archive_Type extends Papi_Option_Type {

	/**
	 * Capability.
	 *
	 * @var array
	 */
	public $capability = 'edit_posts';

	/**
	 * Get menu to register archive type to.
	 *
	 * @return string
	 */
	public function get_menu() {
		$menu = '';

		foreach ( papi_to_array( $this->post_type ) as $post_type ) {
            $menu = 'edit.php?post_type=' . $post_type;

            if ( 'post' === $post_type ) {
                $menu = 'edit.php';
            }
        }

		return $menu;
	}

	/**
	 * Get post type as prefix.
	 *
	 * @return string
	 */
	public function get_prefix() {
		return $this->post_type;
	}

	/**
	 * Add post type to slug.
	 *
	 * @param  string $slug
	 *
	 * @return string
	 */
	public function modify_slug( $slug ) {
		return papi_core_prefix_slug( $this->get_prefix(), $slug );
	}

	/**
	 * Setup entry type.
	 */
	public function setup() {
		parent::setup();

		add_filter( 'papi/core/slug', [$this, 'modify_slug'] );
	}
}
