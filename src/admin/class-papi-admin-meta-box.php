<?php

/**
 * Admin class that handles admin meta boxes.
 */
final class Papi_Admin_Meta_Box {

	/**
	 * The core box.
	 *
	 * @var Papi_Core_Box
	 */
	protected $box;

	/**
	 * The constructor.
	 *
	 * @param Papi_Core_Box $box
	 */
	public function __construct( Papi_Core_Box $box ) {
		// Check if the current user is allowed to view this box.
		if ( ! papi_current_user_is_allowed( $box->capabilities ) ) {
			return;
		}

		if ( $box->display ) {
			$this->box = $box;
			$this->setup_actions();
		}
	}

	/**
	 * Add css classes to meta box.
	 *
	 * @param  array $classes
	 *
	 * @return string[]
	 */
	public function meta_box_css_classes( array $classes ) {
		return array_merge( $classes, [
			'papi-box'
		] );
	}

	/**
	 * Move meta boxes after title.
	 */
	public function move_meta_box_after_title() {
		global $post, $wp_meta_boxes;
		do_meta_boxes( get_current_screen(), $this->box->context, $post );
		unset( $wp_meta_boxes[get_post_type( $post )][$this->box->context] );
	}

	/**
	 * Get meta post type.
	 *
	 * @return string
	 */
	protected function get_post_type() {
		if ( papi_get_meta_type() === 'post' ) {
			if ( $post_id = papi_get_post_id() ) {
				return get_post_type( $post_id );
			}

			if ( $post_type = papi_get_post_type() ) {
				return $post_type;
			}
		}

		return $this->box->id;
	}

	/**
	 * Get meta box title.
	 *
	 * @return string
	 */
	protected function get_title() {
		$title = $this->box->title;

		if ( $this->box->get_option( 'required' ) ) {
			$title .= papi_property_required_html(
				$this->box->properties[0],
				true
			);
		}

		return $title;
	}

	/**
	 * Render the meta box
	 *
	 * @param array $post
	 * @param array $args
	 */
	public function render_meta_box( $post, array $args ) {
		if ( ! isset( $args['args'] ) ) {
			return;
		}

		// Do a last check before all properties is rendered.
		$args['args'] = array_filter( $args['args'], 'papi_is_property' );

		// Inherit options from the box.
		foreach ( $args['args'] as $index => $property ) {
			if ( $property->layout === 'horizontal' ) {
				$args['args'][$index]->layout = $this->box->layout;
			}
		}

		// Render the properties.
		papi_render_properties( papi_sort_order( array_reverse( $args['args'] ) ) );
	}

	/**
	 * Setup action hooks.
	 */
	protected function setup_actions() {
		if ( post_type_exists( $this->get_post_type() ) && papi_get_meta_type() === 'post' ) {
			add_action( 'add_meta_boxes', [$this, 'setup_meta_box'] );

			if ( $this->box->context === 'after_title' ) {
				add_action( 'edit_form_after_title', [$this, 'move_meta_box_after_title'] );
			}
		} else {
			$this->setup_meta_box();
		}

		// Will be called on when you call do_meta_boxes
		// even without a real post type.
		add_action(
			sprintf(
				'postbox_classes_%s_%s',
				strtolower( $this->get_post_type() ),
				$this->box->id
			),
			[$this, 'meta_box_css_classes']
		);
	}

	/**
	 * Setup meta box.
	 */
	public function setup_meta_box() {
		$properties = $this->box->properties;

		// Check all properties and remove them that can't be rendered.
		foreach ( $properties as $index => $property ) {
			if ( $property instanceof Papi_Property && ! $property->can_render() ) {
				unset( $properties[$index] );
			}
		}

		// Bail if properties array is empty,
		// no need to render a empty meta box.
		if ( empty( $properties ) ) {
			return;
		}

		add_meta_box(
			$this->box->id,
			$this->get_title(),
			[$this, 'render_meta_box'],
			$this->get_post_type(),
			$this->box->context,
			$this->box->priority,
			$properties
		);
	}
}
