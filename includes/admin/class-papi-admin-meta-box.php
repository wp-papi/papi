<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi Admin Meta Box.
 *
 * @package Papi
 * @version 1.0.0
 */

class Papi_Admin_Meta_Box {

	/**
	 * Contains all root level properties in this meta box.
	 *
	 * @var array
	 * @since 1.0.0
	 */

	private $properties = array();

	/**
	 * Meta box default options.
	 *
	 * @var array
	 * @since 1.0.0
	 */

	private $default_options = array(
		'capabilities' => array(),
		'context'      => 'normal',
		'mode'         => 'standard',
		'post_type'    => 'page',
		'priority'     => 'default',
		'properties'   => array(),
		'sort_order'   => null,
		'title'        => '',
		// Private options
		'_id'          => '',
		'_post_type'   => '',
		'_tab_box'     => false
	);

	/**
	 * Meta box options.
	 *
	 * @var object
	 * @since 1.0.0
	 */

	private $options;

	/**
	 * Setup actions.
	 *
	 * @since 1.0.0
	 */

	private function setup_actions() {
		add_action( 'add_meta_boxes', array( $this, 'setup_meta_box' ) );
		add_action( 'postbox_classes_' . $this->options->post_type . '_' . $this->options->_id, array( $this, 'meta_box_css_classes' ) );
	}

	/**
	 * Setup options
	 *
	 * @param array $options
	 *
	 * @since 1.0.0
	 */

	private function setup_options( $options ) {
		$options                  = _papi_h( $options, array() );
		$options                  = array_merge( $this->default_options, $options );
		$this->options            = (object) $options;
		$this->options->title     = ucfirst( $this->options->title );
		$this->options->slug      = _papi_slugify( $this->options->title );
		$this->options->_id       = _papi_underscorify( _papify( $this->options->slug ) );
		$this->options->post_type = $this->populate_post_type( $this->options->post_type );
	}

	/**
	 * Populate post type.
	 *
	 * @param array|string $post_type
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */

	private function populate_post_type ( $post_type ) {
		$post_id = _papi_get_post_id();

		if ( ! is_null( $post_id ) ) {
			return get_post_type( $post_id );
		}

		// Get the post type that we currently are on if it exist in the array of post types.
		$post_type = array_filter( _papi_to_array( $post_type ), function ( $post_type ) {
			return strtolower( $post_type ) == strtolower( _papi_get_or_post( 'post_type' ) );
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
	 *
	 * @since 1.0.0
	 */

	private function populate_properties( $properties ) {
		$this->properties = _papi_populate_properties( $properties );

		if ( ! empty( $this->properties ) ) {
			$this->options->_tab_box = isset( $this->properties[0]->tab ) && $this->properties[0]->tab;
		}
	}

	/**
	 * Constructor.
	 *
	 * @param array $options
	 * @param array $properties
	 *
	 * @since 1.0.0
	 */

	public function __construct( $options = array(), $properties = array() ) {
		// Can't handle empty options.
		if ( empty( $options ) ) {
			return null;
		}

		$this->setup_options( $options );

		// Can current user view this box?
		if ( ! _papi_current_user_is_allowed( $this->options->capabilities ) ) {
			return null;
		}

		$this->populate_properties( $properties );

		$this->setup_actions();
	}

	/**
	 * Add property.
	 *
	 * @param object $property
	 *
	 * @since 1.0.0
	 */

	public function add_property( $property ) {
		$this->properties[] = $property;
	}

	/**
	 * Add css classes to meta box.
	 *
	 * @param array $classes
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	public function meta_box_css_classes( $classes ) {
		$classes[] = 'papi-box';

		if ( $this->options->mode === 'seamless' ) {
			if ( $this->options->_tab_box ) {
				$classes[] = 'papi-mode-seamless-tabs';
			} else {
				$classes[] = 'papi-mode-seamless';
			}
		}

		return $classes;
	}

	/**
	 * Render the meta box
	 *
	 * @param array $post
	 * @param array $args
	 *
	 * @since 1.0.0
	 */

	public function render_meta_box( $post, $args ) {
		if ( ! is_array( $args ) || ! isset( $args['args'] ) ) {
			return;
		}

		wp_nonce_field( 'papi_save_data', 'papi_meta_nonce' );
		?>
		<input type="hidden" name="_papi_page_type" value="<?php echo _papi_get_page_type_meta_value(); ?>"/>
		<?php

		// Render the properties.
		_papi_render_properties( $args['args'] );
	}

	/**
	 * Setup meta box.
	 *
	 * @since 1.0.0
	 */

	public function setup_meta_box() {
		add_meta_box(
			$this->options->_id,
			_papi_remove_papi( $this->options->title ),
			array( $this, 'render_meta_box' ),
			$this->options->post_type,
			$this->options->context,
			$this->options->priority,
			$this->properties
		);
	}
}
