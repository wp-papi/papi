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
		'id'         => '',
		'context'    => 'normal',
		'mode'       => 'standard',
		'post_type'  => 'page',
		'priority'   => 'default',
		'properties' => array(),
		'sort_order' => null,

		// Private options
		'_tab_box'   => false
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
		add_action( 'postbox_classes_page_' . $this->options->id, array( $this, 'meta_box_css_classes' ));
	}

	/**
	 * Add css classes to meta box.
	 *
	 * @param array $classes
	 *
	 * @return array
	 */

	public function meta_box_css_classes ($classes) {
		$classes[] = 'papi-box';

		if ($this->options->mode === 'seamless') {
			if ($this->options->_tab_box) {
				$classes[] = 'papi-mode-seamless-tabs';
			} else {
				$classes[] = 'papi-mode-seamless';
			}
		}

		return $classes;
	}

	/**
	 * Box property is a property that is direct on the box function with the property function.
	 *
	 * @param array $properties
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	private function box_property( $properties ) {
		$box_property = array_filter( $properties, function ( $property ) {
			return ! is_object( $property );
		} );

		if ( ! empty( $box_property ) ) {
			$property = _papi_get_property_options( $box_property );
			if ( ! $property->disabled ) {
				$properties = array( $property );
			}
		}

		return $properties;
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
		$options             = _papi_h( $options, array() );
		$options             = array_merge( $this->default_options, $options );
		$this->options       = (object) $options;
		$this->options->slug = _papi_slugify( $this->options->title );
		$this->options->id   = str_replace( '_', '-', _papify( $this->options->slug ) );

		$properties = $this->box_property( $properties );

		// Fix so the properties array will have the right order.
		$properties = array_reverse( $properties );

		foreach ( $properties as $property ) {
			if ( is_array( $property ) ) {
				foreach ( $property as $p ) {
					if ( is_object( $p ) ) {
						$this->properties[] = $p;
					}
				}
			} else if ( is_object( $property ) ) {
				$this->properties[] = $property;
			}
		}

		if (!empty($this->properties)) {
			$this->options->_tab_box = isset($this->properties[0]->tab) && $this->properties[0]->tab;
		}

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
	 * Setup meta box.
	 */

	public function setup_meta_box() {
		add_meta_box(
			$this->options->id,
			_papi_remove_papi( $this->options->title ),
			array( $this, 'render_meta_box' ),
			$this->options->post_type,
			$this->options->context,
			$this->options->priority,
			$this->properties
		);
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

		wp_nonce_field( 'papi_save_data', 'papi_meta_nonce' );
		?>
		<input type="hidden" name="papi_page_type" value="<?php echo _papi_get_page_type_meta_value(); ?>"/>
		<?php

		// Render the properties.
		_papi_render_properties( $args['args'] );
	}
}
