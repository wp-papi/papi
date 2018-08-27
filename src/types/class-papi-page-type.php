<?php

/**
 * Papi type that handle all post types except attachment.
 * All page types should extend this class.
 */
class Papi_Page_Type extends Papi_Entry_Type {

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
	 * Plus some special:
	 * - `title_placeholder` that can change main title input placeholder.
	 *
	 * @var array
	 */
	public $labels = [];

	/**
	 * The post types to register the page type with.
	 *
	 * @var array
	 */
	public $post_type = ['page'];

	/**
	 * Remove meta boxes.
	 *
	 * @var array
	 */
	protected $remove_meta_boxes = [];

	/**
	 * Show standard page type or not.
	 *
	 * @var bool
	 */
	public $standard_type = false;

	/**
	 * Show permalink edit box.
	 *
	 * @var bool
	 */
	public $show_permalink = true;

	/**
	 * Show page attributes box.
	 *
	 * @var bool
	 */
	public $show_page_attributes = true;

	/**
	 * Show page template dropdown.
	 *
	 * @var bool
	 */
	public $show_page_template = false;

	/**
	 * Show page type switcher.
	 *
	 * @var boolean
	 */
	public $switcher = true;

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
	 * The type name.
	 *
	 * @var string
	 */
	public $type = 'page';

	/**
	 * The constructor.
	 *
	 * Load a page type by the file.
	 *
	 * @param string $file_path
	 */
	public function __construct( $file_path = '' ) {
		parent::__construct( $file_path );
		$this->setup_post_types();
	}

	/**
	 * Determine if the page type is allowed by capabilities and post type.
	 *
	 * @return bool
	 */
	public function allowed() {
		$args = func_get_args();

		if ( empty( $args ) ) {
			return parent::allowed();
		}

		return papi_current_user_is_allowed( $this->capabilities ) && isset( $args[0] ) && in_array( $args[0], $this->post_type, true );
	}

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
	 * Get body css classes.
	 *
	 * @return array
	 */
	public function get_body_classes() {
		$classes = parent::get_body_classes();

		if ( ! $this->show_permalink ) {
			$classes[] = 'papi-hide-edit-slug-box';
		}

		if ( ! $this->show_page_attributes ) {
			$classes[] = 'papi-hide-pageparentdiv';
		}

		return $classes;
	}

	/**
	 * Get child page types that lives under the current page type.
	 *
	 * @return array
	 */
	public function get_child_types() {
		$child_types = [];

		foreach ( papi_to_array( $this->child_types ) as $id ) {
			$child_type = papi_get_entry_type_by_id( $id );

			if ( $child_type instanceof Papi_Page_Type ) {
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
			'add_new_item' => sprintf( '%s %s', __( 'Add New', 'papi' ), $this->name ),
			'edit_item'    => sprintf( '%s %s', __( 'Edit', 'papi' ), $this->name ),
			'view_item'    => sprintf( '%s %s', __( 'View', 'papi' ), $this->name )
		] );
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
	 * Get post type supports that will be removed.
	 *
	 * @return array
	 */
	protected function get_post_type_supports() {
		$supports = ['custom-fields'];

		if ( method_exists( $this, 'remove' ) ) {
			$output   = $this->remove();
			$output   = is_string( $output ) ? [$output] : $output;
			$output   = is_array( $output ) ? $output : [];
			$output   = array_filter( $output, 'is_string' );
			$supports = array_merge( $supports, $output );
		}

		$parent_class  = get_parent_class( $this );
		$parent_remove = method_exists( $parent_class, 'remove' );

		while ( $parent_remove ) {
			$parent        = new $parent_class();
			$output        = $parent->remove();
			$output        = is_string( $output ) ? [$output] : $output;
			$output        = is_array( $output ) ? $output : [];
			$output        = array_filter( $output, 'is_string' );
			$supports      = array_merge( $supports, $output );
			$parent_class  = get_parent_class( $parent_class );
			$parent_remove = method_exists( $parent_class, 'remove' );
		}

		return $supports;
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
	 * Check if the given post is allowed to use the page type.
	 *
	 * @param string $post_type
	 *
	 * @return bool
	 */
	public function has_post_type( $post_type ) {
		return in_array( $post_type, $this->post_type, true );
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

		$post_type_supports = $this->get_post_type_supports();

		foreach ( $post_type_supports as $key => $value ) {
			if ( is_numeric( $key ) ) {
				$key = $value;
				$value = '';
			}

			if ( isset( $_wp_post_type_features[$post_type], $_wp_post_type_features[$post_type][$key] ) ) {
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

		foreach ( $this->remove_meta_boxes as $item ) {
			remove_meta_box( $item[0], $post_type, $item[1] );
		}
	}

	/**
	 * Setup page type.
	 */
	public function setup() {
		parent::setup();

		// Remove post type support and meta boxes.
		$this->remove_post_type_support();

		// Add support for displaying information in publish box from a page type.
		if ( method_exists( $this, 'publish_box' ) ) {
			add_action( 'post_submitbox_misc_actions', [$this, 'publish_box'] );
		}

		// Hide page template dropdown if it shouldn't be showed.
		if ( ! $this->show_page_template ) {
			add_filter( 'theme_page_templates', '__return_empty_array' );
		}

		// Main title input placeholder.
		if ( ! empty( $this->labels['title_placeholder'] ) ) {
			add_filter( 'enter_title_here', function () {
				return $this->labels['title_placeholder'];
			} );
		}
	}

	/**
	 * Setup post types array.
	 */
	protected function setup_post_types() {
		$this->post_type = papi_to_array( $this->post_type );

		// Set a default value to post types array
		// if we don't have a array or a empty array.
		if ( empty( $this->post_type ) ) {
			$this->post_type = ['page'];
		}

		if ( count( $this->post_type ) === 1 && $this->post_type[0] === 'any' ) {
			$this->post_type = get_post_types( '', 'names' );
			$this->post_type = array_values( $this->post_type );
		}
	}
}
