<?php

/**
 * Admin class that handles admin meta boxes.
 */
final class Papi_Admin_Meta_Box {

	/**
	 * Meta box default options.
	 *
	 * @var array
	 */
	private $default_options = [
		'capabilities' => [],
		'context'      => 'normal',
		'priority'     => 'default',
		'properties'   => [],
		'sort_order'   => null,
		'title'        => '',
		// Private options
		'_id'          => '',
		'_post_type'   => 'page',
		'_required'    => false,
		'_tab_box'     => false
	];

	/**
	 * Meta box options.
	 *
	 * @var object
	 */
	private $options;

	/**
	 * Contains all root level properties in this meta box.
	 *
	 * @var array
	 */
	private $properties = [];

	/**
	 * The constructor.
	 *
	 * @param array $options
	 * @param array $properties
	 */
	public function __construct( $options = [], $properties = [] ) {
		if ( empty( $options ) ) {
			return;
		}

		$this->setup_options( $options );

		// Check if the current user is allowed to view this box.
		if ( ! papi_current_user_is_allowed( $this->options->capabilities ) ) {
			return;
		}

		$this->populate_properties( $properties );

		$this->setup_actions();
	}

	/**
	 * Add property.
	 *
	 * @param object $property
	 */
	public function add_property( $property ) {
		$this->properties[] = $property;
	}

	/**
	 * Get meta box id.
	 *
	 * @param string $slug
	 *
	 * @return string
	 */
	private function get_meta_box_id( $slug ) {
		return papi_f( papi_underscorify( papify( $slug ) ) );
	}

	/**
	 * Add css classes to meta box.
	 *
	 * @param  array $classes
	 *
	 * @return string[]
	 */
	public function meta_box_css_classes( $classes ) {
		return array_merge( $classes, [
			'papi-box'
		] );
	}

	/**
	 * Move meta boxes after title.
	 */
	public function move_meta_box_after_title() {
		global $post, $wp_meta_boxes;
		do_meta_boxes( get_current_screen(), $this->options->context, $post );
		unset( $wp_meta_boxes[get_post_type( $post )][$this->options->context] );
	}

	/**
	 * Populate post type.
	 *
	 * @param  array|string $post_type
	 *
	 * @return string
	 */
	private function populate_post_type( $post_type ) {
		$post_id = papi_get_post_id();

		if ( $post_id !== 0 ) {
			return get_post_type( $post_id );
		}

		// Get the post type that we currently are on if it exist in the array of post types.
		$post_type = array_filter( papi_to_array( $post_type ), function ( $post_type ) {
			// Support fake post types.
			if ( strpos( $post_type, '_papi' ) !== false ) {
				return true;
			}

			return ! empty( $post_type ) &&
				strtolower( $post_type ) === strtolower(
					papi_get_or_post( 'post_type' )
				);
		} );

		if ( ! empty( $post_type ) ) {
			return $post_type[0];
		}

		return 'page';
	}

	/**
	 * Populate the properties array
	 *
	 * @param array $properties
	 */
	private function populate_properties( $properties ) {
		$this->properties = papi_populate_properties( $properties );

		if ( ! empty( $this->properties ) ) {
			$this->options->_tab_box = isset( $this->properties[0]->tab ) &&
				$this->properties[0]->tab;
		}
	}

	/**
	 * Render the meta box
	 *
	 * @param array $post
	 * @param array $args
	 */
	public function render_meta_box( $post, $args ) {
		if ( ! is_array( $args ) || ! isset( $args['args'] ) ) {
			return;
		}

		// Render the properties.
		papi_render_properties( $args['args'] );
	}

	/**
	 * Setup actions.
	 */
	private function setup_actions() {
		if ( post_type_exists( $this->options->_post_type ) ) {
			add_action( 'add_meta_boxes', [$this, 'setup_meta_box'] );

			if ( $this->options->context === 'after_title' ) {
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
				$this->options->_post_type,
				$this->options->_id
			),
			[$this, 'meta_box_css_classes']
		);
	}

	/**
	 * Setup meta box.
	 */
	public function setup_meta_box() {
		$this->options->title = papi_remove_papi( $this->options->title );

		if ( $this->options->_required ) {
			$this->options->title .= papi_required_html(
				$this->properties[0],
				true
			);
		}

		add_meta_box(
			$this->options->_id,
			$this->options->title,
			[ $this, 'render_meta_box' ],
			$this->options->_post_type,
			$this->options->context,
			$this->options->priority,
			$this->properties
		);
	}

	/**
	 * Setup options
	 *
	 * @param array $options
	 */
	private function setup_options( $options ) {
		$options                   = empty( $options ) ? [] : $options;
		$options                   = array_merge( $this->default_options, $options );
		$this->options             = (object) $options;
		$this->options->title      = ucfirst( $this->options->title );
		$this->options->slug       = papi_slugify( $this->options->title );
		$this->options->_id        = $this->get_meta_box_id(
			$this->options->slug
		);
		$this->options->_post_type = $this->populate_post_type(
			$this->options->_post_type
		);
	}
}
