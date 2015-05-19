<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Papi Admin.
 *
 * @package Papi
 * @since 1.0.0
 */

final class Papi_Admin {

	/**
	 * Load page type or not?
	 *
	 * @var bool
	 * @since 1.0.0
	 */

	private $load_page_type = false;

	/**
	 * The page type.
	 *
	 * @var Papi_Page_Type
	 * @since 1.0.0
	 */

	private $page_type;

	/**
	 * The page type id.
	 *
	 * @var string
	 * @since 1.0.0
	 */

	private $page_type_id;

	/**
	 * The post type.
	 *
	 * @var string|bool
	 * @since 1.0.0
	 */

	private $post_type;

	/**
	 * Thew view instance.
	 *
	 * @var Papi_Admin_View
	 * @since 1.0.0
	 */

	private $view;

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
	 * @return object
	 */

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new static;
			self::$instance->setup_globals();
			self::$instance->setup_actions();
			self::$instance->setup_filters();

			if ( ! self::$instance->load_page_type ) {
				return;
			}
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
	 * @since 1.0.0
	 */

	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'papi' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */

	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'papi' ), '1.0.0' );
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

		if ( ! $this->setup_papi() ) {
			return;
		}

		$this->page_type->setup();

		$this->override_labels( $this->page_type );
	}

	/**
	 * Fill labels on admin bar.
	 *
	 * @since 1.3.0
	 */

	public function admin_bar_menu() {
		$page = papi_get_page();

		if ( empty( $page ) ) {
			return;
		}

		$page_type = $page->get_page_type();

		$this->override_labels( $page_type );
	}

	/**
	 * Build up the sub menu for post types.
	 *
	 * @since 1.0.0
	 */

	public function admin_menu() {
		global $submenu;

		$post_types = papi_get_post_types();

		foreach ( $post_types as $post_type ) {

			if ( ! post_type_exists( $post_type ) ) {
				continue;
			}

			if ( $post_type === 'post' ) {
				$edit_url = 'edit.php';
			} else {
				$edit_url = 'edit.php?post_type=' . $post_type;
			}

			if ( ! isset( $submenu[$edit_url] ) || ! isset( $submenu[$edit_url][10] ) || ! isset( $submenu[$edit_url][10][2] ) ) {
				continue;
			}

			$only_page_type = papi_filter_settings_only_page_type( $post_type );

			if ( ! empty( $only_page_type ) ) {
				$submenu[$edit_url][10][2] = papi_get_page_new_url( $only_page_type, false, $post_type, [ 'action', 'message', 'page_type', 'post', 'post_type' ] );
			} else {
				$page = 'papi-add-new-page,' . $post_type;

				if ( strpos( $edit_url, 'post_type' ) === false ) {
					$start = '?';
				} else {
					$start = '&';
				}

				$submenu[$edit_url][10][2] = $edit_url . $start . 'page=' . $page;

				// Hidden menu item.
				add_submenu_page( null, __( 'Add New', 'papi' ), __( 'Add New', 'papi' ), 'read', $page, [ $this, 'render_view' ] );
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
		wp_enqueue_style( 'papi-main', dirname( PAPI_PLUGIN_URL ) . '/dist/css/style.min.css', false, null );
	}

	/**
	 * Enqueue script into admin footer.
	 *
	 * @since 1.0.0
	 */

	public function admin_enqueue_scripts() {
		// WordPress will override window.papi on plugins page,
		// so don't include Papi JavaScript on plugins page.
		if ( strpos( $_SERVER['REQUEST_URI'], 'plugins.php' ) !== false ) {
			return;
		}

		wp_enqueue_script( 'papi-main', dirname( PAPI_PLUGIN_URL ) . '/dist/js/main.min.js', [
			'json2',
			'jquery',
			'jquery-ui-core',
			'jquery-ui-sortable',
			'wp-color-picker'
		], '', true );

		wp_localize_script( 'papi-main', 'papiL10n', [
			'remove'        => __( 'Remove', 'papi' ),
			'requiredError' => __( 'This fields are required:', 'papi' ),
		] );
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
		$post_type = papi_get_wp_post_type();

		if ( ! in_array( $post_type, papi_get_post_types() ) ) {
			return $classes;
		}

		if ( count( get_page_templates() ) ) {
			$classes .= 'papi-hide-cpt';
		}

		return $classes;
	}

	/**
	 * Output Papi page type hidden field.
	 *
	 * @since 1.2.1
	 */

	public function edit_form_after_title() {
		wp_nonce_field( 'papi_save_data', 'papi_meta_nonce' );
		?>
		<input type="hidden" name="<?php esc_attr_e( PAPI_PAGE_TYPE_KEY ); ?>" value="<?php esc_attr_e( papi_get_page_type_meta_value() ); ?>"/>
		<?php
	}

	/**
	 * Output hidden meta boxes.
	 *
	 * @since 1.3.0
	 */

	public function hidden_meta_boxes() {
		global $_wp_post_type_features;
		if ( ! isset( $_wp_post_type_features[$this->post_type]['editor'] ) ) {
			add_meta_box( 'papi-hidden-editor', 'Papi hidden editor', [ $this, 'hidden_meta_box_editor' ], $this->post_type );
		}
	}

	/**
	 * Output hidden WordPress editor.
	 *
	 * @since 1.3.0
	 */

	public function hidden_meta_box_editor() {
		wp_editor( '', 'papiHiddenEditor' );
	}

	/**
	 * Load post new action
	 * Redirect to right url if no page type is set.
	 *
	 * @since 1.0.0
	 */

	public function load_post_new() {
		$request_uri = $_SERVER['REQUEST_URI'];
		$post_types = papi_get_post_types();
		$post_type  = papi_get_wp_post_type();

		if ( in_array( $post_type, $post_types ) && strpos( $request_uri, 'page_type=' ) === false && strpos( $request_uri, 'papi-bypass=true' ) === false ) {
			$parsed_url = parse_url( $request_uri );

			$only_page_type = papi_filter_settings_only_page_type( $post_type );

			// Check if we should show one post type or not and create the right url for that.
			if ( ! empty( $only_page_type ) ) {
				$url = papi_get_page_new_url( $only_page_type, false );
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
			$page_type = papi_get_file_data( $post_id );
			if ( ! is_null( $page_type ) ) {
				esc_html_e( $page_type->name );
			} else {
				esc_html_e( papi_filter_standard_page_name( papi_get_wp_post_type() ) );
			}
		}
	}

	/**
	 * Override labels with labels from the page type.
	 *
	 * @param Papi_Page_Type $page_type
	 * @since 1.3.0
	 */

	private function override_labels( $page_type ) {
		global $wp_post_types;

		if ( empty( $page_type ) ) {
			return;
		}

		$post_type = papi_get_wp_post_type();

		if ( empty( $post_type ) || ! isset( $wp_post_types[$post_type] ) ) {
			return;
		}

		foreach ( $page_type->get_labels() as $key => $value ) {
			if ( ! isset( $wp_post_types[$post_type]->labels->$key ) || empty( $value ) ) {
				continue;
			}

			$wp_post_types[$post_type]->labels->$key = $value;
		}
	}

	/**
	 * Menu callback that loads right view depending on what the `page` query string says.
	 *
	 * @since 1.0.0
	 */

	public function render_view() {
		if ( strpos( papi_get_qs( 'page' ), 'papi' ) !== false ) {
			$page = str_replace( 'papi-', '', papi_get_qs( 'page' ) );
			$res = preg_replace( '/\,.*/', '', $page );

			if ( is_string( $res ) ) {
				$page_view = $res;
			}
		}

		if ( ! isset( $page_view ) ) {
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

		$post_types = papi_get_post_types();

		if ( in_array( $typenow, $post_types ) ) {
			$page_types = papi_get_all_page_types( false, $typenow );

			$page_types = array_map( function ( $page_type ) {
				return [
					'name' => $page_type->name,
					'value' => $page_type->get_id()
				];
			}, $page_types );

			// Add the standard page that isn't a real page type.
			$page_types[] = [
				'name' => papi_filter_standard_page_name( papi_get_wp_post_type() ),
				'value' => 'papi-standard-page'
			];

			usort( $page_types, function ( $a, $b ) {
				return strcmp( strtolower( $a['name'] ), strtolower( $b['name'] ) );
			} );

			?>
			<select name="page_type" class="postform">
				<option value="0" selected><?php _e( 'Show all page types', 'papi' ); ?></option>
				<?php
				foreach ( $page_types as $page_type ) {
					printf( '<option value="%s" %s>%s</option>', $page_type['value'], ( papi_get_qs( 'page_type' ) === $page_type['value'] ? ' selected' : '' ), $page_type['name'] );
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

		if ( $pagenow === 'edit.php' && ! is_null( papi_get_qs( 'page_type' ) ) ) {
			if ( papi_get_qs( 'page_type' ) === 'papi-standard-page' ) {
				$query->set( 'meta_query', [
					[
						'key' => PAPI_PAGE_TYPE_KEY,
						'compare' => 'NOT EXISTS'
					]
				] );
			} else {
				$query->set( 'meta_key', PAPI_PAGE_TYPE_KEY );
				$query->set( 'meta_value', papi_get_qs( 'page_type' ) );
			}
		}

		return $query;
	}

	/**
	 * Setup actions.
	 *
	 * @since 1.0.0
	 */

	private function setup_actions() {
		if ( is_admin() ) {
			add_action( 'admin_init', [$this, 'admin_init'] );
			add_action( 'admin_menu', [$this, 'admin_menu'] );
			add_action( 'admin_head', [$this, 'admin_head'] );
			add_action( 'edit_form_after_title', [$this, 'edit_form_after_title'] );
			add_action( 'admin_enqueue_scripts', [$this, 'admin_enqueue_scripts'], 9 );
			add_action( 'load-post-new.php', [$this, 'load_post_new'] );
			add_action( 'restrict_manage_posts', [ $this, 'restrict_page_types'] );
			add_action( 'add_meta_boxes', [$this, 'hidden_meta_boxes'], 10 );
		} else {
			add_action( 'admin_bar_menu', [$this, 'admin_bar_menu'] );
		}
	}

	/**
	 * Setup filters.
	 *
	 * @since 1.0.0
	 */

	private function setup_filters() {
		if ( is_admin() ) {
			add_filter( 'admin_body_class', [$this, 'admin_body_class'] );
			add_filter( 'pre_get_posts', [$this, 'pre_get_posts'] );

			// Add post type columns to eavery post types that is used.
			add_filter( 'manage_' . $this->post_type . '_posts_columns', [$this, 'manage_page_type_posts_columns'] );
			add_action( 'manage_' . $this->post_type . '_posts_custom_column', [
				$this,
				'manage_page_type_posts_custom_column'
			], 10, 2 );
		}
	}

	/**
	 * Setup globals.
	 *
	 * @since 1.0.0
	 */

	private function setup_globals() {
		$this->post_type = papi_get_wp_post_type();

		if ( is_admin() ) {
			$this->view          = new Papi_Admin_View;
			$this->page_type_id  = papi_get_page_type_meta_value();
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
		if ( in_array( $this->post_type, ['revision', 'nav_menu_item'] ) ) {
			return false;
		}

		if ( empty( $this->page_type_id ) ) {
			// If only page type is used, override the page type value.
			$this->page_type_id = papi_filter_settings_only_page_type( $this->post_type );

			if ( empty( $this->page_type_id ) ) {
				// Load page types that don't have any real post type.
				$this->page_type_id = str_replace( 'papi/', '', papi_get_qs( 'page' ) );
			}
		}

		if ( empty( $this->page_type_id ) ) {
			return false;
		}

		$this->page_type = papi_get_page_type_by_id( $this->page_type_id );

		// Do a last check so we can be sure that we have a page type object.
		return ! empty( $this->page_type ) && is_object( $this->page_type );
	}
}

Papi_Admin::instance();
