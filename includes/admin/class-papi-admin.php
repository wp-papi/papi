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
	 *
	 * @since 1.0.0
	 */

	public function __construct() {}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 2.1
	 */

	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'papi' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 2.1
	 */

	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'papi' ), '1.0.0' );
	}

	/**
	 * Build up the sub menu for post types.
	 *
	 * @since 1.0.0
	 */

	public function admin_menu() {
		global $menu, $submenu;

		$post_types = _papi_get_post_types();

		foreach ( $post_types as $post_type ) {

			if ( $post_type === 'post' ) {
				$edit_url = 'edit.php';
			} else {
				$edit_url = 'edit.php?post_type=' . $post_type;
			}

			if ( ! isset( $submenu[$edit_url] ) || ! isset( $submenu[$edit_url][10] ) || !isset( $submenu[$edit_url][10][2] ) ) {
				continue;
			}

			$option_key         = sprintf( 'post_type_%s_only_page_type', $post_type );
			$only_page_type     = _papi_get_option( $option_key );

			if ( ! empty( $only_page_type ) ) {
				$submenu[$edit_url][10][2] = _papi_get_page_new_url( $only_page_type, false, $post_type );
			} else {
				$page = 'papi-add-new-page,' . $post_type;

				if (strpos($edit_url, 'post_type') === false) {
					$start = '?';
				} else {
					$start = '&';
				}

				$submenu[$edit_url][10][2] = $edit_url . $start . 'page=' . $page;

				// Hidden menu item.
				add_submenu_page( null, __( 'Add New', 'papi' ), __( 'Add New', 'papi' ), 'read', $page, array( $this, 'render_view' ) );
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
			return $classes;
		}

		if ( count( get_page_templates() ) ) {
			$classes .= 'papi-hide-cpt';
		}

		return $classes;
	}

	/**
	 * Load post new action
	 * Redirect to right url if no page type is set.
	 *
	 * @since 1.0.0
	 */

	public function load_post_new() {
		$request_uri = $_SERVER['REQUEST_URI'];
		$post_types = _papi_get_post_types();
		$post_type  = _papi_get_wp_post_type();

		if ( in_array( $post_type, $post_types ) && strpos( $request_uri, 'page_type=' ) === false && strpos( $request_uri, 'papi-bypass=true' ) === false ) {
			$parsed_url = parse_url( $request_uri );

			$option_key         = sprintf( 'post_type_%s_only_page_type', $post_type );
			$only_page_type     = _papi_get_option( $option_key );

			// Check if we should show one post type or not and create the right url for that.
			if ( ! empty( $only_page_type ) ) {
				$url = _papi_get_page_new_url( $only_page_type, false );
			} else {
				$page = 'page=papi-add-new-page,' . $post_type;

				if ( $post_type !== 'post' ) {
					$page = '&' . $page;
				}

				$url = 'edit.php?' . $parsed_url['query'] . $page;
			}

			wp_safe_redirect( $url );
			exit;
		}
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
		add_action( 'load-post-new.php', array( $this, 'load_post_new' ) );
		add_action( 'load-media-new.php', array( $this, '_papi_load_post_new' ) );
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
		$path = _papi_get_file_path( $page_type );

		// Load the page type and create a new instance of it.
		$page_type = _papi_get_page_type( $path );

		if ( empty( $page_type ) ) {
			return;
		}

		// Create a new class of the page type.
		$page_type->setup();
	}
}
