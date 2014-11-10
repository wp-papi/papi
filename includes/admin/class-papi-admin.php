<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi Admin.
 *
 * @package Papi
 * @version 1.0.0
 */
final class Papi_Admin {

	/**
	 * Thew view instance.
	 *
	 * @var Papi_Admin_View
	 */

	private $view;

	/**
	 * The meta boxes instance.
	 *
	 * @var Papi_Admin_Meta_Boxes
	 */

	private $meta_boxes;

	/**
	 * The management pages instance.
	 *
	 * @var Papi_Admin_Management_Pages
	 */

	private $management_pages;

	/**
	 * The instance of Papi Core.
	 *
	 * @var object
	 * @since 1.0.0
	 */

	private static $instance;

	/**
	 * Papi Admin instance.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Papi_Admin;
			self::$instance->setup_globals();
			self::$instance->setup_actions();
			self::$instance->setup_filters();
			self::$instance->setup_papi();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */

	public function __construct() {
	}

	/**
	 * Setup globals.
	 *
	 * @since 1.0.0
	 * @access private
	 */

	private function setup_globals() {
		$this->view             = new Papi_Admin_View;
		$this->meta_boxes       = new Papi_Admin_Meta_Boxes;
		$this->management_pages = new Papi_Admin_Management_Pages;
	}

	/**
	 * Setup actions.
	 *
	 * @since 1.0.0
	 * @access private
	 */

	private function setup_actions() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Setup filters.
	 *
	 * @since 1.0.0
	 * @access private
	 */

	private function setup_filters() {
		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );

		$post_types = _papi_get_post_types();

		// Add post type columns to eavery post types that is used.
		foreach ( $post_types as $post_type ) {
			add_filter( 'manage_' . $post_type . '_posts_columns', array( $this, 'manage_page_type_posts_columns' ) );
			add_action( 'manage_' . $post_type . '_posts_custom_column', array(
				$this,
				'manage_page_type_posts_custom_column'
			), 10, 2 );
		}
	}

	/**
	 * Build up the sub menu for "Page".
	 *
	 * @since 1.0.0
	 */

	public function admin_menu() {
		$post_types = _papi_get_post_types();
		$page_types = _papi_get_all_page_types( true );

		// If we don't have any page types don't change any menu items.
		if ( empty( $page_types ) ) {
			return;
		}

		foreach ( $post_types as $post_type ) {

			// Remove "Add new" menu item.
			remove_submenu_page( 'edit.php?post_type=' . $post_type, 'post-new.php?post_type=' . $post_type );

			$option_key         = sprintf('post_type.%s.only_page_type', $post_type);
			$only_page_type     = _papi_get_option($option_key);

			if ( ! empty($only_page_type) ) {
				$url = _papi_get_page_new_url( $only_page_type, false );
				// Add our custom menu item.
				add_submenu_page( 'edit.php?post_type=' . $post_type,
					__( 'Add New', 'papi' ),
					__( 'Add New', 'papi' ),
					'manage_options',
					$url );
			} else {
				// Add our custom menu item.
				add_submenu_page( 'edit.php?post_type=' . $post_type,
					__( 'Add New', 'papi' ),
					__( 'Add New', 'papi' ),
					'read',
					'papi-add-new-page,' . $post_type,
					array( $this, 'render_view' ) );
			}
		}
	}

	/**
	 * Add style to admin head.
	 *
	 * @since 1.0.0
	 */

	public function admin_head() {
		echo '<link href="' . PAPI_PLUGIN_URL . 'gui/css/style.css" type="text/css" rel="stylesheet" />';
		wp_enqueue_media();
	}

	/**
	 * Enqueue script into admin footer.
	 *
	 * @since 1.0.0
	 */

	public function admin_enqueue_scripts() {
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'backbone.min' );
		wp_enqueue_script( 'papi-main', PAPI_PLUGIN_URL . 'gui/js/main.js', array(
			'jquery',
			'jquery-ui-core',
			'jquery-ui-sortable',
			'backbone',
			'wp-backbone'
		), '', true );

		wp_localize_script( 'papi-main', 'papiL10n', array(
			'requiredError' => __( 'This fields are required:', 'papi' ),
		) );
	}

	/**
	 * Add custom body class when it's a page type.
	 *
	 * @param string $classes
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */

	public function admin_body_class( $classes ) {
		$post_type = _papi_get_wp_post_type();

		if ( ! in_array( $post_type, _papi_get_post_types() ) ) {
			return null;
		}

		if ( count( get_page_templates() ) ) {
			$classes .= 'papi-hide-cpt';
		}

		return $classes;
	}

	/**
	 * Add custom table header to page type.
	 *
	 * @param array $defaults
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	public function manage_page_type_posts_columns( $defaults ) {
		$defaults['page_type'] = __( 'Page Type', 'papi' );

		return $defaults;
	}

	/**
	 * Add custom table column to page type.
	 *
	 * @param string $column_name
	 * @param int $post_id
	 *
	 * @since 1.0.0
	 */

	public function manage_page_type_posts_custom_column( $column_name, $post_id ) {
		if ( $column_name === 'page_type' ) {
			$page_type = _papi_get_file_data( $post_id );
			if ( ! is_null( $page_type ) ) {
				echo $page_type->name;
			} else {
				_e( 'Standard page', 'papi' );
			}
		}
	}

	/**
	 * Menu callback that loads right view depending on what the "page" query string says.
	 *
	 * @since 1.0.0
	 */

	public function render_view() {
		if ( isset( $_GET['page'] ) && strpos( $_GET['page'], 'papi' ) !== false ) {
			$page      = str_replace( 'papi-', '', $_GET['page'] );
			$page_view = preg_replace( '/\,.*/', '', $page );
		} else {
			$page_view = null;
		}

		if ( ! is_null( $page_view ) ) {
			$this->view->render( $page_view );
		} else {
			echo '<h2>Papi - 404</h2>';
		}
	}

	/**
	 * Load right Papi file if it exists.
	 *
	 * @since 1.0.0
	 */

	public function setup_papi() {
		$post_id   = _papi_get_post_id();
		$page_type = _papi_get_page_type_meta_value( $post_id );
		$post_type = _papi_get_wp_post_type();

		// If the post type isn't in the post types array we can't proceed.
		if ( in_array( $post_type, array( 'revision', 'nav_menu_item' ) ) ) {
			return;
		}

		// If we have a null page type we need to find which page type to use.
		if ( empty( $page_type ) ) {
			if ( _papi_is_method( 'post' ) && isset( $_POST['papi_page_type'] ) && $_POST['papi_page_type'] ) {
				$page_type = $_POST['papi_page_type'];
			} else {
				$page_type = _papi_get_page_type_meta_value();
			}
		}

		if ( empty( $page_type ) ) {
			return;
		}

		// Get the path to the page type file.
		$path = _papi_get_page_type_file( $page_type );

		// Load the page type and create a new instance of it.
		$page_type = _papi_get_page_type( $path );

		if ( empty( $page_type ) ) {
			return;
		}

		// Create a new class of the page type.
		$page_type->new_class();
	}

}
