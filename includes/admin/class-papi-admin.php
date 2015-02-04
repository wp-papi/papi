<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi Admin.
 *
 * @package Papi
 * @since 1.0.0
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
	 * The page type.
	 *
	 * @var string
	 */

	private $page_type;

	/**
	 * The post id.
	 *
	 * @var int
	 */

	private $post_id;

	/**
	 * The post type.
	 *
	 * @var string|bool
	 */

	private $post_type;

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

			$proceed = self::$instance->setup_papi();

			// Some times setup papi method will return false
			// and Papi should not proceed.
			if ( !$proceed ) {
				return null;
			}

			self::$instance->setup_actions();
			self::$instance->setup_filters();

			if ( empty( self::$instance->page_type ) || ! is_object( self::$instance->page_type ) ) {
				return null;
			}

			self::$instance->page_type->setup();
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

			$only_page_type = _papi_filter_settings_only_page_type( $post_type );

			if ( ! empty( $only_page_type ) ) {
				$submenu[$edit_url][10][2] = _papi_get_page_new_url( $only_page_type, false, $post_type, array( 'page_type', 'post_type' ) );
			} else {
				$page = 'papi-add-new-page,' . $post_type;

				if ( strpos( $edit_url, 'post_type' ) === false ) {
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
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'papi-main', PAPI_PLUGIN_URL . 'gui/css/style.css', false, null );
	}

	/**
	 * Enqueue script into admin footer.
	 *
	 * @since 1.0.0
	 */

	public function admin_enqueue_scripts() {
		wp_enqueue_script( 'papi-main', PAPI_PLUGIN_URL . 'gui/js/main.js', array(
			'jquery',
			'jquery-ui-core',
			'jquery-ui-sortable',
			'backbone',
			'wp-backbone',
			'wp-color-picker'
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

			$only_page_type = _papi_filter_settings_only_page_type( $post_type );

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
	 * Filter page types in post type list.
	 *
	 * @since 1.1.0
	 */

	public function restrict_page_types() {
		global $typenow;

		$post_types = _papi_get_post_types();

		if ( in_array( $typenow, $post_types ) ) {
			$page_types = _papi_get_all_page_types( false, $typenow );

			$page_types = array_map( function ( $page_type ) {
				return array(
					'name' => $page_type->name,
					'value' => $page_type->get_filename()
				);
			}, $page_types );

			// Add the standard page that isn't a real page type.
			$page_types[] = array(
				'name' => __( 'Standard page', 'papi' ),
				'value' => 'papi-standard-page'
			);

			usort( $page_types, function ( $a, $b ) {
				return strcmp( strtolower( $a['name'] ), strtolower( $b['name'] ) );
			} );

			?>
			<select name="page_type" class="postform">
				<option value="0" selected><?php _e( 'Show all page types', 'papi' ); ?></option>
				<?php
				foreach ( $page_types as $page_type ) {
					printf( '<option value="%s" %s>%s</option>', $page_type['value'], ( _papi_get_qs( 'page_type' ) === $page_type['value'] ? ' selected' : '' ), $page_type['name'] );
				}
				?>
			</select>
			<?php
		}
	}

	/**
	 * Filter posts on load if `page_type` query string is set.
	 *
	 * @param object $query
	 *
	 * @since 1.1.0
	 *
	 * @return object
	 */

	public function pre_get_posts( $query ) {
		global $pagenow;

		if ( $pagenow === 'edit.php' && !is_null( _papi_get_qs( 'page_type' ) ) ) {
			if ( _papi_get_qs( 'page_type' ) === 'papi-standard-page' ) {
				$query->set( 'meta_query', array(
					array(
						'key' => '_papi_page_type',
						'compare' => 'NOT EXISTS'
					)
				) );
			} else {
				$query->set( 'meta_key', '_papi_page_type' );
				$query->set( 'meta_value', _papi_get_qs( 'page_type' ) );
			}
		}

		return $query;
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
		$this->post_type        = _papi_get_wp_post_type();
		$this->post_id          = _papi_get_post_id();
		$this->page_type        = _papi_get_page_type_meta_value( $this->post_id );
	}

	/**
	 * Setup actions.
	 *
	 * @since 1.0.0
	 * @access private
	 */

	private function setup_actions() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'load-post-new.php', array( $this, 'load_post_new' ) );
		add_action( 'restrict_manage_posts', array( $this, 'restrict_page_types' ) );
	}

	/**
	 * Setup filters.
	 *
	 * @since 1.0.0
	 * @access private
	 */

	private function setup_filters() {
		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );
		add_filter( 'pre_get_posts', array( $this, 'pre_get_posts' ) );

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
	 * Admin init.
	 *
	 * Change add new item text.
	 *
	 * @since 1.2.0
	 */

	public function admin_init() {
		global $wp_post_types;
		$post_type = _papi_get_wp_post_type();

		if ( isset( $wp_post_types[$post_type] ) && !empty( $this->page_type->add_new_name ) ) {
			if ( !empty( $this->page_type->add_new_name ) ) {
				$title = $this->page_type->add_new_name;
			} else {
				$title = $wp_post_types[$post_type]->labels->add_new . ' ' . $this->page_type->name;
			}

			$wp_post_types[$post_type]->labels->add_new_item = $this->page_type->add_new_name;
		}
	}

	/**
	 * Load right Papi file if it exists.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */

	public function setup_papi() {
		// If the post type isn't in the post types array we can't proceed.
		if ( in_array( $this->post_type, array( 'revision', 'nav_menu_item' ) ) ) {
			return false;
		}

		// If we have a null page type we need to find which page type to use.
		if ( empty( $this->page_type ) ) {
			if ( _papi_is_method( 'post' ) && isset( $_POST['papi_page_type'] ) && $_POST['papi_page_type'] ) {
				$this->page_type = $_POST['papi_page_type'];
			} else {
				$this->page_type = _papi_get_page_type_meta_value();
			}
		}

		if ( empty( $this->page_type ) ) {
			// If only page type is used, override the page type value.
			$this->page_type = _papi_filter_settings_only_page_type( $this->post_type );

			if ( empty( $this->page_type ) ) {
				return false;
			}
		}

		// Get the path to the page type file.
		$path = _papi_get_file_path( $this->page_type );

		// Load the page type and create a new instance of it.
		$this->page_type = _papi_get_page_type( $path );

		return true;
	}
}
